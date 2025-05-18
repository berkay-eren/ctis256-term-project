<?php
session_start();
require_once 'db.php';
require_once 'mail.php';

$error = '';
$success = '';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Restore old form data
$old = $_SESSION['register_data'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_code'])) {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $error = "Please enter a valid email.";
    } else {
        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "This email is already registered. Please use another one or log in.";
        } else {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $_SESSION['otp'] = $otp;
            $_SESSION['register_data'] = $_POST;
            $_SESSION['otp_time'] = time();

            if (sendVerificationCode($email, $otp)) {
                $success = "Verification code sent to your email.";
            } else {
                $error = "Failed to send email. Please try again.";
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $entered_otp = $_POST['otp'] ?? '';
    $session_otp = $_SESSION['otp'] ?? '';
    $data = $_SESSION['register_data'] ?? [];

    if (!$data || time() - $_SESSION['otp_time'] > 600) {
        $error = "Verification code expired. Please try again.";
        unset($_SESSION['otp'], $_SESSION['register_data'], $_SESSION['otp_time']);
    } elseif ($entered_otp !== $session_otp) {
        $error = "Incorrect verification code.";
    } else {
        $name = trim($data['name']);
        $city = trim($data['city']);
        $district = trim($data['district']);
        $email = $data['email'];
        $password = $data['password'];
        $user_type = $data['user_type'];
        $verification_code = $session_otp;

        $passHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (name, city, district, email, pass, user_type, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");
        $inserted = $stmt->execute([$name, $city, $district, $email, $passHash, $user_type, $verification_code]);

        if ($inserted) {
            unset($_SESSION['otp'], $_SESSION['register_data'], $_SESSION['otp_time']);
            header("Location: login.php");
            exit;
        } else {
            $error = "Failed to register. Try again.";
        }
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
            font-family: Arial;
            background: #e6f4ea;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .container {
            max-width: 450px;
            margin: 2em auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 15px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            text-align: center;
            margin: 10px 0;
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Register</h2>

        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['otp'])): ?>
            <form method="POST">
                <label for="name">Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>

                <label for="city">City</label>
                <input type="text" name="city" value="<?= htmlspecialchars($old['city'] ?? '') ?>" required>

                <label for="district">District</label>
                <input type="text" name="district" value="<?= htmlspecialchars($old['district'] ?? '') ?>" required>

                <label for="email">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>

                <label for="password">Password</label>
                <input type="password" name="password" required minlength="6">

                <label for="user_type">User Type</label>
                <select name="user_type" required>
                    <option value="">Select</option>
                    <option value="consumer" <?= (isset($old['user_type']) && $old['user_type'] === 'consumer') ? 'selected' : '' ?>>Consumer</option>
                    <option value="market" <?= (isset($old['user_type']) && $old['user_type'] === 'market') ? 'selected' : '' ?>>Market</option>
                </select>

                <button type="submit" name="send_code">Send Verification Code</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <label for="otp">Enter Verification Code</label>
                <input type="text" name="otp" maxlength="6" pattern="\d{6}" required>

                <button type="submit" name="verify_code">Complete Registration</button>
            </form>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
