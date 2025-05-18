<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        .footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 30px 40px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .footer-help {
            display: flex;
            flex-direction: column;
            gap: 10px;
            text-align: left;
            margin: 0;
        }
        .footer-help h2 {
            color: #4CAF50;
        }
        .footer-help p {
            margin: 0;
        }
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .footer ul {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .footer li {
            display: inline;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .footer-social {
            display: flex;
            flex-direction: column;
            text-align: right;
            gap: 10px;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
   <div class="footer">
        <div class="footer-help">
            <h2>Contact Us</h2>
            <p>256termproject@gmail.com </p>
        </div>
        <div class="footer-links">
            <h2>Quick Links</h2>
            <ul>
                <li><a href="index.php">Home</a></li> |
                <li><a href="about.php">About Us</a></li> |
                <li><a href="contact.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer-social">
            <p>Follow us on:
                <a href="https://www.facebook.com" target="_blank">Facebook</a> |
                <a href="https://www.twitter.com" target="_blank">Twitter</a> |
                <a href="https://www.instagram.com" target="_blank">Instagram</a>
                <p>&copy; 2023 E-Shop. All rights reserved.</p>
            </p>
        </div>
    </div> 
</body>
</html>