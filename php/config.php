<?php
<<<<<<< HEAD
    $host = '127.0.0.1:3307';
=======
    $host = 'localhost';
    $port = 3307;
>>>>>>> origin
    $dbname = 'taxi_meeya';
    $username = "root";
    $password = "";

    $conn = mysqli_connect($host, $username, $password, $dbname, $port);

    if (!$conn) {
<<<<<<< HEAD
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Connection failed. Please try again later.");

    }

    else {
        // Uncomment the line below for debugging purposes
        echo "Database connection successful.";
=======
        die("Connection failed. Please try again later.".mysqli_connect_error());
    } else {
        // echo "Database connection successful.";
>>>>>>> origin
    }
?>