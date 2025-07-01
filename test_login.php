<?php
// test_login.php - Test script to verify login functionality
include_once 'php/session_check.php';

echo "<h2>Login System Test</h2>";

// Check if user is logged in
if (isLoggedIn()) {
    $userData = getUserData();
    echo "<p style='color: green;'>✓ User is logged in!</p>";
    echo "<h3>User Data:</h3>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> " . $userData['id'] . "</li>";
    echo "<li><strong>Name:</strong> " . $userData['name'] . "</li>";
    echo "<li><strong>Email:</strong> " . $userData['email'] . "</li>";
    echo "</ul>";
    echo "<a href='php/auth/logout.php' style='color: red;'>Logout</a>";
} else {
    echo "<p style='color: red;'>✗ User is not logged in</p>";
    echo "<a href='login.php' style='color: blue;'>Go to Login</a>";
}

// Display session data for debugging
echo "<h3>Session Data (Debug):</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
?>
