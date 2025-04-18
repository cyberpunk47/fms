<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$utilsLoaded = false;
if (file_exists('includes/utilities.php')) {
    require_once 'includes/utilities.php';
    $utilsLoaded = true;
} else {
    function tableExists($tableName) {
        global $pdo;
        try {
            $result = $pdo->query("SHOW TABLES LIKE '$tableName'");
            return $result->rowCount() > 0;
        } catch (Exception $e) {
            return false;
        }
    }
    
    function safeQueryFetch($sql, $params = []) {
        global $pdo;
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
}

if (file_exists('includes/dashboard_data.php')) {
    require_once 'includes/dashboard_data.php';
} else {
    function getDashboardStatsAlt() {
        global $pdo;
        return getDashboardStats();
    }
    
}
$dashboardStats = getDashboardStatsAlt();

$dashboardStats['facultyCount'] = $dashboardStats['total_faculty'] ?? 0;
$dashboardStats['departmentCount'] = $dashboardStats['total_departments'] ?? 0;
$dashboardStats['evaluationCount'] = $dashboardStats['total_evaluations'] ?? 0;
$dashboardStats['pendingEvaluations'] = $dashboardStats['pending_evaluations'] ?? 0;
$dashboardStats['developmentCount'] = $dashboardStats['development_programs'] ?? 0;
$dashboardStats['upcomingDevelopment'] = $dashboardStats['pending_promotions'] ?? 0;

$upcomingEvents = [];
if (function_exists('getUpcomingEvents')) {
    $upcomingEvents = getUpcomingEvents(3);
}

$pendingTasks = [];
if (function_exists('getPendingTasks')) {
    $userId = $_SESSION['user_id'] ?? null;
    
    try {
        $reflection = new ReflectionFunction('getPendingTasks');
        $paramCount = $reflection->getNumberOfRequiredParameters();
        
        if ($paramCount == 0) {
            $pendingTasks = getPendingTasks();
        } else if ($paramCount == 1) {
            $pendingTasks = getPendingTasks($userId);
        } else {
            $pendingTasks = getPendingTasks($userId, 5);
        }
    } catch (Exception $e) {
        try {
            $pendingTasks = getPendingTasks($userId);
        } catch (Error $e) {
            $pendingTasks = [];
        }
    }
}
function getrecent()
{
    global $pdo;
    try {
        $activities = [];
        $evalStmt = $pdo->prepare("
            SELECT 
                'evaluation' as activity_type,
                e.faculty_id as id,
                CONCAT('Evaluation for ', f.Name) as title,
                e.created_at as activity_time,
                'purple' as color
            FROM evaluations e
            JOIN facultydata f ON e.faculty_id = f.f_id
            ORDER BY e.created_at DESC
            LIMIT 5
        ");
        $evalStmt->execute();
        $evalActivities = $evalStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $promoStmt = $pdo->prepare("
            SELECT 
                'promotion' as activity_type,
                p.id as id,
                CONCAT('Promotion request by ', f.Name) as title,
                p.submission_date as activity_time,
                'amber' as color
            FROM promotion_requests p
            JOIN facultydata f ON p.faculty_id = f.f_id
            ORDER BY p.submission_date DESC
            LIMIT 5
        ");
        $promoStmt->execute();
        $promoActivities = $promoStmt->fetchAll(PDO::FETCH_ASSOC);
        
        $workshopStmt = $pdo->prepare("
            SELECT 
                'workshop' as activity_type,
                w.workshop_id as id,
                CONCAT('Workshop: ', w.title) as title,
                w.created_at as activity_time,
                'blue' as color
            FROM workshops w
            ORDER BY w.created_at DESC
            LIMIT 5
        ");
        $workshopStmt->execute();
        $workshopActivities = $workshopStmt->fetchAll(PDO::FETCH_ASSOC);
        $activities = array_merge($evalActivities, $promoActivities, $workshopActivities);
        
        usort($activities, function($a, $b) {
            return strtotime($b['activity_time']) - strtotime($a['activity_time']);
        });
        
        $formattedActivities = [];
        foreach (array_slice($activities, 0, 5) as $activity) {
            $icon = '';
            
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
$recentActivity = getrecent();
$pageTitle = "Dashboard";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 sm:ml-64">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Dashboard
            </h2>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <a href="faculty.php" class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500 hover:bg-blue-50 transition">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-md p-3 bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm font-medium">Faculty</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $dashboardStats['facultyCount']; ?></p>
                    </div>
                </div>
            </a>
            <a href="departments.php" class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500 hover:bg-green-50 transition">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-md p-3 bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm font-medium">Departments</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $dashboardStats['departmentCount']; ?></p>
                    </div>
                </div>
            </a>
            <a href="evaluations.php" class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500 hover:bg-purple-50 transition">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-md p-3 bg-purple-100">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm font-medium">Evaluations</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $dashboardStats['evaluationCount']; ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $dashboardStats['pendingEvaluations']; ?> pending</p>
                    </div>
                </div>
            </a>
            <a href="development.php" class="bg-white rounded-lg shadow p-4 border-l-4 border-amber-500 hover:bg-amber-50 transition">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-md p-3 bg-amber-100">
                        <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-gray-600 text-sm font-medium">Development</h2>
                        <p class="text-2xl font-semibold text-gray-800"><?php echo $dashboardStats['developmentCount']; ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo $dashboardStats['upcomingDevelopment']; ?> upcoming</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="flex justify-center">
            <div class="w-full lg:w-3/4">
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 text-center">Recent Activity</h3>
                    </div>

                    <?php if (empty($recentActivity)): ?>
                        <div class="p-4 text-center text-gray-500">
                            No recent activity found.
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-<?php echo htmlspecialchars($activity['color'] ?? 'gray'); ?>-500" fill="currentColor" viewBox="0 0 20 20">
                                                <?php echo $activity['icon'] ?? ''; ?>
                                            </svg>
                                        </div>
                                        <div class="ml-3 w-0 flex-1">
                                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($activity['title'] ?? 'Unknown activity'); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($activity['time'] ?? ''); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="bg-gray-50 px-4 py-3 sm:px-6 text-right">
                        <a href="reports.php" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all activity</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>