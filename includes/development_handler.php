<?php
require_once 'config.php';
require_once 'data_access.php';

function getPromotionRequests($faculty_id = null) {
    global $pdo;
    
    try {
        
        $check = $pdo->query("SHOW TABLES LIKE 'promotion_requests'");
        if ($check->rowCount() == 0) {
            
            return [];
        }
        
        $sql = "SELECT p.*, 
                CONCAT(u.first_name, ' ', u.last_name) as faculty_name,
                f.rank as current_rank
            FROM promotion_requests p
            JOIN users u ON p.faculty_id = u.user_id
            JOIN faculty f ON p.faculty_id = f.faculty_id";
        
        $params = [];
        if ($faculty_id) {
            $sql .= " WHERE p.faculty_id = ?";
            $params[] = $faculty_id;
        }
        
        $sql .= " ORDER BY p.submission_date DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching promotion requests: " . $e->getMessage());
        return [];
    }
}


function getDevelopmentPlans($faculty_id = null) {
    global $pdo;
    
    try {
    
        $check = $pdo->query("SHOW TABLES LIKE 'development_plans'");
        if ($check->rowCount() == 0) {
           
            return [];
        }
        
        $sql = "SELECT d.*, 
                CONCAT(u.first_name, ' ', u.last_name) as faculty_name
            FROM development_plans d
            JOIN users u ON d.faculty_id = u.user_id";
        
        $params = [];
        if ($faculty_id) {
            $sql .= " WHERE d.faculty_id = ?";
            $params[] = $faculty_id;
        }
        
        $sql .= " ORDER BY d.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching development plans: " . $e->getMessage());
        return [];
    }
}


 
function saveDevelopmentPlan($planData) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO development_plans (
                faculty_id, title, description, goals, timeline, status, created_by
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $planData['faculty_id'],
            $planData['title'],
            $planData['description'],
            $planData['goals'],
            $planData['timeline'],
            $planData['status'] ?? 'draft',
            $planData['created_by']
        ]);
        
        if ($result) {
            return $pdo->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error saving development plan: " . $e->getMessage());
        return false;
    }
}
?>