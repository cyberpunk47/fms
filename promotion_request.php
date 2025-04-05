<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';
require_once 'includes/promotion_handler.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageType = '';

// Get current faculty info
if ($_SESSION['user_role'] === 'faculty') {
    $faculty_id = $_SESSION['user_id'];
    
    try {
        $stmt = $mysqli->prepare("
            SELECT f.*, u.first_name, u.last_name, u.email, d.name as department_name
            FROM faculty f
            JOIN users u ON f.faculty_id = u.user_id
            JOIN departments d ON f.department_id = d.department_id
            WHERE f.faculty_id = ?
        ");
        $stmt->bind_param("i", $faculty_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $faculty = $result->fetch_assoc();
        
        if (!$faculty) {
            header("Location: dashboard.php");
            exit();
        }
    } catch (Exception $e) {
        $message = "Error retrieving faculty information: " . $e->getMessage();
        $messageType = "error";
    }
} else {
    // Admin or department head selects a faculty member
    $faculty_id = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : 0;
    
    if ($faculty_id > 0) {
        try {
            $stmt = $mysqli->prepare("
                SELECT f.*, u.first_name, u.last_name, u.email, d.name as department_name
                FROM faculty f
                JOIN users u ON f.faculty_id = u.user_id
                JOIN departments d ON f.department_id = d.department_id
                WHERE f.faculty_id = ?
            ");
            $stmt->bind_param("i", $faculty_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $faculty = $result->fetch_assoc();
            
            if (!$faculty) {
                header("Location: faculty.php");
                exit();
            }
        } catch (Exception $e) {
            $message = "Error retrieving faculty information: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        // Need to select a faculty member
        try {
            $stmt = $mysqli->prepare("
                SELECT f.faculty_id, u.first_name, u.last_name, f.rank
                FROM faculty f 
                JOIN users u ON f.faculty_id = u.user_id
                ORDER BY u.last_name, u.first_name
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            $allFaculty = $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            $message = "Error retrieving faculty list: " . $e->getMessage();
            $messageType = "error";
            $allFaculty = [];
        }
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $request_faculty_id = $_POST['faculty_id'] ?? $faculty_id;
    $current_rank = $_POST['current_rank'];
    $requested_rank = $_POST['requested_rank'];
    $justification = $_POST['justification'];
    
    $result = processPromotionRequest($request_faculty_id, $current_rank, $requested_rank, $justification);
    
    $message = $result['message'];
    $messageType = $result['success'] ? 'success' : 'error';
    
    if ($result['success']) {
        // Redirect after short delay
        header("Refresh: 2; URL=development.php");
    }
}

$pageTitle = "Submit Promotion Request";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Submit Promotion Request
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <?php if ($message): ?>
                    <div class="mb-4 p-4 rounded-md <?php 
                        if ($messageType === 'success') echo 'bg-green-50 text-green-800';
                        elseif ($messageType === 'error') echo 'bg-red-50 text-red-800';
                        else echo 'bg-blue-50 text-blue-800';
                    ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($faculty)): ?>
                    <!-- Faculty information display -->
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Faculty Information</h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?></dd>
                            </div>
                            <div></div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($faculty['email']); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($faculty['department_name']); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Current Rank</dt>
                                <dd class="text-sm text-gray-900"><?php echo ucfirst(htmlspecialchars($faculty['rank'])); ?></dd>
                            </div>
                        </dl>
                    </div>
                    
                    <!-- Promotion Request Form -->
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="faculty_id" value="<?php echo $faculty['faculty_id']; ?>">
                        <input type="hidden" name="current_rank" value="<?php echo $faculty['rank']; ?>">
                        
                        <div>
                            <label for="requested_rank" class="block text-sm font-medium text-gray-700">Requested Rank</label>
                            <select id="requested_rank" name="requested_rank" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">Select Rank</option>
                                <?php if ($faculty['rank'] === 'assistant'): ?>
                                    <option value="associate">Associate Professor</option>
                                <?php elseif ($faculty['rank'] === 'associate'): ?>
                                    <option value="full">Full Professor</option>
                                <?php elseif ($faculty['rank'] === 'full'): ?>
                                    <option value="emeritus">Professor Emeritus</option>
                                <?php else: ?>
                                    <option value="assistant">Assistant Professor</option>
                                    <option value="associate">Associate Professor</option>
                                    <option value="full">Full Professor</option>
                                    <option value="emeritus">Professor Emeritus</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="justification" class="block text-sm font-medium text-gray-700">Justification</label>
                            <textarea id="justification" name="justification" rows="6" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            <p class="mt-1 text-xs text-gray-500">Provide detailed information supporting this promotion request. Include academic achievements, publications, teaching excellence, service contributions, and any other relevant qualifications.</p>
                        </div>
                        
                        <div class="flex justify-end"></div>
                            <a href="development.php" class="mr-2 bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Cancel
                            </a>
                            <button type="submit" name="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Submit Request
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Faculty Selection Form (for admin users) -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Faculty Member</h3>
                        <p class="text-sm text-gray-600 mb-4">Please select the faculty member for whom you'd like to submit a promotion request:</p>
                        
                        <form action="promotion_request.php" method="GET" class="max-w-lg">
                            <div class="flex"></div>
                                <select name="faculty_id" required class="flex-1 border border-gray-300 rounded-l-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Select Faculty Member</option>
                                    <?php foreach ($allFaculty as $f): ?>
                                        <option value="<?php echo $f['faculty_id']; ?>">
                                            <?php echo htmlspecialchars($f['first_name'] . ' ' . $f['last_name'] . ' (' . ucfirst($f['rank']) . ')'); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-r-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Continue
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="flex"></div>
                        <a href="development.php" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Back to Development
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
