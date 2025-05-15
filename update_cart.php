<?php
session_start();
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'], $_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $new_quantity = max(1, intval($_POST['quantity']));

    $stmt = $db->prepare("
        SELECT sc.product_id, sc.market_id, p.stock
        FROM shopping_cart sc
        JOIN products p ON sc.market_id = p.market_id AND sc.product_id = p.id
        WHERE sc.id = ?
    ");
    $stmt->execute([$cart_id]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        http_response_code(400);
        echo "Cart item not found.";
        exit;
    }

    $stock = intval($item['stock']);

    if ($new_quantity > $stock) {
        http_response_code(400);
        echo "Cannot exceed available stock (" . $stock . ").";
        exit;
    }

    $stmt = $db->prepare("UPDATE shopping_cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$new_quantity, $cart_id]);

    echo "ok";
}
