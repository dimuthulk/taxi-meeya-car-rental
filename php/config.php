<?php
    $host = 'localhost';
    $port = 3306;
    $dbname = 'taxi_meeya';
    $username = "root";
    $password = "";

    $conn = mysqli_connect($host, $username, $password, $dbname, $port);

    if (!$conn) {
        die("Connection failed. Please try again later.".mysqli_connect_error());
    } else {
        // echo "Database connection successful.";
    }
?>