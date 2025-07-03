<?php
/**
 * Simple test endpoint to check if PHP backend is working
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Check if config file exists and database connection works
    if (file_exists('config.php')) {
        require_once 'config.php';
        
        // Test database connection
        $testQuery = $pdo->query("SELECT 1 as test");
        $result = $testQuery->fetch();
        
        echo json_encode([
            'success' => true,
            'message' => 'Backend is working!',
            'database' => 'Connected',
            'php_version' => PHP_VERSION,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Config file not found',
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Backend error: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}
?>
