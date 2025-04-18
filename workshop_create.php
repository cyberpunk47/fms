<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$message = '';
$departments = getAllDepartments();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    try {
        $workshopData = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'location' => $_POST['location'] ?? '',
            'max_participants' => $_POST['max_participants'] ?? 0,
            'facilitator' => $_POST['facilitator'] ?? '',
            'department_id' => !empty($_POST['department_id']) ? $_POST['department_id'] : null,
            'created_by' => $_SESSION['user_id'],
            'status' => 'scheduled'
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO workshops (
                title, description, start_date, end_date, location, max_participants, 
                facilitator, department_id, created_by, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $workshopData['title'],
            $workshopData['description'],
            $workshopData['start_date'],
            $workshopData['end_date'],
            $workshopData['location'],
            $workshopData['max_participants'],
            $workshopData['facilitator'],
            $workshopData['department_id'],
            $workshopData['created_by'],
            $workshopData['status']
        ]);
        
        if ($result) {
            // Record in audit log
            $workshop_id = $pdo->lastInsertId();
            recordAuditTrail($_SESSION['user_id'], 'INSERT', 'workshops', $workshop_id, null, $workshopData);
            
            header("Location: development.php?success=1");
            exit();
        } else {
            $message = 'Error adding workshop. Please try again.';
        }
    } catch (PDOException $e) {
        $message = 'Database error: ' . $e->getMessage();
    }
}

$pageTitle = "Schedule New Workshop";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Schedule New Workshop
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Workshop Form -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <?php if ($message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Workshop Title</label>
                            <input type="text" id="title" name="title" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="facilitator" class="block text-sm font-medium text-gray-700">Facilitator</label>
                            <input type="text" id="facilitator" name="facilitator" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="datetime-local" id="start_date" name="start_date" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="datetime-local" id="end_date" name="end_date" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" id="location" name="location" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="max_participants" class="block text-sm font-medium text-gray-700">Maximum Participants</label>
                            <input type="number" id="max_participants" name="max_participants" min="1" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700">Department (Optional)</label>
                            <select id="department_id" name="department_id"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo $dept['department_id']; ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea id="description" name="description" rows="4" required
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="development.php" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Schedule Workshop
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>