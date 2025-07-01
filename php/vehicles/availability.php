<?php
header('Content-Type: application/json');
require_once '../../config.php';

if (empty($_GET['vehicle_id']) || empty($_GET['pickup_date']) || empty($_GET['dropoff_date'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$conn = getDBConnection();
$vehicleId = (int)$_GET['vehicle_id'];
$pickupDate = $_GET['pickup_date'];
$dropoffDate = $_GET['dropoff_date'];

$stmt = $conn->prepare("SELECT v.id 
                       FROM vehicles v
                       WHERE v.id = ? 
                       AND v.status = 'available'
                       AND NOT EXISTS (
                           SELECT 1 FROM bookings b
                           WHERE b.vehicle_id = v.id
                           AND b.status IN ('confirmed', 'pending')
                           AND (
                               (b.pickup_date BETWEEN ? AND ?) OR 
                               (b.dropoff_date BETWEEN ? AND ?) OR 
                               (b.pickup_date <= ? AND b.dropoff_date >= ?)
                           )
                       )");
$stmt->bind_param("issssss", $vehicleId, $pickupDate, $dropoffDate, 
                  $pickupDate, $dropoffDate, $pickupDate, $dropoffDate);
$stmt->execute();
$result = $stmt->get_result();

$isAvailable = $result->num_rows > 0;
echo json_encode([
    'success' => true, 
    'available' => $isAvailable,
    'message' => $isAvailable ? 'Available' : 'Not available'
]);

$stmt->close();
$conn->close();
?>