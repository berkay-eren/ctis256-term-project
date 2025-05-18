<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    .header * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    .header {
        background-color: purple;
        color: white;
        text-align: center;
        display: flex;
        padding: 5px 40px;
        justify-content: space-between;
        align-items: center;
    }
    #company-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }
    #company-info img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
    }
    #company-info h1 {
        color: white;
        margin: 0;
        padding: 0;
    }
    #company-slogan h2 {
        color: greenyellow;
        margin: 0;
        padding: 0;
    }
    .header ul {
        list-style-type: none;
        padding: 0;
    }
    .header li {
        display: inline;
        margin: 0 15px;
    }
    .header a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        font-size: 18px;
    }
</style>
<div class="header">
    <div id="company-info">
        <img src="assets/logo.png">
        <h1>E-Shop</h1>
    </div>
    <div id="company-slogan">
        <h2>Your one-stop shop for everything!</h2>
    </div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="">About</a></li>
        <li><a href="">Contact</a></li>
    </ul>
</div>