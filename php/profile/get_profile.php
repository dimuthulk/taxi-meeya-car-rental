<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

require_once '../../config.php';

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT first_name, last_name, email, phone, 
                               drivers_license, license_expiry
                        FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$profile = $result->fetch_assoc();
echo json_encode(['success' => true, 'profile' => $profile]);

$stmt->close();
$conn->close();
?>