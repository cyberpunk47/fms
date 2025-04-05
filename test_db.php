<?php
// filepath: /opt/lampp/htdocs/fms/test_db.php
require_once 'includes/config.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Database Connection Test</h2>";

// Test connection
try {
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }

    echo "<p>Connection successful!</p>";

    // Check if users table exists
    $result = $mysqli->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "<p>Users table exists.</p>";

        // Check users table structure
        $result = $mysqli->query("DESCRIBE users");
        echo "<h3>Users Table Structure:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<td>" . ($value ?? "NULL") . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>Users table does not exist!</p>";
    }

    $mysqli->close();
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>