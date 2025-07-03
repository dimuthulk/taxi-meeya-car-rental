<?php
// login.php
session_start();
include_once '../config.php';
header('Content-Type: application/json');

// Enable error logging for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '../error.log');

// Check if $conn is set and is a valid mysqli connection
if (!isset($conn) || !$conn || !($conn instanceof mysqli)) {
    error_log("Login: Database connection not established.");
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
error_log("Login: Raw input - " . $input);

$data = json_decode($input, true);

// Check if JSON decode was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("Login: JSON decode error - " . json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
    exit;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

error_log("Login: Attempting login for email - " . $email);

// Basic validation
if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Check if user exists and get user data
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, password FROM users WHERE email = ?");
if (!$stmt) {
    error_log("Login: Prepare statement failed - " . $conn->error);
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
    exit;
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_log("Login: No user found for email - " . $email);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    $stmt->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Verify password
if (!password_verify($password, $user['password'])) {
    error_log("Login: Password verification failed for email - " . $email);
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    exit;
}

// Login successful - create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
$_SESSION['logged_in'] = true;

error_log("Login: Successful login for user ID - " . $user['id']);

// Return success response
echo json_encode([
    'success' => true, 
    'message' => 'Login successful!',
    'user' => [
        'id' => $user['id'],
        'name' => $user['first_name'] . ' ' . $user['last_name'],
        'email' => $user['email']
    ],
    'redirect' => 'http://localhost/taxi-meeya-website/index.html'
]);

$conn->close();
?>
