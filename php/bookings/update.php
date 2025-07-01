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

if (empty($data['booking_id']) || empty($data['pickup_date']) || empty($data['dropoff_date'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$conn = getDBConnection();

// Verify booking belongs to user
$stmt = $conn->prepare("SELECT vehicle_id FROM bookings WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $data['booking_id'], $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

$booking = $result->fetch_assoc();
$stmt->close();

// Check availability
$stmt = $conn->prepare("SELECT id FROM bookings 
                       WHERE vehicle_id = ? 
                       AND id != ?
                       AND status IN ('confirmed', 'pending')
                       AND (
                           (pickup_date BETWEEN ? AND ?) OR 
                           (dropoff_date BETWEEN ? AND ?) OR 
                           (pickup_date <= ? AND dropoff_date >= ?)
                       )");
$stmt->bind_param("iissssss", $booking['vehicle_id'], $data['booking_id'], 
                  $data['pickup_date'], $data['dropoff_date'], 
                  $data['pickup_date'], $data['dropoff_date'], 
                  $data['pickup_date'], $data['dropoff_date']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Vehicle not available']);
    exit;
}
$stmt->close();

// Update booking
$stmt = $conn->prepare("UPDATE bookings 
                       SET pickup_date = ?, dropoff_date = ?, 
                       pickup_location = ?, dropoff_location = ?
                       WHERE id = ?");
$stmt->bind_param("ssssi", $data['pickup_date'], $data['dropoff_date'], 
                  $data['pickup_location'], $data['dropoff_location'], $data['booking_id']);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$stmt->close();
$conn->close();
?>