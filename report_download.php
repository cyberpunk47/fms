<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Check if report parameters exist in session
if (!isset($_SESSION['report_params'])) {
    header("Location: reports.php");
    exit();
}

$params = $_SESSION['report_params'];
$reportType = $params['report_type'] ?? 'custom';
$reportFormat = $params['report_format'] ?? 'pdf';
$reportName = $params['report_name'] ?? 'Report';

// Set appropriate headers based on report format
switch ($reportFormat) {
    case 'csv':
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $reportName . '.csv"');
        generateCSVReport($params);
        break;
        
    case 'excel':
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $reportName . '.xls"');
        generateExcelReport($params);
        break;
        
    case 'pdf':
    default:
        // For PDF, show a preview page instead of direct download
        $pageTitle = "Report Preview";
        include 'includes/header.php';
        include 'includes/sidebar.php';
        showPDFPreview($params);
        include 'includes/footer.php';
        break;
}

// Function to generate CSV report
function generateCSVReport($params) {
    $reportType = $params['report_type'];
    $output = fopen('php://output', 'w');
    
    // Output headers based on report type
    switch ($reportType) {
        case 'faculty':
            fputcsv($output, ['ID', 'Name', 'Department', 'Position', 'Rank', 'Tenure Status', 'Hire Date']);
            
            // Generate query based on filters
            $departmentFilter = !empty($params['department_id']) ? " AND f.department_id = " . intval($params['department_id']) : "";
            $rankFilter = !empty($params['rank']) ? " AND f.rank = '" . $params['rank'] . "'" : "";
            
            // Query database for faculty data
            global $pdo;
            $sql = "SELECT 
                        f.faculty_id,
                        CONCAT(u.first_name, ' ', u.last_name) as name,
                        d.name as department,
                        f.position,
                        f.rank,
                        f.tenure_status,
                        f.hire_date
                    FROM faculty f
                    JOIN users u ON f.faculty_id = u.user_id
                    JOIN departments d ON f.department_id = d.department_id
                    WHERE 1=1 $departmentFilter $rankFilter
                    ORDER BY u.last_name, u.first_name";
            
            try {
                $stmt = $pdo->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    fputcsv($output, $row);
                }
            } catch (PDOException $e) {
                fputcsv($output, ['Error', $e->getMessage()]);
            }
            break;
            
        // Add other report types here
        default:
            fputcsv($output, ['Report Type', 'Sample Data']);
            fputcsv($output, ['Sample', 'Data']);
            break;
    }
    
    fclose($output);
}

// Function to generate Excel report (simplified, uses HTML tables for basic Excel)
function generateExcelReport($params) {
    echo "<table border='1'>";
    echo "<tr><th colspan='5'>$params[report_name]</th></tr>";
    echo "<tr><th>Date Range</th><td colspan='4'>From: " . ($params['date_from'] ?? 'All time') . " To: " . ($params['date_to'] ?? 'Present') . "</td></tr>";
    echo "<tr><th>ID</th><th>Name</th><th>Department</th><th>Position</th><th>Date</th></tr>";
    
    // Add sample data rows
    for ($i = 1; $i <= 10; $i++) {
        echo "<tr><td>$i</td><td>Sample Name $i</td><td>Department</td><td>Position</td><td>" . date('Y-m-d') . "</td></tr>";
    }
    
    echo "</table>";
}

// Function to show PDF preview
function showPDFPreview($params) {
    ?>
    <div class="flex-1 sm:ml-64">
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">
                    <?php echo htmlspecialchars($params['report_name']); ?> - Preview
                </h2>
                <div>
                    <a href="reports.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300 mr-2">
                        Back to Reports
                    </a>
                    <a href="#" onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                        Print Report
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                    <h1 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($params['report_name']); ?></h1>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">Generated on <?php echo date('F j, Y'); ?></p>
                </div>
                
                <!-- Report Content -->
                <div class="px-4 py-5 sm:px-6">
                    <div class="bg-gray-50 p-4 mb-6 rounded-md">
                        <h2 class="text-lg font-medium text-gray-900">Report Parameters</h2>
                        <dl class="mt-2 grid grid-cols-1 gap-x-4 gap-y-2 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Report Type</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo ucfirst($params['report_type']); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date Range</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php echo $params['date_from'] ?? 'All time'; ?> to <?php echo $params['date_to'] ?? 'Present'; ?>
                                </dd>
                            </div>
                        </dl>
                    </div>
                    
                    <!-- Sample Report Data -->
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Report Data</h2>
                    
                    <?php if ($params['report_type'] === 'faculty'): ?>
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenure Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                // Get actual faculty data from database
                                global $pdo;
                                
                                $departmentFilter = !empty($params['department_id']) ? " AND f.department_id = " . intval($params['department_id']) : "";
                                $rankFilter = !empty($params['rank']) ? " AND f.rank = '" . $params['rank'] . "'" : "";
                                
                                $sql = "SELECT 
                                            f.faculty_id,
                                            u.first_name,
                                            u.last_name,
                                            d.name as department,
                                            f.position,
                                            f.rank,
                                            f.tenure_status
                                        FROM faculty f
                                        JOIN users u ON f.faculty_id = u.user_id
                                        JOIN departments d ON f.department_id = d.department_id
                                        WHERE 1=1 $departmentFilter $rankFilter
                                        ORDER BY u.last_name, u.first_name
                                        LIMIT 20";
                                
                                try {
                                    $stmt = $pdo->query($sql);
                                    $facultyData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($facultyData) > 0) {
                                        foreach ($facultyData as $faculty) {
                                            echo '<tr>';
                                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">' . htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']) . '</td>';
                                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($faculty['department']) . '</td>';
                                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . htmlspecialchars($faculty['position']) . '</td>';
                                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . ucfirst(htmlspecialchars($faculty['rank'])) . '</td>';
                                            echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . str_replace('_', ' ', ucfirst(htmlspecialchars($faculty['tenure_status']))) . '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No faculty members found matching the criteria.</td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    echo '<tr><td colspan="5" class="px-6 py-4 text-center text-red-500">Error retrieving data: ' . $e->getMessage() . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-gray-500">Sample report content for <?php echo ucfirst($params['report_type']); ?> report.</p>
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php for ($i = 1; $i <= 10; $i++): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $i; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Item <?php echo $i; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo ucfirst($params['report_type']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('Y-m-d', strtotime('-' . $i . ' days')); ?></td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                    <?php if (isset($params['include_charts']) && $params['include_charts']): ?>
                        <div class="mt-8">
                            <h2 class="text-lg font-medium text-gray-900 mb-4">Data Visualization</h2>
                            <div class="bg-gray-100 p-6 rounded-lg text-center">
                                <p class="text-gray-500">[Chart visualization would appear here in a real report]</p>
                                <div class="h-64 flex items-center justify-center">
                                    <div class="bg-gray-300 h-40 w-64 rounded flex items-center justify-center">
                                        <span class="text-gray-600">Sample Chart</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    <?php
}
?>