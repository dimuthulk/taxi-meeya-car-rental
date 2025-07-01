<?php
header('Content-Type: application/json');
require_once '../config.php';

// Simple vehicle search
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check database connection
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }
    
    // Get basic search parameters
    $type = isset($_GET['type']) ? $_GET['type'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    
    // Build simple SQL query
    $query = "SELECT * FROM vehicles WHERE status = 'available'";
    
    // Add type filter if specified
    if ($type) {
        $query .= " AND type = '" . $conn->real_escape_string($type) . "'";
    }
    
    // Add search filter if specified
    if ($search) {
        $query .= " AND (make LIKE '%" . $conn->real_escape_string($search) . "%' 
                    OR model LIKE '%" . $conn->real_escape_string($search) . "%')";
    }
    
    // Order by price (cheapest first)
    $query .= " ORDER BY price_per_day ASC";
    
    // Execute query
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $vehicles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vehicles[] = $row;
        }
        echo json_encode(['success' => true, 'vehicles' => $vehicles]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Search failed']);
    }
    
    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Only GET method allowed']);
}
?>