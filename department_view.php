<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$department_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$department = getDepartmentById($department_id);

if (!$department) {
    header("Location: departments.php");
    exit();
}

// Get faculty in this department
$faculty = $pdo->prepare("
    SELECT f.*, u.first_name, u.last_name, u.email 
    FROM faculty f
    JOIN users u ON f.faculty_id = u.user_id
    WHERE f.department_id = ?
    ORDER BY u.last_name, u.first_name
");
$faculty->execute([$department_id]);
$departmentFaculty = $faculty->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Department Details";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                Department: <?php echo htmlspecialchars($department['name']); ?>
            </h2>
            <div>
                <a href="department_edit.php?id=<?php echo $department_id; ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 mr-2">
                    Edit Department
                </a>
                <a href="departments.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                    Back to List
                </a>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Department Info -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-800">Department Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Department Code</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($department['code']); ?></p>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Department Head</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                <?php if ($department['head_id']): ?>
                                    <a href="faculty_view.php?id=<?php echo $department['head_id']; ?>" class="text-blue-600 hover:underline">
                                        <?php echo htmlspecialchars($department['head_name'] ?? 'Unknown'); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-gray-500 italic">Not assigned</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($department['description'] ?? 'No description available.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Faculty List -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-800">Faculty Members</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            <?php echo count($departmentFaculty); ?> Members
                        </span>
                    </div>
                    <div class="p-6">
                        <?php if (empty($departmentFaculty)): ?>
                            <p class="text-gray-500">No faculty members in this department.</p>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-200">
                                <?php foreach ($departmentFaculty as $member): ?>
                                    <li class="py-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-gray-500 font-medium"><?php echo strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)); ?></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                                </p>
                                                <p class="text-sm text-gray-500 truncate">
                                                    <?php echo htmlspecialchars($member['position'] . ' • ' . ucfirst($member['rank'])); ?>
                                                </p>
                                            </div>
                                            <div>
                                                <a href="faculty_view.php?id=<?php echo $member['faculty_id']; ?>" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>