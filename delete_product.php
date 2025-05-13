<?php
session_start();
require_once './db.php';

// Check if user is logged in and is a market user
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'market') {
    header('Location: login.php');
    exit;
}

$market_id = $_SESSION['user_id'];

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$product_id = $_GET['id'];

try {
    // First, get the product image URL before deleting
    $stmt = $db->prepare("SELECT image_url FROM products WHERE market_id = ? AND id = ?");
    $stmt->execute([$market_id, $product_id]);
    $product = $stmt->fetch();

    if ($product) {
        // Delete the product
        $stmt = $db->prepare("DELETE FROM products WHERE market_id = ? AND id = ?");
        $stmt->execute([$market_id, $product_id]);

        // If product had an image, delete it from the server
        if ($product['image_url'] && file_exists($product['image_url'])) {
            unlink($product['image_url']);
        }

        $_SESSION['success_message'] = "Product deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Product not found.";
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error deleting product: " . $e->getMessage();
}

// Redirect back to dashboard
header('Location: dashboard.php');
exit;
?>
