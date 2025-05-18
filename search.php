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
    $city = $_SESSION['city'] ?? '';
    $district = $_SESSION['district'] ?? '';
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
            background: #e6f4ea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 900px;
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
            border-radius: 4px;
            margin-bottom: 15px;
            text-decoration: none;
            font-size: 14px;
            display: inline-block;
            user-select: none;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: #388e3c;
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
            font-size: 1rem;
        }
        button[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        button[type="submit"]:hover {
            background-color: #388e3c;
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .product-card {
            background-color: #f9fff7;
            border-radius: 12px;
            border: 1px solid #a1d8a3;
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: box-shadow 0.2s ease;
            position: relative;
            color: #333;
        }
        .product-card:hover {
            box-shadow: 0 10px 25px rgba(76, 175, 80, 0.4);
        }
        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #d0f0c0;
        }
        .product-info {
            flex-grow: 1;
        }
        .product-title {
            font-weight: 600;
            font-size: 1.2rem;
            margin-bottom: 8px;
            color: #2e7d32;
        }
        .product-price {
            margin-bottom: 12px;
            font-weight: 700;
            font-size: 1rem;
            color: #4CAF50;
        }
        .normal-price {
            text-decoration: line-through;
            color: #7b7b7b;
            margin-right: 10px;
        }
        .discounted-price {
            color: #388e3c;
            font-weight: 700;
        }
        .product-meta {
            font-size: 0.9rem;
            color: #555;
        }
        .add-cart-form {
            padding: 10px 15px;
            border-top: 1px solid #eee;
            background-color: #fafafa;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .add-cart-form input[type="number"] {
            width: 60px;
            padding: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .add-cart-form button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .add-cart-form button:hover {
            background-color: #388e3c;
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

                        <form method="POST" action="add_to_cart.php" class="add-cart-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="market_id" value="<?= $product['market_id'] ?>">
                            <input type="number" name="quantity" value="1" min="1" required>
                            <input type="hidden" name="redirect_to" value="search.php?q=<?= urlencode($searchTerm) ?>">
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
