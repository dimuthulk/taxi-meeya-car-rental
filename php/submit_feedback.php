<?php
include("config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $nic = mysqli_real_escape_string($conn, $_POST['nic']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);

    $sql = "INSERT INTO feedback (name, email, nic, rating, comments)
            VALUES ('$name', '$email', '$nic', '$rating', '$comments')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Thank you for your feedback!'); window.location.href='contact-us.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>
