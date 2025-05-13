<?php
$servername = "localhost"; 
$username = "root";       
$password = "";             
$dbname = "test";

try {
    $db = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
} catch (PDOException $e){
    echo "Database Connection Failed: " . $e->getMessage();
    exit;  
}
?>
