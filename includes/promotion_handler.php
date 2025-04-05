<?php
require_once 'config.php';
require_once 'data_access.php';

function processPromotionRequest($faculty_id, $current_rank, $requested_rank, $justification) {
    global $mysqli;
    
    try {
        // Check if table exists first
        $tableCheck = $mysqli->query("SHOW TABLES LIKE 'promotion_requests'");
        if ($tableCheck->num_rows == 0) {
            return [
                'success' => false,
                'message' => 'System error: Promotion requests table does not exist. Please contact administrator.'
            ];
        }
        
        // Check for existing requests
        $check = $mysqli->prepare("SELECT id FROM promotion_requests WHERE faculty_id = ? AND status IN ('pending', 'under_review')");
        $check->bind_param("i", $faculty_id);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            return [
                'success' => false,
                'message' => 'A pending promotion request already exists for this faculty member.'
            ];
        }
        
        // Insert new request
        $stmt = $mysqli->prepare("INSERT INTO promotion_requests 
            (faculty_id, current_rank, requested_rank, justification, status, submission_date) 
            VALUES (?, ?, ?, ?, 'pending', NOW())");
        
        if (!$stmt) {
            return [
                'success' => false,
                'message' => 'Error preparing statement: ' . $mysqli->error
            ];
        }
        
        $stmt->bind_param("isss", $faculty_id, $current_rank, $requested_rank, $justification);
        
        if ($stmt->execute()) {
            // Create notification for department head if notifications table exists
            $tableCheck = $mysqli->query("SHOW TABLES LIKE 'notifications'");
            if ($tableCheck->num_rows > 0) {
                createPromotionRequestNotification($faculty_id);
            }
            
            return [
                'success' => true,
                'message' => 'Promotion request submitted successfully.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error submitting promotion request: ' . $mysqli->error
            ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'System error: ' . $e->getMessage()
        ];
    }
}

function createPromotionRequestNotification($faculty_id) {
    global $mysqli;
    
    try {
        // Get faculty info
        $stmt = $mysqli->prepare("
            SELECT f.*, u.first_name, u.last_name, d.head_id 
            FROM faculty f 
            JOIN users u ON f.faculty_id = u.user_id
            JOIN departments d ON f.department_id = d.department_id
            WHERE f.faculty_id = ?
        ");
        $stmt->bind_param("i", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $faculty = $result->fetch_assoc();
        
        if ($faculty && $faculty['head_id']) {
            // Create notification for department head
            $subject = "New Promotion Request";
            $message = "Faculty member {$faculty['first_name']} {$faculty['last_name']} has submitted a promotion request.";
            
            $stmt = $mysqli->prepare("
                INSERT INTO notifications (recipient_id, sender_id, subject, message, notification_type, related_id)
                VALUES (?, ?, ?, ?, 'promotion', (SELECT MAX(id) FROM promotion_requests WHERE faculty_id = ?))
            ");
            $stmt->bind_param("iissi", $faculty['head_id'], $faculty_id, $subject, $message, $faculty_id);
            $stmt->execute();
        }
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        // Continue even if notification fails
    }
}