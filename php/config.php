<?php
/**
 * Database configuration for Taxi Meeya booking system
 */

// Database configuration
$host = 'localhost';
$port = 3307; // Default MySQL port (change to 3307 if needed)
$dbname = 'taxi_meeya';
$username = "root";
$password = "";

try {
    // Create PDO connection
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Uncomment for debugging
    // echo "Database connection successful using PDO";
    
} catch (PDOException $e) {
    // Log error for debugging
    error_log("Database connection failed: " . $e->getMessage());
    
    // Don't expose database details in production
    if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1:80')) {
        die("Database connection failed: " . $e->getMessage());
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Legacy MySQLi connection for backward compatibility
$conn = mysqli_connect($host, $username, $password, $dbname, $port);

if (!$conn) {
    error_log("MySQLi connection failed: " . mysqli_connect_error());
    if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1:80')) {
        die("MySQLi connection failed: " . mysqli_connect_error());
    } else {
        die("Database connection failed. Please try again later.");
    }
}
?>