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
        /* CSS buraya aynen gelecek, önceki gönderdiğin stil kodlarını kullanabilirsin */
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
                            <!-- Burada redirect_to olarak tam sayfa ve query gönderiyoruz -->
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
