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

// Fetch products for the market user
$products = [];
if ($user_type === 'market') {
    $stmt = $db->prepare("SELECT * FROM products WHERE market_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    $products = $stmt->fetchAll();

?>    
<?php } elseif ($user_type == 'consumer') { ?>
    <div class="market-actions">
        <a href="search.php">Search Products</a>
        <a href="cart.php">View Cart</a>
        <a href="edit_info.php">Edit Profile</a>
    </div>
<?php } ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

.user-info {
    background-color: #f0faf2;
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 25px;
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

.stock-info,
.expiration-date {
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 6px;
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
}

.edit-btn:hover {
    background-color: #388e3c;
}

.delete-btn {
    background-color: #d32f2f;
}

.delete-btn:hover {
    background-color: #a12727;
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
    content: "SÜRESİ DOLDU";
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

/* MARKET ACTIONS */
.market-actions {
    margin: 30px 10px 40px 10px;
    text-align: center;
}

.market-actions a {
    display: inline-block;
    padding: 12px 25px;
    margin: 10px 10px 20px 0;
    border-radius: 30px;
    font-weight: 600;
    font-size: 1rem;
    text-decoration: none;
    color: white;
    user-select: none;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.market-actions a[href="add_product.php"] {
    background-color: #4CAF50;
}

.market-actions a[href="add_product.php"]:hover {
    background-color: #388e3c;
    box-shadow: 0 6px 12px rgba(56, 142, 60, 0.5);
}

.market-actions a[href="edit_info.php"] {
    background-color: #2196F3;
}

.market-actions a[href="edit_info.php"]:hover {
    background-color: #1769aa;
    box-shadow: 0 6px 12px rgba(23, 105, 170, 0.5);
}

/* LOGOUT FORM */
.logout-form {
    width: 200px;
    margin: 40px auto 0 auto;
    text-align: center;
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
}

.logout-button:hover {
    background-color: #a12727;
    box-shadow: 0 6px 12px rgba(161, 39, 39, 0.6);
}

</style>

</head>
<body>

<div class="container">
    <h2>Welcome to Our Market</h2>

    <div class="user-info">
        <p><strong>User ID:</strong> <?= $user_id; ?></p>
        <p><strong>Email:</strong> <?= $user_email; ?></p>
        <p><strong>User Type:</strong> <?= ucfirst($user_type); ?></p>
    </div>

    <?php if ($user_type == 'market') { ?>
        <div class="product-list">
            <h2>My Products</h2>
            
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>You haven't added any products yet.</p>
                    <p>Click the "Add New Product" button to get started!</p>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $product): 
                        $is_expired = strtotime($product['expiration_date']) < time();
                    ?>
                        <div class="product-card <?php echo $is_expired ? 'expired' : ''; ?>">
                            <?php if ($product['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="product-image">
                            <?php else: ?>
                                <div class="product-image" style="background-color: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <span style="color: #999;">No Image</span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-info">
                                <div class="product-title"><?php echo htmlspecialchars($product['title']); ?></div>
                                
                                <div class="product-price">
                                    <?php if ($product['discounted_price']): ?>
                                        <span class="normal-price">$<?php echo number_format($product['normal_price'], 2); ?></span>
                                        <span class="discounted-price">$<?php echo number_format($product['discounted_price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="discounted-price">$<?php echo number_format($product['normal_price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="stock-info">
                                    Stock: <?php echo $product['stock']; ?> units
                                </div>
                                
                                <div class="expiration-date">
                                    Expires: <?php echo date('M d, Y', strtotime($product['expiration_date'])); ?>
                                </div>
                            </div>
                            
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="market-actions">
                <a href="add_product.php">Add New Product</a>
                <a href="edit_info.php">Edit Profile</a>
        </div>
    <?php } ?>

    <form method="POST" action="logout.php">
        <button type="submit" class="logout-button">Log Out</button>
    </form>
</div>

</body>
</html>
