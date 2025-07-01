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
if (empty($data['vehicle_id']) || empty($data['pickup_date']) || empty($data['dropoff_date']) || 
    empty($data['pickup_location']) || empty($data['dropoff_location'])) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$conn = getDBConnection();

// Check vehicle availability
$stmt = $conn->prepare("SELECT price_per_day FROM vehicles WHERE id = ? AND status = 'available'");
$stmt->bind_param("i", $data['vehicle_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Vehicle not available']);
    exit;
}

$vehicle = $result->fetch_assoc();
$stmt->close();

// Check availability for dates
$stmt = $conn->prepare("SELECT id FROM bookings 
                       WHERE vehicle_id = ? 
                       AND status IN ('confirmed', 'pending')
                       AND (
                           (pickup_date BETWEEN ? AND ?) OR 
                           (dropoff_date BETWEEN ? AND ?) OR 
                           (pickup_date <= ? AND dropoff_date >= ?)
                       )");
$stmt->bind_param("issssss", $data['vehicle_id'], $data['pickup_date'], $data['dropoff_date'], 
                  $data['pickup_date'], $data['dropoff_date'], $data['pickup_date'], $data['dropoff_date']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Vehicle not available for selected dates']);
    exit;
}
$stmt->close();

// Calculate total price
$pickup = new DateTime($data['pickup_date']);
$dropoff = new DateTime($data['dropoff_date']);
$days = $dropoff->diff($pickup)->days + 1;
$totalPrice = $days * $vehicle['price_per_day'];

// Create booking
$bookingNumber = 'TM' . strtoupper(uniqid());
$stmt = $conn->prepare("INSERT INTO bookings 
                       (booking_number, user_id, vehicle_id, pickup_date, dropoff_date, 
                       pickup_location, dropoff_location, total_price, status) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')");
$stmt->bind_param("siissssd", $bookingNumber, $userId, $data['vehicle_id'], 
                  $data['pickup_date'], $data['dropoff_date'], $data['pickup_location'], 
                  $data['dropoff_location'], $totalPrice);

if ($stmt->execute()) {
    $bookingId = $conn->insert_id;
    
    // Add extras if any
    if (!empty($data['extras'])) {
        foreach ($data['extras'] as $extra) {
            $stmt = $conn->prepare("INSERT INTO booking_extras (booking_id, extra_type) VALUES (?, ?)");
            $stmt->bind_param("is", $bookingId, $extra);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Booking created',
        'booking_id' => $bookingNumber
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking failed']);
}

$conn->close();
?>