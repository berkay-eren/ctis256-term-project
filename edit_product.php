<?php
session_start();
require_once './db.php';

// Check if user is logged in and is a market user
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'market') {
    header('Location: login.php');
    exit;
}

$market_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Check if product ID is provided
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$product_id = $_GET['id'];

// Fetch product data
$stmt = $db->prepare("SELECT * FROM products WHERE market_id = ? AND id = ?");
$stmt->execute([$market_id, $product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $normal_price = floatval($_POST['normal_price']);
    $discounted_price = !empty($_POST['discounted_price']) ? floatval($_POST['discounted_price']) : null;
    $stock = intval($_POST['stock']);
    $expiration_date = $_POST['expiration_date'];
    
    // Handle file upload
    $image_url = $product['image_url']; // Keep existing image by default
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = $market_id . '_' . $product_id . '_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($product['image_url'] && file_exists($product['image_url'])) {
                    unlink($product['image_url']);
                }
                $image_url = $upload_path;
            } else {
                $error_message = "Failed to upload image.";
            }
        } else {
            $error_message = "Invalid file type. Allowed types: " . implode(', ', $allowed_extensions);
        }
    }

    if (empty($error_message)) {
        try {
            $stmt = $db->prepare("UPDATE products SET title = ?, normal_price = ?, discounted_price = ?, stock = ?, image_url = ?, expiration_date = ? WHERE market_id = ? AND id = ?");
            $stmt->execute([$title, $normal_price, $discounted_price, $stock, $image_url, $expiration_date, $market_id, $product_id]);
            $success_message = "Product updated successfully!";
            
            // Refresh product data
            $stmt = $db->prepare("SELECT * FROM products WHERE market_id = ? AND id = ?");
            $stmt->execute([$market_id, $product_id]);
            $product = $stmt->fetch();
        } catch (PDOException $e) {
            $error_message = "Error updating product: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
        }
        .back-link:hover {
            color: #333;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }
        .current-image {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Product</h2>
        
        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Product Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="normal_price">Normal Price:</label>
                <input type="number" id="normal_price" name="normal_price" step="0.01" min="0" value="<?php echo $product['normal_price']; ?>" required>
            </div>

            <div class="form-group">
                <label for="discounted_price">Discounted Price (Optional):</label>
                <input type="number" id="discounted_price" name="discounted_price" step="0.01" min="0" value="<?php echo $product['discounted_price']; ?>">
            </div>

            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
            </div>

            <div class="form-group">
                <label for="expiration_date">Expiration Date:</label>
                <input type="date" id="expiration_date" name="expiration_date" value="<?php echo $product['expiration_date']; ?>" required>
            </div>

            <div class="form-group">
                <label>Current Image:</label>
                <?php if ($product['image_url']): ?>
                    <div class="current-image">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current product image" class="preview-image">
                    </div>
                <?php else: ?>
                    <p>No image uploaded</p>
                <?php endif; ?>
                
                <label for="product_image">Change Image (Optional):</label>
                <input type="file" id="product_image" name="product_image" accept="image/*" onchange="previewImage(this)">
                <img id="preview" class="preview-image" style="display: none;">
            </div>

            <button type="submit" class="btn">Update Product</button>
        </form>

        <a href="dashboard.php" class="back-link">Back to Dashboard</a>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>