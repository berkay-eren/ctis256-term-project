<?php
session_start();
require_once './db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['pass'] ?? '';
    $confirm_password = $_POST['confirm_pass'] ?? '';

    if (!$email) {
        $error_message = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error_message = "Email already registered.";
        } else {
            
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $db->prepare("INSERT INTO users (email, pass) VALUES (?, ?)");
            $insert->execute([$email, $hashed_password]);

            header('Location: registration_success.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Register</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #e6f4ea;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 100%;
        max-width: 400px;
        margin: 5em auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
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
        border-radius: 6px;
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
        font-weight: bold;
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
    <h2>Register</h2>

    <?php if ($error_message): ?>
        <div class="error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>" />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email) ?>" />

        <label for="pass">Password</label>
        <input type="password" id="pass" name="pass" required minlength="6" />

        <label for="confirm_pass">Confirm Password</label>
        <input type="password" id="confirm_pass" name="confirm_pass" required minlength="6" />

        <button type="submit">Register</button>
    </form>
</div>
</body>
</html>
