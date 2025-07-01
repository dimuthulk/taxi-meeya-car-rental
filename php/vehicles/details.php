<?php
header('Content-Type: application/json');
require_once '../../config.php';

if (empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Vehicle ID required']);
    exit;
}

$conn = getDBConnection();
$vehicleId = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Vehicle not found']);
    exit;
}

$vehicle = $result->fetch_assoc();

// Get additional images
$stmt = $conn->prepare("SELECT image_path FROM vehicle_images WHERE vehicle_id = ?");
$stmt->bind_param("i", $vehicleId);
$stmt->execute();
$images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$vehicle['additional_images'] = array_column($images, 'image_path');

echo json_encode(['success' => true, 'vehicle' => $vehicle]);

$stmt->close();
$conn->close();
?>