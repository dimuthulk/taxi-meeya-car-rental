<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

require_once '../../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'];

// Validate input
if (empty($data['first_name']) || empty($data['last_name']) || empty($data['phone']) || 
    empty($data['drivers_license']) || empty($data['license_expiry'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$conn = getDBConnection();

$stmt = $conn->prepare("UPDATE users SET 
                       first_name = ?, last_name = ?, phone = ?, 
                       drivers_license = ?, license_expiry = ? 
                       WHERE id = ?");
$stmt->bind_param("sssssi", $data['first_name'], $data['last_name'], $data['phone'], 
                  $data['drivers_license'], $data['license_expiry'], $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Profile updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>