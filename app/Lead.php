<?php
declare(strict_types=1);

namespace App;

final class Lead
{
    public static function all(?string $status = null, ?string $source = null, int $limit = 200): array
    {
        $where = [];
        $params = [];
        if ($status) { $where[] = 'l.status=?'; $params[] = $status; }
        if ($source) { $where[] = 'l.source=?'; $params[] = $source; }
        $sql = 'SELECT l.*, u.name AS assigned_name FROM leads l LEFT JOIN users u ON u.id=l.assigned_to';
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY l.id DESC LIMIT ' . (int) $limit;
        return Database::fetchAll($sql, $params);
    }

    public static function find(int $id): ?array
    {
        return Database::fetchOne(
            'SELECT l.*, u.name AS assigned_name FROM leads l LEFT JOIN users u ON u.id=l.assigned_to WHERE l.id=?',
            [$id]
        );
    }

    public static function countByStatus(): array
    {
        $rows = Database::fetchAll(
            'SELECT status, COUNT(*) AS cnt FROM leads GROUP BY status'
        );
        $out = ['new' => 0, 'contacted' => 0, 'qualified' => 0, 'converted' => 0, 'lost' => 0];
        foreach ($rows as $r) { $out[$r['status']] = (int) $r['cnt']; }
        return $out;
    }

    public static function countRecent(int $days = 7): int
    {
        return (int) Database::fetchValue(
            'SELECT COUNT(*) FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)',
            [$days]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('leads', [
            'name'             => $data['name'],
            'phone'            => $data['phone'],
            'email'            => $data['email'] ?? null,
            'destination'      => $data['destination'] ?? null,
            'study_level'      => $data['study_level'] ?? null,
            'service_interest' => $data['service_interest'] ?? null,
            'message'          => $data['message'] ?? null,
            'source'           => $data['source'] ?? 'contact_form',
            'status'           => 'new',
            'fbclid'           => $data['fbclid'] ?? null,
            'gclid'            => $data['gclid'] ?? null,
            'ip'               => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent'       => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255),
        ]);
    }

    public static function updateStatus(int $id, string $status, ?int $assignedTo = null): void
    {
        $data = ['status' => $status];
        if ($assignedTo !== null) $data['assigned_to'] = $assignedTo;
        Database::update('leads', $data, 'id=:id', ['id' => $id]);
        Content::log(null, 'lead.status_change', 'lead', $id, ['to' => $status]);
    }

    public static function assign(int $id, int $userId): void
    {
        Database::update('leads', ['assigned_to' => $userId], 'id=:id', ['id' => $id]);
        Content::log($userId, 'lead.assigned', 'lead', $id, ['to_user' => $userId]);
    }
}
