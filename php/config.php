<?php
    $host = 'localhost:3307';
    $dbname = 'taxi_meeya';
    $username = "root";
    $password = "";

    $conn = mysqli_connect($host, $username, $password, $dbname);

    if (!$conn) {
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Connection failed. Please try again later.");
    } else {
        // echo "Database connection successful.";
    }
?>