<?php
session_start();
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $stmt = $db->prepare("DELETE FROM shopping_cart WHERE id = ?");
    $stmt->execute([$_POST['cart_id']]);
}

header('Location: cart.php');
exit;
