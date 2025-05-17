<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e6f4ea; /* register ile aynı arka plan */
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 420px;
            width: 100%;
            text-align: center;
        }
        h2 {
            color: #28a745; /* yeşil başarılı mesaj rengi */
            margin-bottom: 20px;
        }
        p {
            color: #333;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎉 Registration Successful</h2>
        <p>You’re now registered. You can go to the home page.</p>
        <form method="post" action="index.php">
            <button type="submit" class="btn">Go to Home</button>
        </form>
    </div>
</body>
</html>
