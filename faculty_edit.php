<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$faculty_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

// Get faculty details
try {
    $stmt = $pdo->prepare("
        SELECT f.*, u.first_name, u.last_name, u.email, d.name as department_name
        FROM faculty f
        JOIN users u ON f.faculty_id = u.user_id
        JOIN departments d ON f.department_id = d.department_id
        WHERE f.faculty_id = ?
    ");
    $stmt->execute([$faculty_id]);
    $faculty = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$faculty) {
        header("Location: faculty.php");
        exit();
    }
    
    // Get all departments for dropdown
    $deptStmt = $pdo->query("SELECT * FROM departments ORDER BY name");
    $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $message = "Database error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Update user record
        $userStmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, email = ?
            WHERE user_id = ?
        ");
        
        $userResult = $userStmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $faculty_id
        ]);
        
        // Update faculty record
        $facultyStmt = $pdo->prepare("
            UPDATE faculty 
            SET department_id = ?, position = ?, rank = ?, 
                tenure_status = ?, bio = ?, office_location = ?
            WHERE faculty_id = ?
        ");
        
        $facultyResult = $facultyStmt->execute([
            $_POST['department_id'],
            $_POST['position'],
            $_POST['rank'],
            $_POST['tenure_status'],
            $_POST['bio'],
            $_POST['office_location'],
            $faculty_id
        ]);
        
        if ($userResult && $facultyResult) {
            header("Location: faculty_view.php?id={$faculty_id}&updated=1");
            exit();
        } else {
            $message = "Error updating faculty member.";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
    }
}

$pageTitle = "Edit Faculty Member";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Edit Faculty: <?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?>
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <?php if ($message): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <form method="POST">
                <div class="px-4 py-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <div class="sm:col-span-3">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
                            <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($faculty['first_name']); ?>" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
                            <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($faculty['last_name']); ?>" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($faculty['email']); ?>" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                            <select id="department_id" name="department_id" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept['department_id']; ?>" <?php echo ($faculty['department_id'] == $dept['department_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($dept['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                            <input type="text" name="position" id="position" value="<?php echo htmlspecialchars($faculty['position']); ?>" required
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="rank" class="block text-sm font-medium text-gray-700">Rank</label>
                            <select id="rank" name="rank" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="assistant" <?php echo ($faculty['rank'] == 'assistant') ? 'selected' : ''; ?>>Assistant Professor</option>
                                <option value="associate" <?php echo ($faculty['rank'] == 'associate') ? 'selected' : ''; ?>>Associate Professor</option>
                                <option value="full" <?php echo ($faculty['rank'] == 'full') ? 'selected' : ''; ?>>Full Professor</option>
                                <option value="emeritus" <?php echo ($faculty['rank'] == 'emeritus') ? 'selected' : ''; ?>>Professor Emeritus</option>
                            </select>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="tenure_status" class="block text-sm font-medium text-gray-700">Tenure Status</label>
                            <select id="tenure_status" name="tenure_status" required
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="tenured" <?php echo ($faculty['tenure_status'] == 'tenured') ? 'selected' : ''; ?>>Tenured</option>
                                <option value="tenure_track" <?php echo ($faculty['tenure_status'] == 'tenure_track') ? 'selected' : ''; ?>>Tenure Track</option>
                                <option value="non_tenure" <?php echo ($faculty['tenure_status'] == 'non_tenure') ? 'selected' : ''; ?>>Non-Tenure</option>
                            </select>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="office_location" class="block text-sm font-medium text-gray-700">Office Location</label>
                            <input type="text" name="office_location" id="office_location" value="<?php echo htmlspecialchars($faculty['office_location'] ?? ''); ?>"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="sm:col-span-6">
                            <label for="bio" class="block text-sm font-medium text-gray-700">Biography</label>
                            <textarea id="bio" name="bio" rows="5"
                                class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"><?php echo htmlspecialchars($faculty['bio'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <a href="faculty_view.php?id=<?php echo $faculty_id; ?>" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
