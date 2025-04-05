<?php
// filepath: /opt/lampp/htdocs/fms/development.php
// Replace the entire file with this implementation that fixes tab content

require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Fetch workshops from database
$workshops = [];
try {
    if (tableExists($pdo, 'workshops')) {
        $stmt = $pdo->query("
            SELECT w.*, u.first_name, u.last_name, d.name as department_name 
            FROM workshops w
            LEFT JOIN users u ON w.created_by = u.user_id
            LEFT JOIN departments d ON w.department_id = d.department_id
            ORDER BY w.start_date DESC
        ");
        $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching workshops: " . $e->getMessage());
}

// Fetch promotion requests
$promotionRequests = [];
try {
    if (tableExists($pdo, 'promotion_requests')) {
        $stmt = $pdo->query("
            SELECT pr.*, 
                   u.first_name, u.last_name, 
                   r.first_name as reviewer_first_name, r.last_name as reviewer_last_name
            FROM promotion_requests pr
            JOIN users u ON pr.faculty_id = u.user_id
            LEFT JOIN users r ON pr.reviewer_id = r.user_id
            ORDER BY pr.submission_date DESC
        ");
        $promotionRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching promotion requests: " . $e->getMessage());
}

// Fetch development plans
$developmentPlans = [];
try {
    if (tableExists($pdo, 'development_plans')) {
        $stmt = $pdo->query("
            SELECT dp.*, 
                   u.first_name, u.last_name,
                   c.first_name as creator_first_name, c.last_name as creator_last_name
            FROM development_plans dp
            JOIN users u ON dp.faculty_id = u.user_id
            JOIN users c ON dp.created_by = c.user_id
            ORDER BY dp.created_at DESC
        ");
        $developmentPlans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    error_log("Error fetching development plans: " . $e->getMessage());
}

// If there's no data, create some dummy data for demonstration
if (empty($workshops)) {
    $workshops = [
        [
            'workshop_id' => 1,
            'title' => 'Teaching Innovation Workshop',
            'description' => 'Exploring innovative teaching methodologies',
            'start_date' => date('Y-m-d H:i:s', strtotime('+3 days')),
            'end_date' => date('Y-m-d H:i:s', strtotime('+3 days +4 hours')),
            'location' => 'Conference Room A',
            'max_participants' => 25,
            'facilitator' => 'Dr. Sarah Johnson',
            'department_name' => 'Computer Science',
            'status' => 'scheduled',
            'first_name' => 'Admin',
            'last_name' => 'User'
        ],
        [
            'workshop_id' => 2,
            'title' => 'Research Methodology Seminar',
            'description' => 'Best practices in academic research',
            'start_date' => date('Y-m-d H:i:s', strtotime('+7 days')),
            'end_date' => date('Y-m-d H:i:s', strtotime('+7 days +6 hours')),
            'location' => 'Faculty Lounge',
            'max_participants' => 30,
            'facilitator' => 'Prof. David Brown',
            'department_name' => 'Mathematics',
            'status' => 'scheduled',
            'first_name' => 'Admin',
            'last_name' => 'User'
        ]
    ];
}

if (empty($promotionRequests)) {
    $promotionRequests = [
        [
            'id' => 1,
            'faculty_id' => 5,
            'current_rank' => 'Assistant Professor',
            'requested_rank' => 'Associate Professor',
            'justification' => 'I have completed 5 years as Assistant Professor with excellent teaching evaluations and 15 published papers.',
            'status' => 'pending',
            'submission_date' => date('Y-m-d H:i:s', strtotime('-3 days')),
            'first_name' => 'Michael',
            'last_name' => 'Wilson'
        ],
        [
            'id' => 2,
            'faculty_id' => 6,
            'current_rank' => 'Associate Professor',
            'requested_rank' => 'Professor',
            'justification' => 'I have served 8 years as Associate Professor with consistent excellent performance in research and teaching.',
            'status' => 'under_review',
            'reviewer_first_name' => 'John',
            'reviewer_last_name' => 'Smith',
            'submission_date' => date('Y-m-d H:i:s', strtotime('-7 days')),
            'first_name' => 'Jessica',
            'last_name' => 'Davis'
        ]
    ];
}

if (empty($developmentPlans)) {
    $developmentPlans = [
        [
            'id' => 1,
            'faculty_id' => 7,
            'title' => 'Research Enhancement Plan',
            'description' => 'Plan to enhance research output and impact',
            'goals' => 'Publish 3 papers in top journals, Apply for 2 major grants, Mentor 2 PhD students',
            'timeline' => '12 months',
            'status' => 'in_progress',
            'created_at' => date('Y-m-d H:i:s', strtotime('-14 days')),
            'first_name' => 'Robert',
            'last_name' => 'Johnson',
            'creator_first_name' => 'John',
            'creator_last_name' => 'Smith'
        ],
        [
            'id' => 2,
            'faculty_id' => 8,
            'title' => 'Teaching Excellence Program',
            'description' => 'Program to enhance teaching methodologies and student engagement',
            'goals' => 'Develop 2 new courses, Implement innovative teaching methods, Improve student feedback scores',
            'timeline' => '24 months',
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s', strtotime('-21 days')),
            'first_name' => 'Catherine',
            'last_name' => 'Thomas',
            'creator_first_name' => 'David',
            'creator_last_name' => 'Brown'
        ]
    ];
}

// Helper function to check if table exists
function tableExists($pdo, $tableName) {
    try {
        $result = $pdo->query("SHOW TABLES LIKE '{$tableName}'");
        return $result->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Include header and sidebar
$pageTitle = "Faculty Development";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 sm:ml-64">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>
                <a href="workshop_create.php" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 mr-2">
                    Add Workshop
                </a>
                <a href="promotion_request.php" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700">
                    New Promotion Request
                </a>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Development Tabs">
                <button id="tab-workshops" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-current="page">
                    Workshops & Training
                </button>
                <button id="tab-promotions" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Promotion Requests
                </button>
                <button id="tab-plans" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Professional Development Plans
                </button>
            </nav>
        </div>

        <!-- Workshop Content -->
        <div id="workshops-content" class="mt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Upcoming Workshops & Training</h3>
            
            <?php if (empty($workshops)): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <p class="text-gray-500">No upcoming workshops or training sessions.</p>
                    <a href="workshop_create.php" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Workshop
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($workshops as $workshop): ?>
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-blue-500 text-white">
                                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 001 1h12a1 1 0 001-1V8a1 1 0 00.496-1.868l-7-4zM3 9.5v-1.793l7-4.7 7 4.7v1.793l-7-1.548-7 1.548zm0 2.267V15h14v-3.233L10 13.036 3 11.767z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($workshop['title']); ?></h4>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($workshop['description']); ?></p>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                    switch($workshop['status']) {
                                                        case 'scheduled':
                                                            echo 'bg-blue-100 text-blue-800';
                                                            break;
                                                        case 'in_progress':
                                                            echo 'bg-yellow-100 text-yellow-800';
                                                            break;
                                                        case 'completed':
                                                            echo 'bg-green-100 text-green-800';
                                                            break;
                                                        case 'cancelled':
                                                            echo 'bg-red-100 text-red-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-100 text-gray-800';
                                                    }
                                                ?>">
                                                <?php echo ucfirst($workshop['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <?php 
                                                    echo date('M j, Y, g:i A', strtotime($workshop['start_date'])); 
                                                    if (!empty($workshop['end_date'])) {
                                                        echo ' - ' . date('g:i A', strtotime($workshop['end_date']));
                                                    }
                                                ?>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                </svg>
                                                <?php echo htmlspecialchars($workshop['location']); ?>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                                </svg>
                                                Max: <?php echo htmlspecialchars($workshop['max_participants']); ?> participants
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <a href="workshop_view.php?id=<?php echo $workshop['workshop_id']; ?>" class="text-indigo-600 hover:text-indigo-900">View details</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Promotion Requests Content -->
        <div id="promotions-content" class="mt-6" style="display: none;">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Promotion Requests</h3>
            
            <?php if (empty($promotionRequests)): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <p class="text-gray-500">No promotion requests found.</p>
                    <a href="promotion_request.php" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Submit Promotion Request
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($promotionRequests as $request): ?>
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-amber-500 text-white">
                                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-lg font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($request['first_name'] . ' ' . $request['last_name']); ?>
                                                </h4>
                                                <p class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($request['current_rank'] . ' → ' . $request['requested_rank']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                    switch($request['status']) {
                                                        case 'pending':
                                                            echo 'bg-yellow-100 text-yellow-800';
                                                            break;
                                                        case 'under_review':
                                                            echo 'bg-blue-100 text-blue-800';
                                                            break;
                                                        case 'approved':
                                                            echo 'bg-green-100 text-green-800';
                                                            break;
                                                        case 'denied':
                                                            echo 'bg-red-100 text-red-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-100 text-gray-800';
                                                    }
                                                ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Submitted: <?php echo date('M j, Y', strtotime($request['submission_date'])); ?>
                                            </p>
                                            <?php if (isset($request['reviewer_first_name']) && isset($request['reviewer_last_name'])): ?>
                                                <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                    <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                                    </svg>
                                                    Reviewer: <?php echo htmlspecialchars($request['reviewer_first_name'] . ' ' . $request['reviewer_last_name']); ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <a href="promotion_view.php?id=<?php echo $request['id']; ?>" class="text-indigo-600 hover:text-indigo-900">View details</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Professional Development Plans Content -->
        <div id="plans-content" class="mt-6" style="display: none;">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Professional Development Plans</h3>
            
            <?php if (empty($developmentPlans)): ?>
                <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                    <p class="text-gray-500">No development plans found.</p>
                    <a href="development_plan_create.php" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Create Development Plan
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <?php foreach ($developmentPlans as $plan): ?>
                            <li>
                                <div class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-md bg-green-500 text-white">
                                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-lg font-medium text-gray-900"><?php echo htmlspecialchars($plan['title']); ?></h4>
                                                <p class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($plan['first_name'] . ' ' . $plan['last_name']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php 
                                                    switch($plan['status']) {
                                                        case 'draft':
                                                            echo 'bg-gray-100 text-gray-800';
                                                            break;
                                                        case 'in_progress':
                                                            echo 'bg-blue-100 text-blue-800';
                                                            break;
                                                        case 'completed':
                                                            echo 'bg-green-100 text-green-800';
                                                            break;
                                                        case 'cancelled':
                                                            echo 'bg-red-100 text-red-800';
                                                            break;
                                                        default:
                                                            echo 'bg-gray-100 text-gray-800';
                                                    }
                                                ?>">
                                                <?php echo ucfirst($plan['status']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-2 sm:flex sm:justify-between">
                                        <div class="sm:flex">
                                            <p class="flex items-center text-sm text-gray-500">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Created: <?php echo date('M j, Y', strtotime($plan['created_at'])); ?>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                                                </svg>
                                                By: <?php echo htmlspecialchars($plan['creator_first_name'] . ' ' . $plan['creator_last_name']); ?>
                                            </p>
                                            <p class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0 sm:ml-6">
                                                <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                Timeline: <?php echo htmlspecialchars($plan['timeline']); ?>
                                            </p>
                                        </div>
                                        <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                            <a href="development_plan_view.php?id=<?php echo $plan['id']; ?>" class="text-indigo-600 hover:text-indigo-900">View details</a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php 
// Define script for tab functionality
$pageScripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Get all tab buttons
    const tabWorkshops = document.getElementById("tab-workshops");
    const tabPromotions = document.getElementById("tab-promotions");
    const tabPlans = document.getElementById("tab-plans");
    
    // Get all content divs
    const workshopsContent = document.getElementById("workshops-content");
    const promotionsContent = document.getElementById("promotions-content");
    const plansContent = document.getElementById("plans-content");
    
    // Function to reset all tabs
    function resetTabs() {
        // Hide all content
        workshopsContent.style.display = "none";
        promotionsContent.style.display = "none";
        plansContent.style.display = "none";
        
        // Reset tab styles
        tabWorkshops.classList.remove("border-indigo-500", "text-indigo-600");
        tabWorkshops.classList.add("border-transparent", "text-gray-500");
        
        tabPromotions.classList.remove("border-indigo-500", "text-indigo-600");
        tabPromotions.classList.add("border-transparent", "text-gray-500");
        
        tabPlans.classList.remove("border-indigo-500", "text-indigo-600");
        tabPlans.classList.add("border-transparent", "text-gray-500");
    }
    
    // Set up click handlers for tabs
    tabWorkshops.addEventListener("click", function() {
        resetTabs();
        workshopsContent.style.display = "block";
        this.classList.remove("border-transparent", "text-gray-500");
        this.classList.add("border-indigo-500", "text-indigo-600");
    });
    
    tabPromotions.addEventListener("click", function() {
        resetTabs();
        promotionsContent.style.display = "block";
        this.classList.remove("border-transparent", "text-gray-500");
        this.classList.add("border-indigo-500", "text-indigo-600");
    });
    
    tabPlans.addEventListener("click", function() {
        resetTabs();
        plansContent.style.display = "block";
        this.classList.remove("border-transparent", "text-gray-500");
        this.classList.add("border-indigo-500", "text-indigo-600");
    });
    
    // Show workshops tab by default
    tabWorkshops.click();
});
</script>
';

include 'includes/footer.php'; 
?>