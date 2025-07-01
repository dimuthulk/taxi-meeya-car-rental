<?php
// test_db.php - Database connection test script
include_once 'php/config.php';

echo "<h2>Database Connection Test</h2>";

// Test database connection
if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Users table exists!</p>";
        
        // Show table structure
        $result = $conn->query("DESCRIBE users");
        if ($result) {
            echo "<h3>Users Table Structure:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['Field'] . "</td>";
                echo "<td>" . $row['Type'] . "</td>";
                echo "<td>" . $row['Null'] . "</td>";
                echo "<td>" . $row['Key'] . "</td>";
                echo "<td>" . $row['Default'] . "</td>";
                echo "<td>" . $row['Extra'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: red;'>✗ Users table does not exist!</p>";
        echo "<p>Please create the database and tables using the SQL file in the database folder.</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    echo "<p>Please check your database configuration in php/config.php</p>";
}

// Close connection
if ($conn) {
    $conn->close();
}
?>
