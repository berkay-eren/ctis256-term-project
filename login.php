<?php
session_start();
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['pass'])) {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['pass'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        header('Location: dashboard.php'); 
        exit;
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        input, button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }
        input:focus, button:focus {
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
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if (isset($error_message)): ?>
        <div class="error"><?= $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="pass">Password:</label>
        <input type="password" name="pass" id="pass" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
