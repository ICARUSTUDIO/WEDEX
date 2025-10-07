<?php
// config.php
// Database connection settings

// Turn on error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session on every page that includes this file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Replace with your database username
define('DB_PASS', '');     // Replace with your database password
define('DB_NAME', 'wedex_db'); // Replace with your database name

// Establish a database connection using PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, stop the script and show an error
    die("ERROR: Could not connect. " . $e->getMessage());
}

// Define a base URL for the project
// This helps in creating absolute paths for links and assets
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/');

?>
