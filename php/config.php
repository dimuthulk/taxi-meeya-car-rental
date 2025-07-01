<?php
    $host = 'localhost:3307';
    $dbname = 'taxi_meeya';
    $username = "root";
    $password = "";

    // Create connection with proper error handling
    $conn = new mysqli($host, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        // Log error (in production, don't expose database details)
        error_log("Database connection failed: " . $conn->connect_error);
        die("Connection failed. Please try again later.");
    }
    
    // Set charset to prevent character encoding issues
    $conn->set_charset("utf8");
?>