<?php
require_once 'includes/config.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$admin_firstname = "Tester";
$admin_lastname = "New"; 
$admin_email = "sudo@faculty.edu";  
$admin_password = "admin123";

try {
    
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($mysqli->connect_error) {
        throw new Exception("Connection failed: " . $mysqli->connect_error);
    }
    
   
    $check = $mysqli->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->bind_param("s", $admin_email);
    $check->execute();
    $check->store_result();
    $count = $check->num_rows;
    
    if ($count > 0) {
        echo "<h2>An admin with this email already exists!</h2>";
        echo "<p>Please choose a different email address.</p>";
    } else {

        $salt = bin2hex(random_bytes(16));
        
    
        $hashed_password = hash('sha256', $admin_password . $salt);
   
        $secure_hash = password_hash($hashed_password, PASSWORD_DEFAULT);
        
      
        $stmt = $mysqli->prepare("INSERT INTO users 
            (first_name, last_name, email, password_hash, salt, role, account_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
            
        $role = 'admin';
        $status = 'active';
        $stmt->bind_param("sssssss", 
            $admin_firstname, 
            $admin_lastname, 
            $admin_email, 
            $secure_hash, 
            $salt, 
            $role, 
            $status
        );
        
        $result = $stmt->execute();
        
        if ($result) {
            echo "<h2>New admin user created successfully!</h2>";
            echo "<p>You can now log in with:</p>";
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>Email</th><td><strong>{$admin_email}</strong></td></tr>";
            echo "<tr><th>Password</th><td><strong>{$admin_password}</strong></td></tr>";
            echo "</table>";
            
            echo "<p><a href='index.php'>Go to login page</a></p>";
        } else {
            echo "<h2>Error creating admin user</h2>";
            echo "<p>Please check your database configuration.</p>";
        }
    }
    $mysqli->close();
} catch (Exception $e) {
    echo "<h2>Database Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>