<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];
$user_type = $_SESSION['user_type'];

require_once './db.php';

$products = [];

if ($user_type === 'market') {
    $stmt = $db->prepare("SELECT * FROM products WHERE market_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll();
} elseif ($user_type === 'consumer') {
    $stmt = $db->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard</title>
<style>
    body {
        background: #e6f4ea;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        color: #333;
    }
    .container {
        max-width: 900px;
        margin: 20px auto;
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
    .user-info {
        background-color: #f0faf2;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 15px;
        border: 1px solid #a1d8a3;
        color: #333;
    }
    .user-info p {
        margin: 8px 0;
        font-size: 1rem;
        font-weight: 500;
    }
    .user-info strong {
        color: #388e3c;
    }
    .user-actions {
        text-align: center;
        margin-bottom: 30px;
    }
    .user-actions .action-btn {
        display: inline-block;
        margin: 0 10px;
        padding: 10px 25px;
        background-color: #4CAF50;
        color: white;
        border-radius: 30px;
        font-weight: 600;
        font-size: 1rem;
        text-decoration: none;
        user-select: none;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }
    .user-actions .action-btn:hover {
        background-color: #388e3c;
        box-shadow: 0 6px 12px rgba(56, 142, 60, 0.5);
    }
    .product-list h2 {
        font-size: 1.6rem;
        margin-bottom: 20px;
        color: #333;
    }
    .no-products {
        background-color: #fff7e6;
        border: 1px solid #ffecb3;
        padding: 30px 15px;
        border-radius: 10px;
        color: #555;
        font-weight: 600;
        text-align: center;
        font-size: 1.1rem;
    }
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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
        align-items: center;
        width: 250px;
    }
    .product-card:hover {
        box-shadow: 0 10px 25px rgba(76, 175, 80, 0.4);
    }
    .product-image {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 15px;
        background-color: #d0f0c0;
    }
    .product-info {
        flex-grow: 1;
    }
    form {
        display: flex;
        flex-direction: column;
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
    .stock-info,
    .expiration-date {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 6px;
    }
    .product-card.expired {
        background-color: #fff0f0;
        border-color: #d32f2f;
        color: #7b0000;
    }
    .product-card.expired .product-image {
        filter: grayscale(90%);
    }
    .product-card.expired::before {
        content: "EXPIRED";
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #d32f2f;
        color: white;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 10px;
        border-radius: 20px;
        box-shadow: 0 0 6px #d32f2faa;
    }
    .product-actions {
        margin-top: 15px;
        display: flex;
        gap: 10px;
        justify-content: space-between;
    }
    .product-actions a {
        flex: 1;
        text-align: center;
        padding: 10px 0;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        color: white;
        user-select: none;
        transition: background-color 0.3s ease;
    }
    .edit-btn {
        background-color: #4CAF50;
        width: 100px;
    }
    .edit-btn:hover {
        background-color: #388e3c;
    }
    .delete-btn {
        background-color: #d32f2f;
        width: 100px;
    }
    .delete-btn:hover {
        background-color: #a12727;
    }
    .add-to-cart-btn {
        background-color: #2196F3;
        color: white;
        border: none;
        padding: 10px 0;
        width: 150px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s ease;
        user-select: none;
        margin-top: 10px;
    }
    .add-to-cart-btn:hover {
        background-color: #1769aa;
    }
    .logout-button {
        background-color: #d32f2f;
        border: none;
        color: white;
        padding: 14px 40px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 30px;
        cursor: pointer;
        user-select: none;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        display: block;
        letter-spacing: 1px;
        margin: 40px auto 0 auto;
        text-align: center;
    }
    .logout-button:hover {
        background-color: #a12727;
        box-shadow: 0 6px 12px rgba(161, 39, 39, 0.6);
    }
</style>
</head>
<body>
<?php include 'header.php' ?>
<div class="container">
    <h2>Welcome to Our Market</h2>

    <div class="user-info">
        <p><strong>User ID:</strong> <?= htmlspecialchars($user_id); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user_email); ?></p>
        <p><strong>User Type:</strong> <?= ucfirst(htmlspecialchars($user_type)); ?></p>
    </div>

    <div class="user-actions">
        <?php if ($user_type === 'consumer'): ?>
            <a href="search.php" class="action-btn">Search Products</a>
            <a href="cart.php" class="action-btn">View Cart</a>
            <a href="edit_info.php" class="action-btn">Edit Profile</a>
        <?php elseif ($user_type === 'market'): ?>
            <a href="add_product.php" class="action-btn">Add New Product</a>
            <a href="edit_info.php" class="action-btn">Edit Profile</a>
        <?php endif; ?>
    </div>

    <div class="product-list">
        <h2><?= $user_type === 'market' ? "My Products" : "All Products"; ?></h2>

        <?php if (empty($products)): ?>
            <div class="no-products">
                <?= $user_type === 'market' ? "You haven't added any products yet." : "No products found."; ?>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): 
                    $is_expired = strtotime($product['expiration_date']) < time();
                ?>
                    <div class="product-card <?= $is_expired ? 'expired' : ''; ?>">
                        <?php if ($product['image_url']): ?>
                            <img src="<?= htmlspecialchars($product['image_url']); ?>" alt="<?= htmlspecialchars($product['title']); ?>" class="product-image">
                        <?php else: ?>
                            <div class="product-image" style="background-color: #f0f0f0; display:flex; align-items:center; justify-content:center; color:#999;">No Image</div>
                        <?php endif; ?>

                        <div class="product-info">
                            <div class="product-title"><?= htmlspecialchars($product['title']); ?></div>
                            <div class="product-price">
                                <?php if ($product['discounted_price']): ?>
                                    <span class="normal-price">$<?= number_format($product['normal_price'], 2); ?></span>
                                    <span class="discounted-price">$<?= number_format($product['discounted_price'], 2); ?></span>
                                <?php else: ?>
                                    <span class="discounted-price">$<?= number_format($product['normal_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="stock-info">Stock: <?= htmlspecialchars($product['stock']); ?> units</div>
                            <div class="expiration-date">Expires: <?= date('M d, Y', strtotime($product['expiration_date'])); ?></div>
                        </div>

                        <?php if ($user_type === 'market'): ?>
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?= $product['id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_product.php?id=<?= $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </div>
                        <?php elseif ($user_type === 'consumer'): ?>
                            <form method="POST" action="add_to_cart.php" style="margin-top: 10px;">
                                <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                <input type="hidden" name="market_id" value="<?= $product['market_id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>" required style="width: 100px;">
                                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <form method="POST" action="logout.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <button type="submit" class="logout-button">Log Out</button>
    </form>
</div>
<?php include 'footer.php' ?>
</body>
</html>
