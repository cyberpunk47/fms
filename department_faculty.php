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

$faculty = $pdo->prepare("
    SELECT f.*, u.first_name, u.last_name, u.email, u.created_at
    FROM faculty f
    JOIN users u ON f.faculty_id = u.user_id
    WHERE f.department_id = ?
    ORDER BY u.last_name, u.first_name
");
$faculty->execute([$department_id]);
$departmentFaculty = $faculty->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Department Faculty";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 sm:ml-64">

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                Faculty in <?php echo htmlspecialchars($department['name']); ?> Department
            </h2>
            <div>
                <a href="faculty_create.php?department_id=<?php echo $department_id; ?>" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 mr-2">
                    Add Faculty
                </a>
                <a href="departments.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                    Back to Departments
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Position
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rank
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tenure Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hire Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($departmentFaculty)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                        No faculty members in this department. <a href="faculty_create.php?department_id=<?php echo $department_id; ?>" class="text-blue-600 hover:underline">Add a faculty member</a>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($departmentFaculty as $member): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($member['email']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($member['position']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst(htmlspecialchars($member['rank'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($member['tenure_status']))); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($member['hire_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="faculty_view.php?id=<?php echo $member['faculty_id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="faculty_edit.php?id=<?php echo $member['faculty_id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a href="evaluations.php?faculty_id=<?php echo $member['faculty_id']; ?>" class="text-green-600 hover:text-green-900">Evaluations</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>