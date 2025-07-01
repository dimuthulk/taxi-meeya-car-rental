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

if (empty($data['booking_id'])) {
    echo json_encode(['success' => false, 'message' => 'Booking ID required']);
    exit;
}

$conn = getDBConnection();

// Verify booking belongs to user
$stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' 
                       WHERE id = ? AND user_id = ? AND status IN ('confirmed', 'pending')");
$stmt->bind_param("ii", $data['booking_id'], $userId);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled']);
} else {
    echo json_encode(['success' => false, 'message' => 'Cancellation failed']);
}

$stmt->close();
$conn->close();
?>