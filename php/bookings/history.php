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

$query = "SELECT b.id, b.booking_number, b.pickup_date, b.dropoff_date, 
                 b.pickup_location, b.dropoff_location, b.total_price, b.status,
                 v.make AS vehicle_make, v.model AS vehicle_model, v.image AS vehicle_image
          FROM bookings b
          JOIN vehicles v ON b.vehicle_id = v.id
          WHERE b.user_id = ?
          ORDER BY b.pickup_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode(['success' => true, 'bookings' => $bookings]);
$stmt->close();
$conn->close();
?>