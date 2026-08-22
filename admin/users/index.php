<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin');
$adminPage = 'users';
$pageTitle = 'Users';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    App\Csrf::verifyRequest();
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'create') {
            $name  = App\Validation::str((string) ($_POST['name'] ?? ''), 1, 120);
            $email = App\Validation::email((string) ($_POST['email'] ?? ''));
            $role  = in_array($_POST['role'] ?? 'editor', ['admin','editor','viewer'], true) ? $_POST['role'] : 'editor';
            $pw    = (string) ($_POST['password'] ?? '');
            if (strlen($pw) < 8) throw new RuntimeException('Password must be at least 8 characters.');
            if (App\Database::fetchOne('SELECT id FROM users WHERE email=?', [$email])) {
                throw new RuntimeException('A user with this email already exists.');
            }
            $id = App\Database::insert('users', [
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($pw, PASSWORD_BCRYPT, ['cost' => 12]),
                'role'          => $role,
                'status'        => 'active',
                'must_change_pw'=> 1,
            ]);
            App\Content::log((int) App\Auth::user()['id'], 'user.create', 'user', $id);
            App\Flash::set('success', 'User created.');
        } elseif ($action === 'update') {
            $id   = (int) ($_POST['id'] ?? 0);
            $name = App\Validation::str((string) ($_POST['name'] ?? ''), 1, 120);
            $role = in_array($_POST['role'] ?? 'editor', ['admin','editor','viewer'], true) ? $_POST['role'] : 'editor';
            $status = ($_POST['status'] ?? 'active') === 'disabled' ? 'disabled' : 'active';
            if ($id === (int) App\Auth::user()['id'] && $status === 'disabled') {
                throw new RuntimeException('You cannot disable your own account.');
            }
            App\Database::update('users', [
                'name' => $name, 'role' => $role, 'status' => $status,
            ], 'id=:id', ['id' => $id]);
            $newPw = (string) ($_POST['new_password'] ?? '');
            if (strlen($newPw) >= 8) {
                App\Auth::setPassword($id, $newPw);
            }
            App\Content::log((int) App\Auth::user()['id'], 'user.update', 'user', $id);
            App\Flash::set('success', 'User updated.');
        }
    } catch (Throwable $e) {
        App\Flash::set('error', $e->getMessage());
    }
    redirect('/admin/users/index.php');
}

if (isset($_GET['delete'])) {
    App\Csrf::verifyRequest();
    $id = (int) $_GET['delete'];
    if ($id === (int) App\Auth::user()['id']) {
        App\Flash::set('error', 'You cannot delete your own account.');
    } else {
        App\Database::delete('users', 'id=:id', ['id' => $id]);
        App\Content::log((int) App\Auth::user()['id'], 'user.delete', 'user', $id);
        App\Flash::set('success', 'User deleted.');
    }
    redirect('/admin/users/index.php');
}

$rows = App\Database::fetchAll('SELECT id, name, email, role, status, must_change_pw, last_login_at, created_at FROM users ORDER BY id');
$edit = isset($_GET['edit']) ? App\Database::fetchOne('SELECT * FROM users WHERE id=?', [(int) $_GET['edit']]) : null;

require __DIR__ . '/../_layout/head.php';
require __DIR__ . '/../_layout/sidebar.php';
?>
<div class="flex-1 flex flex-col min-w-0">
  <?php require __DIR__ . '/../_layout/topbar.php'; ?>
  <main class="flex-1 p-4 lg:p-8 bg-gray-50 space-y-6">
    <?= App\Flash::render() ?>

    <?php if ($edit): ?>
      <section class="bg-white rounded-xl border border-gray-200 p-5 max-w-2xl">
        <h2 class="font-semibold text-ink mb-4">Edit user #<?= (int) $edit['id'] ?></h2>
        <form method="post" class="space-y-4">
          <?= App\Csrf::field() ?>
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int) $edit['id'] ?>">
          <div class="grid sm:grid-cols-2 gap-4">
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</span>
              <input name="name" value="<?= h($edit['name']) ?>" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Email (read-only)</span>
              <input value="<?= h($edit['email']) ?>" disabled class="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-md px-4 py-2.5 text-sm text-gray-500">
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Role</span>
              <select name="role" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
                <option value="admin"  <?= $edit['role'] === 'admin'  ? 'selected' : '' ?>>Admin (full access)</option>
                <option value="editor" <?= $edit['role'] === 'editor' ? 'selected' : '' ?>>Editor (content + orders)</option>
                <option value="viewer" <?= $edit['role'] === 'viewer' ? 'selected' : '' ?>>Viewer (read-only)</option>
              </select>
            </label>
            <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Status</span>
              <select name="status" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
                <option value="active"   <?= $edit['status'] === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="disabled" <?= $edit['status'] === 'disabled' ? 'selected' : '' ?>>Disabled</option>
              </select>
            </label>
            <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">New password (optional, min 8 chars)</span>
              <input name="new_password" type="password" minlength="8" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
            </label>
          </div>
          <div class="flex items-center gap-2">
            <button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Save</button>
            <a href="index.php" class="text-sm text-gray-500 hover:text-brand">Cancel</a>
          </div>
        </form>
      </section>
    <?php else: ?>
      <section class="bg-white rounded-xl border border-gray-200 p-5 max-w-2xl">
        <h2 class="font-semibold text-ink mb-4">Add user</h2>
        <form method="post" class="grid sm:grid-cols-2 gap-4">
          <?= App\Csrf::field() ?>
          <input type="hidden" name="action" value="create">
          <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Name</span>
            <input name="name" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</span>
            <input name="email" type="email" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <label class="block"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Role</span>
            <select name="role" class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm bg-white">
              <option value="admin">Admin</option>
              <option value="editor" selected>Editor</option>
              <option value="viewer">Viewer</option>
            </select>
          </label>
          <label class="block sm:col-span-2"><span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Initial password (user must change on first login)</span>
            <input name="password" type="text" minlength="8" required class="mt-1.5 w-full border border-gray-300 rounded-md px-4 py-2.5 text-sm">
          </label>
          <div class="sm:col-span-2"><button class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">Create user</button></div>
        </form>
      </section>
    <?php endif; ?>

    <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-4 border-b border-gray-200"><h2 class="font-semibold text-ink">All users</h2></div>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr><th class="text-left px-5 py-2.5">Name</th><th class="text-left px-3 py-2.5">Email</th><th class="text-left px-3 py-2.5">Role</th><th class="text-left px-3 py-2.5">Status</th><th class="text-left px-3 py-2.5">Last login</th><th class="text-right px-5 py-2.5">Actions</th></tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $u): ?>
              <tr class="border-t border-gray-100">
                <td class="px-5 py-2.5 font-medium"><?= h($u['name']) ?><?php if ($u['must_change_pw']): ?><span class="ml-1 text-xs text-accent">(must change PW)</span><?php endif; ?></td>
                <td class="px-3 py-2.5 text-xs"><?= h($u['email']) ?></td>
                <td class="px-3 py-2.5"><span class="text-xs px-2 py-0.5 rounded-full bg-navy/10 text-navy"><?= h($u['role']) ?></span></td>
                <td class="px-3 py-2.5"><?= $u['status'] === 'active' ? '<span class=text-brand>Active</span>' : '<span class=text-gray-400>Disabled</span>' ?></td>
                <td class="px-3 py-2.5 text-xs text-gray-500"><?= h($u['last_login_at'] ?? '—') ?></td>
                <td class="px-5 py-2.5 text-right text-xs space-x-3">
                  <a href="?edit=<?= (int) $u['id'] ?>" class="text-brand hover:underline">Edit</a>
                  <?php if ((int) $u['id'] !== (int) App\Auth::user()['id']): ?>
                    <a href="?delete=<?= (int) $u['id'] ?>&_csrf=<?= urlencode(App\Csrf::token()) ?>" onclick="return confirm('Delete this user?')" class="text-accent hover:underline">Delete</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>
<?php require __DIR__ . '/../_layout/footer.php'; ?>
