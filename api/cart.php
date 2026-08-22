<?php
require_once __DIR__ . '/../app/bootstrap.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$pid    = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$qty    = (int) ($_GET['qty'] ?? $_POST['qty'] ?? 1);

if ($action === 'add')    { App\Cart::add($pid, max(1, $qty)); }
if ($action === 'set')    { App\Cart::set($pid, max(0, $qty)); }
if ($action === 'remove') { App\Cart::remove($pid); }
if ($action === 'clear')  { App\Cart::clear(); }

$items = [];
foreach (App\Cart::items() as $i) {
    $items[] = [
        'id'         => (int) $i['id'],
        'slug'       => $i['slug'],
        'title'      => $i['title'],
        'type'       => $i['type'],
        'unit_price' => (float) $i['unit_price'],
        'cover_url'  => $i['cover_id'] ? App\Content::mediaUrl((int) $i['cover_id']) : null,
        'qty'        => (int) $i['qty'],
        'line_total' => (float) $i['unit_price'] * (int) $i['qty'],
    ];
}
echo json_encode([
    'ok'       => true,
    'items'    => $items,
    'count'    => array_sum(array_column($items, 'qty')),
    'subtotal' => App\Cart::subtotal(),
    'shipping' => App\Cart::shipping(),
    'total'    => App\Cart::total(),
]);
