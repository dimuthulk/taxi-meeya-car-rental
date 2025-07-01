<?php
// debug_login.php - Comprehensive login system debugging
echo "<h1>Login System Debugging Tool</h1>";
echo "<style>
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { color: blue; }
    pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
</style>";

// Check PHP version and extensions
echo "<h2>1. PHP Environment Check</h2>";
echo "<p class='info'>PHP Version: " . PHP_VERSION . "</p>";

$required_extensions = ['mysqli', 'json', 'session'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='success'>✓ Extension '$ext' is loaded</p>";
    } else {
        echo "<p class='error'>✗ Extension '$ext' is NOT loaded</p>";
    }
}

// Test database connection
echo "<h2>2. Database Connection Test</h2>";
try {
    include_once 'php/config.php';
    if (isset($conn) && $conn instanceof mysqli) {
        echo "<p class='success'>✓ Database connection successful</p>";
        echo "<p class='info'>Connected to: " . $conn->get_server_info() . "</p>";
        
        // Test users table
        $result = $conn->query("SHOW TABLES LIKE 'users'");
        if ($result && $result->num_rows > 0) {
            echo "<p class='success'>✓ Users table exists</p>";
            
            // Check table structure
            $result = $conn->query("DESCRIBE users");
            echo "<h3>Table Structure:</h3>";
            echo "<table border='1' style='border-collapse:collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Key</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Key']}</td></tr>";
            }
            echo "</table>";
            
            // Count users
            $result = $conn->query("SELECT COUNT(*) as count FROM users");
            $count = $result->fetch_assoc();
            echo "<p class='info'>Total users in database: " . $count['count'] . "</p>";
            
        } else {
            echo "<p class='error'>✗ Users table does not exist</p>";
        }
    } else {
        echo "<p class='error'>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}

// Test session functionality
echo "<h2>3. Session Test</h2>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p class='success'>✓ Session is active</p>";
} else {
    echo "<p class='warning'>! Session not started, starting now...</p>";
    session_start();
}

echo "<p class='info'>Session ID: " . session_id() . "</p>";
echo "<p class='info'>Session status: " . session_status() . " (1=disabled, 2=active)</p>";

// Show current session data
echo "<h3>Current Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Test password hashing
echo "<h2>4. Password Hashing Test</h2>";
$test_password = "test123";
$hashed = password_hash($test_password, PASSWORD_DEFAULT);
echo "<p class='info'>Test password: '$test_password'</p>";
echo "<p class='info'>Hashed: $hashed</p>";

if (password_verify($test_password, $hashed)) {
    echo "<p class='success'>✓ Password verification works</p>";
} else {
    echo "<p class='error'>✗ Password verification failed</p>";
}

// Test file permissions and paths
echo "<h2>5. File System Check</h2>";
$files_to_check = [
    'php/config.php',
    'php/auth/login.php',
    'php/auth/logout.php',
    'php/auth/registration.php',
    'php/session_check.php',
    'js/auth.js',
    'login.html'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = substr(sprintf('%o', fileperms($file)), -4);
        echo "<p class='success'>✓ $file exists (permissions: $perms)</p>";
    } else {
        echo "<p class='error'>✗ $file does not exist</p>";
    }
}

// Test login endpoint
echo "<h2>6. Login Endpoint Test</h2>";
echo "<div id='login-test'>";
echo "<p class='info'>Testing login endpoint with sample data...</p>";

// Create a test user if none exist (only for debugging)
if (isset($conn) && $conn instanceof mysqli) {
    $test_email = 'test@example.com';
    
    // Check if test user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param('s', $test_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo "<p class='warning'>Creating test user for debugging...</p>";
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $test_password_hash = password_hash('test123', PASSWORD_DEFAULT);
        $stmt->bind_param('sssss', 
            $first_name = 'Test', 
            $last_name = 'User', 
            $test_email, 
            $phone = '1234567890', 
            $test_password_hash
        );
        
        if ($stmt->execute()) {
            echo "<p class='success'>✓ Test user created (email: test@example.com, password: test123)</p>";
        } else {
            echo "<p class='error'>✗ Failed to create test user</p>";
        }
    } else {
        echo "<p class='info'>Test user already exists (email: test@example.com)</p>";
    }
}

echo "</div>";

echo "<h2>7. Debugging Instructions</h2>";
echo "<ol>";
echo "<li><strong>If MySQLi extension is missing:</strong> Enable it in php.ini and restart Apache</li>";
echo "<li><strong>If database connection fails:</strong> Check XAMPP MySQL is running on port 3307</li>";
echo "<li><strong>If users table doesn't exist:</strong> Import database/create_tables.sql</li>";
echo "<li><strong>If login fails:</strong> Check browser console and network tab for errors</li>";
echo "<li><strong>For password issues:</strong> Ensure registration.php is using password_hash()</li>";
echo "</ol>";

echo "<h2>8. Test Login Form</h2>";
echo "<p>You can test the login with:</p>";
echo "<ul><li>Email: test@example.com</li><li>Password: test123</li></ul>";
echo "<p><a href='login.html' target='_blank' style='color: blue;'>Open Login Form</a></p>";

if (isset($conn)) {
    $conn->close();
}
?>
