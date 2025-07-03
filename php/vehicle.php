<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

//Include database configuration
require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    $error = [
        'success' => false,
        'message' => 'Database connection failed',
        'error_code' => $e->getCode(),
        'error_message' => $e->getMessage()
    ];
    echo json_encode($error);
    exit();
}

// Process form data
$vehicle_number = $_POST['vehicle_number'] ?? '';
$make = $_POST['make'] ?? '';
$model = $_POST['model'] ?? '';
$type = $_POST['type'] ?? '';
$seats = $_POST['seats'] ?? 0;
$price_per_day = $_POST['price_per_day'] ?? 0;
$status = $_POST['status'] ?? 'available';
$image = 'default-car.svg'; // Default image

// Validate input
if (empty($vehicle_number) || empty($make) || empty($model) || empty($type) || $seats < 2 || $price_per_day <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

// Handle file upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/uploads/'; 
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $fileName = uniqid('vehicles_') . '.' . $fileExt;
    $targetPath = $uploadDir . $fileName;

    // Check if image file is an actual image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check !== false) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
            exit();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        exit();
    }
}

// Check for duplicate vehicle number
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM vehicles WHERE vehicle_number = :vehicle_number");
$checkStmt->bindParam(':vehicle_number', $vehicle_number);
$checkStmt->execute();
if ($checkStmt->fetchColumn() > 0) {
    echo json_encode(['success' => false, 'message' => 'Vehicle number already exists. Please enter a unique vehicle number.']);
    exit();
}

// Insert into database
try {
    $stmt = $pdo->prepare("INSERT INTO vehicles (vehicle_number, make, model, type, seats, price_per_day, image, status) 
                           VALUES (:vehicle_number, :make, :model, :type, :seats, :price_per_day, :image, :status)");
    $stmt->bindParam(':vehicle_number', $vehicle_number);
    $stmt->bindParam(':make', $make);
    $stmt->bindParam(':model', $model);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':seats', $seats, PDO::PARAM_INT);
    $stmt->bindParam(':price_per_day', $price_per_day);
    $stmt->bindParam(':image', $image);
    $stmt->bindParam(':status', $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Vehicle added successfully!']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Failed to add vehicle.', 'error' => $errorInfo]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>