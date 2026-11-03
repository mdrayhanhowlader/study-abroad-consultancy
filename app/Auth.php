<?php
declare(strict_types=1);

namespace App;

final class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;
        $cfg = require __DIR__ . '/../config/config.php';
        $s = $cfg['session'];
        $https = (!empty($_SERVER['HTTPS']) && !in_array(strtolower((string) $_SERVER['HTTPS']), ['off', '0'], true))
            || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443;
        session_name($s['name']);
        session_set_cookie_params([
            'lifetime' => $s['lifetime'],
            'path'     => $s['cookie_path'],
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => ($cfg['app']['env'] === 'production' && $https),
        ]);
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.gc_maxlifetime', (string) $s['lifetime']);
        session_start();
    }

    public static function attempt(string $email, string $password): array
    {
        $cfg = require __DIR__ . '/../config/config.php';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $maxAttempts    = $cfg['login']['max_attempts'];
        $lockoutMinutes = $cfg['login']['lockout_minutes'];

        $since = date('Y-m-d H:i:s', time() - $lockoutMinutes * 60);
        $fails = (int) Database::fetchValue(
            'SELECT COUNT(*) FROM login_attempts WHERE ip=? AND succeeded=0 AND attempted_at >= ?',
            [$ip, $since]
        );
        if ($fails >= $maxAttempts) {
            return ['ok' => false, 'message' => 'Too many failed attempts. Try again in ' . $lockoutMinutes . ' minutes.'];
        }

        $u = Database::fetchOne('SELECT * FROM users WHERE email=? AND status=\'active\'', [$email]);
        if (!$u || !password_verify($password, $u['password_hash'])) {
            Database::insert('login_attempts', [
                'ip' => $ip, 'email' => $email, 'succeeded' => 0,
            ]);
            return ['ok' => false, 'message' => 'Invalid email or password.'];
        }

        Database::insert('login_attempts', [
            'ip' => $ip, 'email' => $email, 'succeeded' => 1,
        ]);
        Database::update('users', ['last_login_at' => now()], 'id=:id', ['id' => $u['id']]);
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'             => (int) $u['id'],
            'name'           => $u['name'],
            'email'          => $u['email'],
            'role'           => $u['role'],
            'must_change_pw' => (int) $u['must_change_pw'],
        ];
        return ['ok' => true, 'user' => $_SESSION['user']];
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return !empty($_SESSION['user']);
    }

    public static function hasRole(string ...$roles): bool
    {
        $u = self::user();
        if (!$u) return false;
        return in_array($u['role'], $roles, true);
    }

    public static function require(string $loginPath = '/admin/login.php'): void
    {
        if (!self::check()) {
            header('Location: ' . url($loginPath), true, 302);
            exit;
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::require();
        if (!self::hasRole(...$roles)) {
            http_response_code(403);
            echo '<h1>403 — Forbidden</h1><p>You do not have permission to view this page.</p>';
            exit;
        }
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function setPassword(int $userId, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        Database::update('users', [
            'password_hash'  => $hash,
            'must_change_pw' => 0,
        ], 'id=:id', ['id' => $userId]);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}
