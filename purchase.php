<?php
session_start();
require_once './db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: login.php");
    exit;
}

$consumer_id = $_SESSION['user_id'];

// 1. get cart items
$stmt = $db->prepare("
    SELECT market_id, product_id, quantity 
    FROM shopping_cart 
    WHERE consumer_id = ?
");
$stmt->execute([$consumer_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. update stock
foreach ($cart_items as $item) {
    $update = $db->prepare("
        UPDATE products 
        SET stock = stock - ? 
        WHERE market_id = ? AND id = ?
    ");
    $update->execute([
        $item['quantity'],
        $item['market_id'],
        $item['product_id']
    ]);
}

// 3. clear cart
$delete = $db->prepare("DELETE FROM shopping_cart WHERE consumer_id = ?");
$delete->execute([$consumer_id]);

header('Location: cart.php');
exit;
