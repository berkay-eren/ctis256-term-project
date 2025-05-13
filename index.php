<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .container {
            text-align: center;
            padding: 2em;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 1em;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 0.7em 1.5em;
            margin: 1em;
            font-size: 1em;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Welcome</h1>
    <a href="register.php"><button>Register</button></a>
    <a href="login.php"><button>Login</button></a>
</div>

</body>
</html>
