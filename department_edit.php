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

$faculty = getAllFaculty(); 
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $departmentData = [
        'name' => $_POST['name'] ?? '',
        'code' => $_POST['code'] ?? '',
        'description' => $_POST['description'] ?? '',
        'head_id' => !empty($_POST['head_id']) ? $_POST['head_id'] : null
    ];
   
    $oldValues = [
        'name' => $department['name'],
        'code' => $department['code'],
        'description' => $department['description'],
        'head_id' => $department['head_id']
    ];
    
    try {
        $stmt = $pdo->prepare("
            UPDATE departments 
            SET name = ?, code = ?, description = ?, head_id = ?
            WHERE department_id = ?
        ");
        
        $result = $stmt->execute([
            $departmentData['name'],
            $departmentData['code'],
            $departmentData['description'],
            $departmentData['head_id'],
            $department_id
        ]);
        
        if ($result) {
            // Record in audit log
            recordAuditTrail($_SESSION['user_id'], 'UPDATE', 'departments', $department_id, $oldValues, $departmentData);
            
            header("Location: department_view.php?id={$department_id}&updated=1");
            exit();
        } else {
            $message = 'Error updating department. Please try again.';
        }
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
    }
}

$pageTitle = "Edit Department";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 sm:ml-64">

    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Edit Department: <?php echo htmlspecialchars($department['name']); ?>
            </h2>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
   
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <?php if ($message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Department Name</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($department['name']); ?>" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Department Code</label>
                            <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($department['code']); ?>" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="head_id" class="block text-sm font-medium text-gray-700">Department Head</label>
                            <select id="head_id" name="head_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Department Head (Optional)</option>
                                <?php foreach ($faculty as $member): ?>
                                <option value="<?php echo $member['faculty_id']; ?>" <?php echo ($department['head_id'] == $member['faculty_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($department['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="department_view.php?id=<?php echo $department_id; ?>" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Department
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>