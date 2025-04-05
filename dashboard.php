<?php
// Include required files
require_once 'includes/config.php';
require_once 'includes/data_access.php';

// Authentication check
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Try to include dashboard_data.php and utilities.php
$utilsLoaded = false;
if (file_exists('includes/utilities.php')) {
    require_once 'includes/utilities.php';
    $utilsLoaded = true;
} else {
    // Define minimal versions of required functions
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
    // Define fallback functions
    function getDashboardStatsAlt() {
        global $pdo;
        return getDashboardStats();
    }
    
    function getRecentActivity($limit = 5) {
        global $pdo;
        return getRecentActivities($limit);
    }
}

// Get dashboard data
$dashboardStats = getDashboardStatsAlt();
$recentActivity = getRecentActivity();

// Map the array keys to what the dashboard HTML expects
$dashboardStats['facultyCount'] = $dashboardStats['total_faculty'] ?? 0;
$dashboardStats['departmentCount'] = $dashboardStats['total_departments'] ?? 0;
$dashboardStats['evaluationCount'] = $dashboardStats['total_evaluations'] ?? 0;
$dashboardStats['pendingEvaluations'] = $dashboardStats['pending_evaluations'] ?? 0;
$dashboardStats['developmentCount'] = $dashboardStats['development_programs'] ?? 0;
$dashboardStats['upcomingDevelopment'] = $dashboardStats['pending_promotions'] ?? 0;

// Get upcoming events and tasks
$upcomingEvents = [];
if (function_exists('getUpcomingEvents')) {
    $upcomingEvents = getUpcomingEvents(3);
}

$pendingTasks = [];
if (function_exists('getPendingTasks')) {
    // Get current user ID from session
    $userId = $_SESSION['user_id'] ?? null;
    
    // Check how many parameters the function accepts to handle it properly
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
        // If reflection fails, try the most likely parameter pattern
        try {
            $pendingTasks = getPendingTasks($userId);
        } catch (Error $e) {
            $pendingTasks = [];
        }
    }
}

// Set page title
$pageTitle = "Dashboard";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Dashboard
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Faculty Card -->
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

            <!-- Departments Card -->
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

            <!-- Evaluations Card -->
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

            <!-- Development Card -->
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

        <!-- Two column layout for remaining content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Activity</h3>
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

            <!-- Right Column - Tasks & Events -->
            <div class="space-y-6">
                <!-- Upcoming Events -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Upcoming Events</h3>
                    </div>
                    
                    <?php if (empty($upcomingEvents)): ?>
                        <div class="p-4 text-center text-gray-500">
                            No upcoming events scheduled.
                        </div>
                    <?php else: ?>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($upcomingEvents as $event): ?>
                                <div class="flex px-4 py-4">
                                    <div class="flex-shrink-0 mr-4 text-center">
                                        <div class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded-t">
                                            <?php echo htmlspecialchars($event['month']); ?>
                                        </div>
                                        <div class="bg-white border border-blue-100 text-gray-800 text-base font-bold px-2 py-1 rounded-b border-t-0">
                                            <?php echo htmlspecialchars($event['day']); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($event['title']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($event['time']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between">
                        <a href="event_create.php" class="text-sm font-medium text-blue-600 hover:text-blue-500">Create event</a>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">View calendar</a>
                    </div>
                </div>

                <!-- Your Tasks -->
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                    <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Your Tasks</h3>
                    </div>
                    
                    <?php if (empty($pendingTasks)): ?>
                        <div class="p-4 text-center text-gray-500">
                            No pending tasks.
                        </div>
                    <?php else: ?>
                        <div class="p-4">
                            <ul class="space-y-3">
                                <?php foreach ($pendingTasks as $task): ?>
                                    <li class="flex items-start">
                                        <div class="flex-shrink-0 mt-0.5">
                                            <input type="checkbox" id="task-<?php echo $task['id']; ?>" class="task-checkbox h-4 w-4 text-blue-600 rounded border-gray-300" data-task-id="<?php echo $task['id']; ?>" <?php echo ($task['completed'] ? 'checked' : ''); ?>>
                                        </div>
                                        <label for="task-<?php echo $task['id']; ?>" class="ml-3 text-sm text-gray-700 <?php echo ($task['completed'] ? 'line-through' : ''); ?>">
                                            <?php echo htmlspecialchars($task['description']); ?>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex justify-between">
                        <a href="task_create.php" class="text-sm font-medium text-blue-600 hover:text-blue-500">Create task</a>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all tasks</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>