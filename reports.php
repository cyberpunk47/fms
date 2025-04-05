<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Get statistics
$stats = getDashboardStats();

// Map the array keys to what the reports page expects (same as in dashboard.php)
$stats['facultyCount'] = $stats['total_faculty'] ?? 0;
$stats['departmentCount'] = $stats['total_departments'] ?? 0;
$stats['evaluationCount'] = $stats['total_evaluations'] ?? 0;
$stats['pendingEvaluations'] = $stats['pending_evaluations'] ?? 0;
$stats['developmentCount'] = $stats['development_programs'] ?? 0;
$stats['upcomingDevelopment'] = $stats['pending_promotions'] ?? 0;

$pageTitle = "Reports";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                Reports & Analytics
            </h2>
            <a href="report_generate.php" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                Generate Custom Report
            </a>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Quick Stats -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Summary Statistics</h3>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Faculty</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['facultyCount']; ?></dd>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Departments</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['departmentCount']; ?></dd>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Completed Evaluations</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['evaluationCount']; ?></dd>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-sm font-medium text-gray-500 truncate">Development Programs</dt>
                        <dd class="mt-1 text-3xl font-semibold text-gray-900"><?php echo $stats['developmentCount']; ?></dd>
                    </div>
                </div>
            </dl>
        </div>
        
        <!-- Available Reports -->
        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Reports</h3>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                <li>
                    <a href="report_generate.php?type=faculty" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-md flex items-center justify-center">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-indigo-600">Faculty Report</div>
                                        <div class="text-sm text-gray-500">Comprehensive report on faculty demographics, ranks, and tenure status</div>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="report_generate.php?type=evaluation" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-indigo-600">Evaluation Analysis</div>
                                        <div class="text-sm text-gray-500">Performance metrics and trends across departments and academic periods</div>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="report_generate.php?type=department" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-md flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-indigo-600">Department Analytics</div>
                                        <div class="text-sm text-gray-500">Departmental performance, faculty distribution, and evaluation outcomes</div>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="report_generate.php?type=development" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-yellow-100 rounded-md flex items-center justify-center">
                                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-indigo-600">Development & Growth Analysis</div>
                                        <div class="text-sm text-gray-500">Professional development participation and promotion metrics</div>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="#" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-md flex items-center justify-center">
                                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-indigo-600">Compliance Report</div>
                                        <div class="text-sm text-gray-500">Regulatory compliance status and required actions</div>
                                    </div>
                                </div>
                                <div class="ml-2 flex-shrink-0">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Coming Soon</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Recent Report Activity -->
        <h3 class="text-lg font-medium text-gray-900 mt-8 mb-4">Recently Generated Reports</h3>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-900">
                            <span class="font-medium">Faculty Demographics Report</span>
                            <span class="text-gray-500 ml-1">- Generated on April 2, 2025</span>
                        </div>
                        <div>
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Download</a>
                        </div>
                    </div>
                </li>
                
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-900">
                            <span class="font-medium">Spring 2025 Evaluation Summary</span>
                            <span class="text-gray-500 ml-1">- Generated on March 28, 2025</span>
                        </div>
                        <div>
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Download</a>
                        </div>
                    </div>
                </li>
                
                <li class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-900">
                            <span class="font-medium">Computer Science Department Analysis</span>
                            <span class="text-gray-500 ml-1">- Generated on March 15, 2025</span>
                        </div>
                        <div>
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Download</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>