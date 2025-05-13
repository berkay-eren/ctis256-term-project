<?php
session_start();
require_once './mail.php';  
require_once './db.php';    

if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = 1;
}


if ($_SESSION['step'] === 1 && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['user_type'] = $_POST['user_type'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['pass'] = password_hash($_POST['pass'], PASSWORD_DEFAULT);

    $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['verification_code'] = $code;

    
    sendVerificationCode($_SESSION['email'], $code);
    $_SESSION['step'] = 2;  
}


if ($_SESSION['step'] === 2 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $_SESSION['name'] = $_POST['name'];
    $_SESSION['city'] = $_POST['city'];
    $_SESSION['district'] = $_POST['district'];
    $_SESSION['step'] = 3; 
}


if ($_SESSION['step'] === 3 && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verification_code'])) {
    if ($_POST['verification_code'] === $_SESSION['verification_code']) {
        $stmt = $db->prepare("INSERT INTO users (user_type, name, email, pass, city, district, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
            $_SESSION['user_type'],
            $_SESSION['name'],
            $_SESSION['email'],
            $_SESSION['pass'],
            $_SESSION['city'],
            $_SESSION['district'],
            $_SESSION['verification_code']
        ]);
        session_destroy(); 

        echo "
        <div style='text-align:center; padding: 50px;'>
            <h2 style='color: green;'>Registration Complete</h2>
            <p>Your registration was successful. You can now log in.</p>
            <a href='login.php' style='padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;'>Log In</a>
        </div>";
        
    } else {
        echo "<p>Invalid verification code.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 5em auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            font-weight: bold;
            color: #555;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus, select:focus, button:focus {
            outline: none;
            border-color: #4CAF50;
        }
        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <?php if ($_SESSION['step'] === 1): ?>
        <form method="POST">
            <label for="user_type">User Type:</label>
            <select name="user_type" id="user_type" required>
                <option value="consumer">Consumer</option>
                <option value="market">Market</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="pass">Password:</label>
            <input type="password" name="pass" id="pass" required>

            <button type="submit">Next</button>
        </form>
    <?php elseif ($_SESSION['step'] === 2): ?>
        <div class="step-description">Step 2: Enter your personal information</div>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="city">City:</label>
            <input type="text" name="city" id="city" required>

            <label for="district">District:</label>
            <input type="text" name="district" id="district" required>

            <button type="submit">Continue</button>
        </form>
    <?php elseif ($_SESSION['step'] === 3): ?>
        <div class="step-description">Step 3: Enter the 6-digit verification code sent to your email</div>
        <form method="POST">
            <label for="verification_code">Verification Code:</label>
            <input type="text" name="verification_code" id="verification_code" required maxlength="6">

            <button type="submit">Verify & Register</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>

