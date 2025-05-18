<?php
session_start();
require_once './db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consumer_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'] ?? null;
    $market_id = $_POST['market_id'] ?? null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    if ($quantity < 1) $quantity = 1;

    $redirect = 'dashboard.php';

    if (isset($_POST['redirect_to']) && !empty($_POST['redirect_to'])) {
        $redirect_to = $_POST['redirect_to'];
        if (preg_match('/^[a-zA-Z0-9_\-]+\.php(\?.*)?$/', $redirect_to)) {
            $redirect = $redirect_to;
        }
    }

    $stmt = $db->prepare("SELECT stock FROM products WHERE market_id = ? AND id = ?");
    $stmt->execute([$market_id, $product_id]);
    $stock = $stmt->fetchColumn();

    if ($stock === false) {
        die("Product not found.");
    }

    $stmt = $db->prepare("SELECT id, quantity FROM shopping_cart WHERE consumer_id = ? AND market_id = ? AND product_id = ?");
    $stmt->execute([$consumer_id, $market_id, $product_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    $existing_quantity = $existing ? $existing['quantity'] : 0;
    $new_total = $existing_quantity + $quantity;

    if ($new_total > $stock) {
        echo "<script>alert('You cannot add more than available stock.'); window.history.back();</script>";
        exit;
    }

    if ($existing) {
        $stmt = $db->prepare("UPDATE shopping_cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_total, $existing['id']]);
    } else {
        $stmt = $db->prepare("INSERT INTO shopping_cart (consumer_id, market_id, product_id, quantity) VALUES (?, ?, ?, ?)");
        $stmt->execute([$consumer_id, $market_id, $product_id, $quantity]);
    }

    header("Location: $redirect");
    exit;
}
?>
