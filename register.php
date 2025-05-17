<?php
session_start();
require_once 'db.php';
require_once 'mail.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['send_otp'])) {
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $error = "Please enter a valid email.";
        } else {
            $otp = rand(100000, 999999);
            $_SESSION['otp'] = $otp;
            $_SESSION['otp_email'] = $email;
            $_SESSION['otp_time'] = time();

            if (sendVerificationCode($email, $otp)) {
                $_SESSION['otp_sent'] = true;
                $success = "Verification code sent to your email.";
            } else {
                $error = "Failed to send email. Please try again.";
            }
        }
    }

    if (isset($_POST['verify_otp'])) {
        $email = $_SESSION['otp_email'] ?? '';
        $user_otp = $_POST['otp'] ?? '';
        $password = $_POST['pass'] ?? '';
        $user_type = $_POST['user_type'] ?? '';

        if (!isset($_SESSION['otp_time']) || (time() - $_SESSION['otp_time']) > 600) {
            $error = "Verification code expired. Please request a new one.";
            unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_time'], $_SESSION['otp_sent']);
        }

        elseif (!isset($_SESSION['otp']) || $user_otp != $_SESSION['otp']) {
            $error = "Incorrect verification code.";
        }
       
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email address.";
        }
        
        elseif (strlen($password) < 6) {
            $error = "Password must be at least 6 characters.";
        }
       
        elseif (!in_array($user_type, ['consumer', 'market'])) {
            $error = "Please select a valid user type.";
        }
        else {
            $passHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO users (email, pass, user_type, is_verified) VALUES (?, ?, ?, ?)");
            $inserted = $stmt->execute([$email, $passHash, $user_type, 1]);

            if ($inserted) {
                unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['otp_time'], $_SESSION['otp_sent']);
                header("Location: registration_success.php");
                exit;
            } else {
                $error = "Error during registration. Please try again.";
            }
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
    margin: 0; padding: 0;
}
.container {
    max-width: 400px;
    margin: 3em auto;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
label, select, input, button {
    width: 100%;
    margin: 10px 0;
    box-sizing: border-box;
}
input, select {
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
button {
    background-color: #4CAF50;
    border: none;
    padding: 10px;
    color: white;
    font-size: 16px;
    border-radius: 5px;
    cursor: pointer;
}
button:hover {
    background-color: #45a049;
}
.error {
    color: red;
    text-align: center;
}
.success {
    color: green;
    text-align: center;
}
</style>
</head>
<body>

<div class="container">
    <h2>Register</h2>

    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['otp_sent'])): ?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
            <button type="submit" name="send_otp">Send Verification Code</button>
        </form>
    <?php else: ?>
        <form method="POST">
            <label for="otp">Verification Code</label>
            <input type="text" name="otp" id="otp" maxlength="6" pattern="\d{6}" required>

            <label for="pass">Password</label>
            <input type="password" name="pass" id="pass" required>

            <label for="user_type">User Type</label>
            <select name="user_type" id="user_type" required>
                <option value="">Select</option>
                <option value="consumer">Consumer</option>
                <option value="market">Market</option>
            </select>

            <button type="submit" name="verify_otp">Register</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
