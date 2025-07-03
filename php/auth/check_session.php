<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['authenticated' => false]);
    exit;
}

// Return user session data
echo json_encode([
    'authenticated' => true,
    'user' => [
        'id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
        'name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '',
        'email' => isset($_SESSION['user_email']) ? $_SESSION['user_email'] : ''
    ]
]);
?>
