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

    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['pass'] ?? '';

    if (!$email) {
        $error_message = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters.";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['pass'])) {
            // Login success: set session and redirect
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'] ?? '';
            $_SESSION['city'] = $user['city'] ?? '';
            $_SESSION['district'] = $user['district'] ?? '';

            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #e6f4ea;
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .container {
        width: 100%;
        max-width: 400px;
        margin: 2em auto;
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
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Login</h2>

        <?php if ($error_message): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />

            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= htmlspecialchars($email) ?>"
                autocomplete="username"
            />

            <label for="pass">Password</label>
            <input
                type="password"
                id="pass"
                name="pass"
                required
                minlength="6"
                autocomplete="current-password"
            />

            <button type="submit">Login</button>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
