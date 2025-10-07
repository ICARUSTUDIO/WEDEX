<?php
session_start();

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wedex_db');

// TinyPNG API Key
define('TINIFY_API_KEY', 'tj6tZ4y9BVL0SfM4LSNmQM2Q42CZS83k'); // <-- PASTE YOUR API KEY HERE

// Create PDO instance
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>

