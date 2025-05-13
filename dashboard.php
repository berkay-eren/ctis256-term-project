<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['email'];
$user_type = $_SESSION['user_type'];
?>

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

    <form method="POST" action="logout.php">
        <button type="submit" class="logout-button">Log Out</button>
    </form>
</div>

</body>
</html>
