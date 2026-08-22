<?php
require_once __DIR__ . '/../../app/bootstrap.php';
App\Auth::requireRole('admin','editor');
App\Csrf::verifyRequest();
$id = (int) ($_GET['id'] ?? 0);
if ($id) {
    App\Database::delete('products', 'id=:id', ['id' => $id]);
    App\Content::log((int) App\Auth::user()['id'], 'product.delete', 'product', $id);
    App\Flash::set('success', 'Product deleted.');
}
redirect('/admin/products/index.php');
