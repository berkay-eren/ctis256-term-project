<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Home</title>
    <style>
        body {
            background: #e6f4ea;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 420px;
            margin: 60px auto;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 {
            margin-top: 0;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }
        a, button {
            background-color: #4CAF50; /* Tam yeşil */
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: block;
            margin-top: 15px;
            transition: background-color 0.3s ease;
            box-sizing: border-box;
        }
        a:hover, button:hover {
            background-color: #388e3c; /* Koyu yeşil hover */
            text-decoration: none;
        }
        form {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h1>Welcome</h1>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hello, <?= htmlspecialchars($_SESSION['email']) ?>!</p>
            <a href="dashboard.php">Go to Dashboard</a>
            <form method="post" action="logout.php">
                <button type="submit">Logout</button>
            </form>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
