<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['report_params'] = $_POST;
    header("Location: report_download.php");
    exit();
}

$reportType = isset($_GET['type']) ? $_GET['type'] : 'custom';
$reportTitle = '';

switch ($reportType) {
    case 'faculty':
        $reportTitle = 'Faculty Report';
        break;
    case 'department':
        $reportTitle = 'Department Analytics';
        break;
    default:
        $reportTitle = 'Custom Report';
}
try {
    $departments = $pdo->query("SELECT department_id, name FROM departments ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $departments = [];
}
if($reportType==='department'){
    header("Location: department_wise.php");
}
$pageTitle = "Generate " . $reportTitle;
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="flex-1 sm:ml-64">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                Generate <?php echo $reportTitle; ?>
            </h2>
            <a href="reports.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                Back to Reports
            </a>
        </div>
    </header>
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Report Parameters</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Configure the parameters for your <?php echo strtolower($reportTitle); ?>.</p>
            </div>
            
            <div class="border-t border-gray-200 px-4 py-5 sm:px-6">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="report_type" value="<?php echo $reportType; ?>">
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label for="report_name" class="block text-sm font-medium text-gray-700">Report Name</label>
                            <input type="text" name="report_name" id="report_name" 
                                value="<?php echo $reportTitle . ' - ' . date('F Y'); ?>" 
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="report_format" class="block text-sm font-medium text-gray-700">Report Format</label>
                            <select id="report_format" name="report_format" 
                                class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="pdf">PDF Document</option>
                                <option value="excel">Excel Spreadsheet</option>
                                <option value="csv">CSV Data File</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">Date From</label>
                            <input type="date" name="date_from" id="date_from" 
                                value="<?php echo date('Y-m-d', strtotime('-30 days')); ?>"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                            
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">Date To</label>
                            <input type="date" name="date_to" id="date_to" 
                                value="<?php echo date('Y-m-d'); ?>"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                        
                        <?php if ($reportType === 'faculty'): ?>
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="department_id" name="department_id"
                                    class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Departments</option>
                                    <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['department_id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="rank" class="block text-sm font-medium text-gray-700">Faculty Rank</label>
                                <select id="rank" name="rank"
                                    class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">All Ranks</option>
                                    <option value="assistant">Assistant Professor</option>
                                    <option value="associate">Associate Professor</option>
                                    <option value="full">Full Professor</option>
                                    <option value="emeritus">Professor Emeritus</option>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex justify-end">
                        <a href="reports.php" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>