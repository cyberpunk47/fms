<?php
// filepath: /opt/lampp/htdocs/fms/setup_tables.php
require_once 'includes/config.php';

try {
    // Create events table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS events (
            event_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            start_date DATETIME NOT NULL,
            end_date DATETIME,
            location VARCHAR(255),
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Create tasks table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS tasks (
            task_id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            assigned_to INT,
            assigned_by INT,
            due_date DATE,
            status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Create promotion_requests table if it doesn't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS promotion_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            faculty_id INT NOT NULL,
            current_rank VARCHAR(50) NOT NULL,
            requested_rank VARCHAR(50) NOT NULL,
            justification TEXT,
            status ENUM('pending', 'under_review', 'approved', 'denied') DEFAULT 'pending',
            reviewer_id INT,
            review_comments TEXT,
            submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    echo "Tables created successfully!";
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}