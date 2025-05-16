<?php
session_start();
require_once './db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: login.php");
    exit;
}

$consumer_id = $_SESSION['user_id'];

$stmt = $db->prepare("
    SELECT sc.id AS cart_id, sc.quantity, 
        p.title, p.discounted_price, p.image_url, p.stock,
        u.name AS market_name
    FROM shopping_cart sc
    JOIN products p ON sc.market_id = p.market_id AND sc.product_id = p.id
    JOIN users u ON p.market_id = u.id
    WHERE sc.consumer_id = ?
");
$stmt->execute([$consumer_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$grand_total = 0;
foreach ($cart_items as $item) {
    $grand_total += $item['discounted_price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial;
            background-color: #f4f7fc;
            padding: 30px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }
        .cart-info {
            flex: 1;
            margin-left: 20px;
        }
        .cart-info h4 {
            margin: 0 0 5px;
        }
        .actions form {
            display: inline;
        }
        .actions button {
            background-color: #e53935;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        .total {
            text-align: right;
            font-size: 1.2em;
            margin-top: 20px;
        }
        .purchase-btn {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            float: right;
        }
        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        input[type="number"] {
            width: 60px;
            padding: 4px;
            font-size: 14px;
        }

        .back-button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            margin-left: 0;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<div class="container">

    <div style="text-align: left; margin-bottom: 10px;">
        <a href="dashboard.php" class="back-button">← Back to Dashboard</a>
    </div>
    <h2>Your Shopping Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div class="empty">
            <p>Your cart is empty.</p>
        </div>
    <?php else: ?>
        <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="">
                <div class="cart-info">
                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                    <p>Market: <?= htmlspecialchars($item['market_name']) ?></p>
                    <p>
                        Quantity:
                        <input type="number"
                            value="<?= $item['quantity'] ?>"
                            min="1"
                            max="<?= $item['stock'] ?>"
                            onchange="updateQuantity(<?= $item['cart_id'] ?>, this.value)">
                        x <?= $item['discounted_price'] ?> TL
                    </p>
                </div>
                <div class="actions">
                    <form method="POST" action="remove_from_cart.php">
                        <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                        <button type="submit">Remove</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="total">
            <strong>Total: <?= number_format($grand_total, 2) ?> TL</strong>
        </div>

        <form method="POST" action="purchase.php">
            <button class="purchase-btn" type="submit">Purchase</button>
        </form>
    <?php endif; ?>
</div>

<script>
function updateQuantity(cartId, newQuantity) {
    fetch('update_cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `cart_id=${cartId}&quantity=${newQuantity}`
    }).then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert("Failed to update quantity.");
        }
    });
}
</script>
</body>
</html>
