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
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    .container {
        max-width: 900px;
        margin: 0 auto;
        background-color: white;
        padding: 25px 30px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    h2 {
        text-align: center;
        margin-bottom: 25px;
        font-weight: 700;
        color: #007bff;
        font-size: 2rem;
    }

    .user-info {
        background-color: #e9ecef;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 25px;
        border: 1px solid #ced4da;
    }

    .user-info p {
        margin: 8px 0;
        font-size: 1rem;
        font-weight: 500;
    }

    .user-info strong {
        color: #0056b3;
    }

    .product-list h2 {
        font-size: 1.6rem;
        margin-bottom: 20px;
        color: #495057;
    }

    .no-products {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        padding: 30px 15px;
        border-radius: 8px;
        color: #856404;
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
        background-color: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 15px;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s ease;
        position: relative;
    }

    .product-card:hover {
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.25);
    }

    .product-image {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 6px;
        margin-bottom: 15px;
        background-color: #dee2e6;
    }

    .product-info {
        flex-grow: 1;
    }

    .product-title {
        font-weight: 600;
        font-size: 1.2rem;
        margin-bottom: 8px;
        color: #212529;
    }

    .product-price {
        margin-bottom: 12px;
        font-weight: 600;
        font-size: 1rem;
        color: #28a745;
    }

    .normal-price {
        text-decoration: line-through;
        color: #6c757d;
        margin-right: 10px;
    }

    .discounted-price {
        color: #dc3545;
        font-weight: 700;
    }

    .stock-info,
    .expiration-date {
        font-size: 0.9rem;
        color: #6c757d;
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
        padding: 8px 0;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        color: white;
        user-select: none;
        transition: background-color 0.3s ease;
    }

    .edit-btn {
        background-color: #007bff;
    }

    .edit-btn:hover {
        background-color: #0056b3;
    }

    .delete-btn {
        background-color: #dc3545;
    }

    .delete-btn:hover {
        background-color: #a71d2a;
    }

    .product-card.expired {
        background-color: #fff3f3;
        border-color: #dc3545;
    }

    .product-card.expired .product-image {
        filter: grayscale(90%);
    }

    .product-card.expired::before {
        content: "SÜRESİ DOLDU";
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #dc3545;
        color: white;
        font-weight: 700;
        font-size: 12px;
        padding: 3px 9px;
        border-radius: 20px;
        box-shadow: 0 0 6px #dc3545aa;
    }

    form.logout-form {
        margin-top: 40px;
        text-align: center;
    }

    .logout-button {
        background-color: #dc3545;
        border: none;
        color: white;
        padding: 14px 35px;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 30px;
        cursor: pointer;
        user-select: none;
        transition: background-color 0.3s ease;
    }

    .logout-button:hover {
        background-color: #a71d2a;
    }
</style>
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
