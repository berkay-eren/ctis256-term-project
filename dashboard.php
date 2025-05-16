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
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 10px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .user-info {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fafafa;
        }
        .user-info p {
            margin: 5px 0;
            font-size: 16px;
        }
        .logout-button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .logout-button:hover {
            background-color: #e53935;
        }
        .market-actions {
            display: flex;
            gap: 50px;
            justify-content: center;
            margin: 20px 0;
        }
        .market-actions a {
            display: inline-block;
            padding: 8px 16px;
            margin: 4px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .market-actions a:hover {
            background-color: #45a049;
        }
        .product-list {
            margin-top: 30px;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }
        
        .product-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        
        .product-info {
            padding: 15px;
        }
        
        .product-title {
            font-size: 1.1em;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }
        
        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .normal-price {
            color: #666;
            text-decoration: line-through;
        }
        
        .discounted-price {
            color: #e53935;
            font-weight: bold;
        }
        
        .stock-info {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        
        .expiration-date {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-top: 1px solid #eee;
        }
        
        .product-actions a {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.2s;
        }
        
        .edit-btn {
            background-color: #2196F3;
            color: white;
        }
        
        .edit-btn:hover {
            background-color: #1976D2;
        }
        
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        
        .add-product-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .add-product-btn:hover {
            background-color: #45a049;
        }
        
        .no-products {
            text-align: center;
            padding: 40px;
            color: #666;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Add styles for expired products */
        .product-card.expired {
            position: relative;
            opacity: 0.7;
        }

        .product-card.expired::before {
            content: "EXPIRED";
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            z-index: 1;
        }

        .product-card.expired .product-image {
            filter: grayscale(100%);
        }

        .product-card.expired .product-info {
            background-color: #f8f8f8;
        }

        .product-card.expired .product-title {
            color: #999;
        }

        .product-card.expired .product-price {
            color: #999;
        }

        .product-card.expired .stock-info,
        .product-card.expired .expiration-date {
            color: #999;
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
