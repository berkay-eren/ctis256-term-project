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
            background: #e6f4ea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 700;
            color: #4CAF50;
            font-size: 2rem;
        }

        .back-button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 30px;
            margin-bottom: 20px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            user-select: none;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background-color: #388e3c;
        }

        .cart-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            background-color: #d0f0c0;
        }

        .cart-info {
            flex: 1;
            margin-left: 20px;
        }

        .cart-info h4 {
            margin: 0 0 5px;
            font-weight: 600;
            font-size: 1.2rem;
            color: #2e7d32;
        }

        .cart-info p {
            margin: 3px 0;
            font-size: 1rem;
        }

        .actions form {
            display: inline;
        }

        .actions button {
            background-color: #d32f2f;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
            user-select: none;
        }

        .actions button:hover {
            background-color: #a12727;
        }

        .total {
            text-align: right;
            font-size: 1.3rem;
            font-weight: 700;
            margin-top: 25px;
            color: #388e3c;
        }

        .purchase-btn {
            margin-top: 25px;
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 14px 40px;
            font-size: 1.1rem;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 700;
            float: right;
            user-select: none;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .purchase-btn:hover {
            background-color: #388e3c;
            box-shadow: 0 6px 12px rgba(56, 142, 60, 0.5);
        }

        .empty {
            text-align: center;
            padding: 50px;
            color: #777;
            font-size: 1.2rem;
        }

        input[type="number"] {
            width: 60px;
            padding: 6px 10px;
            font-size: 1rem;
            border-radius: 6px;
            border: 1px solid #ccc;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus {
            border-color: #4CAF50;
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
