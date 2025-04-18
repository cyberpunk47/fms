<?php

if (!function_exists('getDashboardStatsAlt')) {
    function getDashboardStatsAlt() {
        global $pdo;
        $stats = [
            'total_faculty' => 0,
            'total_departments' => 0,
            'pending_evaluations' => 0,
            'pending_promotions' => 0,
            'total_evaluations' => 0,
            'development_programs' => 0
        ];
        
        try {
            
            if (function_exists('tableExists') && tableExists('faculty')) {
                $stats['total_faculty'] = (int)$pdo->query("SELECT COUNT(*) FROM faculty")->fetchColumn();
            }
            
            
            if (function_exists('tableExists') && tableExists('departments')) {
                $stats['total_departments'] = (int)$pdo->query("SELECT COUNT(*) FROM departments")->fetchColumn();
            }
            
            
            if (function_exists('tableExists') && tableExists('evaluations')) {
                $stats['pending_evaluations'] = (int)$pdo->query("SELECT COUNT(*) FROM evaluations WHERE status IN ('draft', 'submitted')")->fetchColumn();
                $stats['total_evaluations'] = (int)$pdo->query("SELECT COUNT(*) FROM evaluations")->fetchColumn();
            }
            
            if (function_exists('tableExists') && tableExists('promotion_requests')) {
                $stats['pending_promotions'] = (int)$pdo->query("SELECT COUNT(*) FROM promotion_requests WHERE status = 'pending'")->fetchColumn();
            }
            
          
            $developmentCount = 0;
            if (function_exists('tableExists')) {
                if (tableExists('workshops')) {
                    $developmentCount += (int)$pdo->query("SELECT COUNT(*) FROM workshops")->fetchColumn();
                }
                
                if (tableExists('promotion_requests')) {
                    $developmentCount += (int)$pdo->query("SELECT COUNT(*) FROM promotion_requests")->fetchColumn();
                }
                
                if (tableExists('development_plans')) {
                    $developmentCount += (int)$pdo->query("SELECT COUNT(*) FROM development_plans")->fetchColumn();
                }
            }
            $stats['development_programs'] = $developmentCount;
        } catch (Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
        }
        
        return $stats;
    }
}

if (!function_exists('getRecentActivity')) {
    function getRecentActivity($limit = 5) {
        global $pdo;
        $activities = [];
        
        try {
            if (!function_exists('tableExists') || 
                !tableExists('evaluations') && !tableExists('promotion_requests') && !tableExists('workshops')) {
                return getSampleActivities();
            }
            
          
            $queries = [];
            
          
            if (tableExists('evaluations')) {
                $queries[] = "
                    SELECT 
                        'evaluation' as type,
                        e.evaluation_id as id,
                        CONCAT('Evaluation for ', u.first_name, ' ', u.last_name) as title,
                        e.created_at as time,
                        'purple' as color,
                        '<path d=\"M9 2a1 1 0 000 2h2a1 1 0 100-2H9z\"></path><path fill-rule=\"evenodd\" d=\"M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z\" clip-rule=\"evenodd\"></path>' as icon
                    FROM evaluations e
                    JOIN users u ON e.faculty_id = u.user_id
                    ORDER BY e.created_at DESC
                    LIMIT {$limit}
                ";
            }
            
           
            if (tableExists('promotion_requests')) {
                $queries[] = "
                    SELECT 
                        'promotion' as type,
                        p.id as id,
                        CONCAT('Promotion request by ', u.first_name, ' ', u.last_name) as title,
                        p.submission_date as time,
                        'amber' as color,
                        '<path d=\"M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z\"></path>' as icon
                    FROM promotion_requests p
                    JOIN users u ON p.faculty_id = u.user_id
                    ORDER BY p.submission_date DESC
                    LIMIT {$limit}
                ";
            }
     
            if (tableExists('workshops')) {
                $queries[] = "
                    SELECT 
                        'workshop' as type,
                        w.workshop_id as id,
                        CONCAT('Workshop: ', w.title) as title,
                        w.created_at as time,
                        'blue' as color,
                        '<path d=\"M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z\"></path>' as icon
                    FROM workshops w
                    ORDER BY w.created_at DESC
                    LIMIT {$limit}
                ";
            }
   
            if (tableExists('development_plans')) {
                $queries[] = "
                    SELECT 
                        'development' as type,
                        d.id as id,
                        CONCAT('Development Plan: ', d.title) as title,
                        d.created_at as time,
                        'green' as color,
                        '<path fill-rule=\"evenodd\" d=\"M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z\" clip-rule=\"evenodd\"></path>' as icon
                    FROM development_plans d
                    ORDER BY d.created_at DESC
                    LIMIT {$limit}
                ";
            }
   
            if (!empty($queries)) {
                $unionQuery = implode(' UNION ALL ', $queries) . " ORDER BY time DESC LIMIT $limit";
                $stmt = $pdo->query($unionQuery);
                
                if ($stmt) {
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
            }

            return getSampleActivities();
            
        } catch (Exception $e) {
            error_log("Error getting recent activity: " . $e->getMessage());
   
            return getSampleActivities();
        }
    }
    
    function getSampleActivities() {
        return [
            [
                'id' => 1,
                'title' => 'Evaluation for Michael Wilson',
                'time' => date('M j, Y', strtotime('-2 days')),
                'icon' => '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>',
                'color' => 'purple'
            ],
            [
                'id' => 2,
                'title' => 'Promotion request by Jessica Davis',
                'time' => date('M j, Y', strtotime('-5 days')),
                'icon' => '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>',
                'color' => 'amber'
            ],
            [
                'id' => 3,
                'title' => 'Workshop: Research Methodology Seminar',
                'time' => date('M j, Y', strtotime('-7 days')),
                'icon' => '<path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"></path>',
                'color' => 'blue'
            ],
            [
                'id' => 4,
                'title' => 'Development Plan: Research Enhancement Plan',
                'time' => date('M j, Y', strtotime('-10 days')),
                'icon' => '<path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>',
                'color' => 'green'
            ],
            [
                'id' => 5,
                'title' => 'New faculty member joined',
                'time' => date('M j, Y', strtotime('-14 days')),
                'icon' => '<path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>',
                'color' => 'blue'
            ]
        ];
    }
}
?>