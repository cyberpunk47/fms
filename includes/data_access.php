<?php
// filepath: /opt/lampp/htdocs/fms/includes/data_access.php

// Faculty functions
function getAllFaculty()
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT f.*, u.first_name, u.last_name, u.email, d.name as department_name
            FROM faculty f
            JOIN users u ON f.faculty_id = u.user_id
            JOIN departments d ON f.department_id = d.department_id
            ORDER BY u.last_name, u.first_name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllFaculty: " . $e->getMessage());
        return [];
    }
}

function getFacultyById($facultyId)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT f.*, u.first_name, u.last_name, u.email, d.name as department_name
            FROM faculty f
            JOIN users u ON f.faculty_id = u.user_id
            JOIN departments d ON f.department_id = d.department_id
            WHERE f.faculty_id = ?
        ");
        $stmt->execute([$facultyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getFacultyById: " . $e->getMessage());
        return null;
    }
}

function addFaculty($userData, $facultyData)
{
    global $pdo;

    try {
        $pdo->beginTransaction();

        // Create user first
        $salt = bin2hex(random_bytes(16));
        $hashed_password = hash('sha256', $userData['password'] . $salt);
        $password_hash = password_hash($hashed_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password_hash, salt, role, account_status)
            VALUES (?, ?, ?, ?, ?, 'faculty', 'active')
        ");
        
        $stmt->execute([
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $password_hash,
            $salt
        ]);
        
        $userId = $pdo->lastInsertId();
        
        // Add faculty record
        $stmt = $pdo->prepare("
            INSERT INTO faculty (faculty_id, department_id, position, rank, hire_date, tenure_status, bio, office_location)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $facultyData['department_id'],
            $facultyData['position'],
            $facultyData['rank'],
            $facultyData['hire_date'],
            $facultyData['tenure_status'],
            $facultyData['bio'],
            $facultyData['office_location']
        ]);
        
        $pdo->commit();
        return $userId;
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("Database error in addFaculty: " . $e->getMessage());
        return false;
    }
}

// Departments functions
function getAllDepartments()
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as head_name
            FROM departments d
            LEFT JOIN users u ON d.head_id = u.user_id
            ORDER BY d.name
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getAllDepartments: " . $e->getMessage());
        return [];
    }
}

function getDepartmentById($departmentId)
{
    global $pdo;
    try {
        $stmt = $pdo->prepare("
            SELECT d.*, CONCAT(u.first_name, ' ', u.last_name) as head_name
            FROM departments d
            LEFT JOIN users u ON d.head_id = u.user_id
            WHERE d.department_id = ?
        ");
        $stmt->execute([$departmentId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getDepartmentById: " . $e->getMessage());
        return null;
    }
}

function addDepartment($departmentData)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO departments (name, code, description, head_id)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $departmentData['name'],
            $departmentData['code'],
            $departmentData['description'],
            $departmentData['head_id']
        ]);
        
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Database error in addDepartment: " . $e->getMessage());
        return false;
    }
}

// Evaluations functions
function getEvaluations($facultyId = null, $status = null)
{
    global $pdo;
    try {
        $sql = "
            SELECT e.*, 
                  CONCAT(fu.first_name, ' ', fu.last_name) as faculty_name,
                  CONCAT(eu.first_name, ' ', eu.last_name) as evaluator_name,
                  d.name as department_name
            FROM evaluations e
            JOIN faculty f ON e.faculty_id = f.faculty_id
            JOIN users fu ON f.faculty_id = fu.user_id
            JOIN users eu ON e.evaluator_id = eu.user_id
            JOIN departments d ON f.department_id = d.department_id
        ";
        
        $params = [];
        $conditions = [];
        
        if ($facultyId) {
            $conditions[] = "e.faculty_id = ?";
            $params[] = $facultyId;
        }
        
        if ($status) {
            $conditions[] = "e.status = ?";
            $params[] = $status;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $sql .= " ORDER BY e.created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getEvaluations: " . $e->getMessage());
        return [];
    }
}

// Dashboard statistics
function getDashboardStats()
{
    global $pdo;
    try {
        $stats = [
            'total_faculty' => 0,
            'total_departments' => 0,
            'pending_evaluations' => 0,
            'pending_promotions' => 0,
            'total_evaluations' => 0,
            'development_programs' => 0
        ];
        
        // Get total faculty
        $stmt = $pdo->query("SELECT COUNT(*) FROM faculty");
        $stats['total_faculty'] = (int)$stmt->fetchColumn();
        
        // Get total departments
        $stmt = $pdo->query("SELECT COUNT(*) FROM departments");
        $stats['total_departments'] = (int)$stmt->fetchColumn();
        
        // Get evaluations data
        $stmt = $pdo->query("SELECT COUNT(*) FROM evaluations");
        $stats['total_evaluations'] = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM evaluations WHERE status IN ('draft', 'submitted')");
        $stats['pending_evaluations'] = (int)$stmt->fetchColumn();
        
        // Get promotion data
        $stmt = $pdo->query("SELECT COUNT(*) FROM promotion_requests WHERE status = 'pending'");
        $stats['pending_promotions'] = (int)$stmt->fetchColumn();
        
        // Count development programs (workshops + development plans)
        $stmt = $pdo->query("SELECT COUNT(*) FROM workshops");
        $workshopCount = (int)$stmt->fetchColumn();
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM development_plans");
        $plansCount = (int)$stmt->fetchColumn();
        
        $stats['development_programs'] = $workshopCount + $plansCount;
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Database error in getDashboardStats: " . $e->getMessage());
        return [
            'total_faculty' => 0,
            'total_departments' => 0,
            'pending_evaluations' => 0,
            'pending_promotions' => 0,
            'total_evaluations' => 0,
            'development_programs' => 0
        ];
    }
}

// Recent activities
function getRecentActivities($limit = 5)
{
    global $pdo;
    try {
        $activities = [];
        
        // Get recent evaluations
        $evalStmt = $pdo->prepare("
            SELECT 
                'evaluation' as activity_type,
                e.evaluation_id as id,
                CONCAT('Evaluation for ', u.first_name, ' ', u.last_name) as title,
                e.created_at as activity_time,
                'purple' as color
            FROM evaluations e
            JOIN users u ON e.faculty_id = u.user_id
            ORDER BY e.created_at DESC
            LIMIT ?
        ");
        $evalStmt->execute([$limit]);
        $evalActivities = $evalStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent promotions
        $promoStmt = $pdo->prepare("
            SELECT 
                'promotion' as activity_type,
                p.id as id,
                CONCAT('Promotion request by ', u.first_name, ' ', u.last_name) as title,
                p.submission_date as activity_time,
                'amber' as color
            FROM promotion_requests p
            JOIN users u ON p.faculty_id = u.user_id
            ORDER BY p.submission_date DESC
            LIMIT ?
        ");
        $promoStmt->execute([$limit]);
        $promoActivities = $promoStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get recent workshops
        $workshopStmt = $pdo->prepare("
            SELECT 
                'workshop' as activity_type,
                w.workshop_id as id,
                CONCAT('Workshop: ', w.title) as title,
                w.created_at as activity_time,
                'blue' as color
            FROM workshops w
            ORDER BY w.created_at DESC
            LIMIT ?
        ");
        $workshopStmt->execute([$limit]);
        $workshopActivities = $workshopStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Combine and sort all activities
        $activities = array_merge($evalActivities, $promoActivities, $workshopActivities);
        
        // Sort by activity_time descending
        usort($activities, function($a, $b) {
            return strtotime($b['activity_time']) - strtotime($a['activity_time']);
        });
        
        // Format activities with correct fields for display
        $formattedActivities = [];
        foreach (array_slice($activities, 0, $limit) as $activity) {
            $icon = '';
            
            // Set appropriate icon based on activity type
            switch ($activity['activity_type']) {
                case 'evaluation':
                    $icon = '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>';
                    break;
                case 'promotion':
                    $icon = '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>';
                    break;
                case 'workshop':
                    $icon = '<path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>';
                    break;
                default:
                    $icon = '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>';
            }
            
            $formattedActivities[] = [
                'id' => $activity['id'],
                'title' => $activity['title'],
                'time' => formatTimeAgo($activity['activity_time']),
                'icon' => $icon,
                'color' => $activity['color']
            ];
        }
        
        return $formattedActivities;
    } catch (PDOException $e) {
        error_log("Database error in getRecentActivities: " . $e->getMessage());
        return [];
    }
}

// Helper function for formatting
function formatTimeAgo($timestamp)
{
    $time = strtotime($timestamp);
    $now = time();
    $diff = $now - $time;

    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "$minutes minute" . ($minutes > 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "$hours hour" . ($hours > 1 ? "s" : "") . " ago";
    } elseif ($diff < 172800) {
        return "Yesterday at " . date("g:i A", $time);
    } else {
        return date("M j, Y", $time);
    }
}

function getActivityColor($action)
{
    switch ($action) {
        case 'INSERT':
            return 'green';
        case 'UPDATE':
            return 'blue';
        case 'DELETE':
            return 'red';
        case 'evaluation':
            return 'purple';
        case 'promotion':
            return 'amber';
        case 'workshop':
            return 'blue';
        default:
            return 'gray';
    }
}

function getActivityIcon($action)
{
    switch ($action) {
        case 'INSERT':
            return '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>';
        case 'UPDATE':
            return '<path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>';
        case 'DELETE':
            return '<path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>';
        default:
            return '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>';
    }
}

// Get upcoming events
function getUpcomingEvents($limit = 3)
{
    global $pdo;

    $events = [];

    try {
        $stmt = $pdo->prepare("
            SELECT e.*, DATE_FORMAT(e.start_date, '%b') as month, DATE_FORMAT(e.start_date, '%d') as day
            FROM events e
            WHERE e.start_date >= CURDATE()
            ORDER BY e.start_date
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $events[] = [
                'id' => $row['event_id'],
                'month' => strtoupper($row['month']),
                'day' => $row['day'],
                'title' => $row['title'],
                'time' => date('g:i A', strtotime($row['start_date'])) . ($row['location'] ? ' - ' . $row['location'] : '')
            ];
        }
        
        // If we don't have enough events, try to get workshops too
        if (count($events) < $limit) {
            $workshopStmt = $pdo->prepare("
                SELECT w.*, DATE_FORMAT(w.start_date, '%b') as month, DATE_FORMAT(w.start_date, '%d') as day
                FROM workshops w
                WHERE w.start_date >= CURDATE() AND w.status = 'scheduled'
                ORDER BY w.start_date
                LIMIT ?
            ");
            $workshopStmt->execute([$limit - count($events)]);
            
            while ($row = $workshopStmt->fetch(PDO::FETCH_ASSOC)) {
                $events[] = [
                    'id' => $row['workshop_id'],
                    'month' => strtoupper($row['month']),
                    'day' => $row['day'],
                    'title' => 'Workshop: ' . $row['title'],
                    'time' => date('g:i A', strtotime($row['start_date'])) . ($row['location'] ? ' - ' . $row['location'] : '')
                ];
            }
            
            // Sort combined list by date
            usort($events, function($a, $b) {
                $aDate = strtotime($a['month'] . ' ' . $a['day']);
                $bDate = strtotime($b['month'] . ' ' . $b['day']);
                return $aDate - $bDate;
            });
        }
        
        return $events;
    } catch (PDOException $e) {
        error_log("Error fetching upcoming events: " . $e->getMessage());
        return [];
    }
}

// Get pending tasks
function getPendingTasks($userId, $limit = 3)
{
    global $pdo;

    $tasks = [];

    try {
        $stmt = $pdo->prepare("
            SELECT task_id, title as description, status
            FROM tasks
            WHERE assigned_to = ? AND status IN ('pending', 'in_progress')
            ORDER BY due_date ASC, created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = [
                'id' => $row['task_id'],
                'description' => $row['description'],
                'completed' => $row['status'] === 'completed'
            ];
        }
        
        return $tasks;
    } catch (PDOException $e) {
        error_log("Error fetching pending tasks: " . $e->getMessage());
        return [];
    }
}

// Record audit trail
function recordAuditTrail($userId, $action, $table, $recordId, $oldValues = null, $newValues = null)
{
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO audit_log (user_id, action, table_affected, record_id, old_values, new_values, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $userId,
            $action,
            $table,
            $recordId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Error recording audit trail: " . $e->getMessage());
        return false;
    }
}
?>