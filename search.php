<?php
session_start();
require_once './db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: login.php");
    exit;
}

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';
$products = [];

if ($searchTerm !== '') {
    $city = $_SESSION['city'];
    $district = $_SESSION['district'];
    $today = date('Y-m-d');

    $stmt = $db->prepare("
        SELECT p.*, u.city, u.district 
        FROM products p
        JOIN users u ON p.market_id = u.id
        WHERE u.city = ? 
        AND p.expiration_date >= ? 
        AND p.title LIKE ? 
        ORDER BY (u.district = ?) DESC
        LIMIT 8
    ");
    $keyword = '%' . $searchTerm . '%';
    $stmt->execute([$city, $today, $keyword, $district]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
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
            margin-bottom: 20px;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        input[type="text"] {
            width: 60%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }
        .product-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background-color: #f0f0f0;
        }
        .product-info {
            padding: 15px;
        }
        .product-title {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 10px;
        }
        .product-price {
            margin-bottom: 10px;
        }
        .normal-price {
            color: #888;
            text-decoration: line-through;
        }
        .discounted-price {
            color: #e53935;
            font-weight: bold;
        }
        .product-meta {
            font-size: 0.9em;
            color: #666;
        }
        .add-cart-form {
            padding: 10px 15px;
            border-top: 1px solid #eee;
            background-color: #fafafa;
        }
        .add-cart-form input[type="number"] {
            width: 60px;
            padding: 5px;
        }
        .add-cart-form button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            margin-left: 10px;
            cursor: pointer;
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
    <h2 style="text-align: center;">Search Products</h2>


    <form method="GET">
        <input type="text" name="q" placeholder="Enter product name..." value="<?= htmlspecialchars($searchTerm) ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if ($searchTerm !== ''): ?>
        <h3>Results for "<?= htmlspecialchars($searchTerm) ?>"</h3>
        <?php if (empty($products)): ?>
            <p style="text-align: center;">No products found.</p>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <?php if ($product['image_url']): ?>
                            <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="product-image">
                        <?php else: ?>
                            <div class="product-image" style="display:flex;align-items:center;justify-content:center;color:#aaa;">No Image</div>
                        <?php endif; ?>

                        <div class="product-info">
                            <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
                            <div class="product-price">
                                <?php if ($product['discounted_price']): ?>
                                    <span class="normal-price"><?= $product['normal_price'] ?> TL</span>
                                    <span class="discounted-price"><?= $product['discounted_price'] ?> TL</span>
                                <?php else: ?>
                                    <span class="discounted-price"><?= $product['normal_price'] ?> TL</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-meta">
                                Stock: <?= $product['stock'] ?> <br>
                                Expires: <?= $product['expiration_date'] ?>
                            </div>
                        </div>

                        <form method="POST" action="add_to_cart.php?q=<?= urlencode($searchTerm) ?>" class="add-cart-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="market_id" value="<?= $product['market_id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" required>
                            <button type="submit">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
