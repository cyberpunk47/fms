<?php

$db_host = 'localhost';
$db_name = 'new';
$db_user = 'root'; 
$db_password = 'aman'; 

$base_url = '/fms_master/'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 
    $mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
    if ($mysqli->connect_error) {
        throw new Exception("MySQLi connection failed: " . $mysqli->connect_error);
    }
    
} catch(PDOException $e) {
  
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please check server logs or contact administrator.");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';
?>