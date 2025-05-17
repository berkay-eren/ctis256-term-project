<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('Invalid CSRF token');
    }

    session_destroy();
    header('Location: index.php');
    exit;
}
?>
