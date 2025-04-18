<?php
// filepath: /opt/lampp/htdocs/fms/event_create.php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $location = $_POST['location'] ?? '';
    
    // Validate inputs
    if (empty($title) || empty($start_date)) {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } else {
        try {
            // Create the events table if it doesn't exist
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS events (
                    event_id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    start_date DATETIME NOT NULL,
                    end_date DATETIME,
                    location VARCHAR(255),
                    created_by INT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
            
            // Insert the event
            $stmt = $pdo->prepare("
                INSERT INTO events (title, description, start_date, end_date, location, created_by)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $title,
                $description,
                $start_date,
                $end_date,
                $location,
                $_SESSION['user_id']
            ]);
            
            // Redirect to dashboard with success message
            $_SESSION['success_message'] = 'Event created successfully.';
            header("Location: dashboard.php");
            exit();
            
        } catch (PDOException $e) {
            $message = 'Database error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$pageTitle = "Create Event";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Create New Event
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Event Form -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <?php if ($message): ?>
                <div class="bg-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-100 border border-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-400 text-<?php echo $messageType === 'error' ? 'red' : 'green'; ?>-700 px-4 py-3 rounded mb-4">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Event Title*</label>
                            <input type="text" id="title" name="title" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date & Time*</label>
                                <input type="datetime-local" id="start_date" name="start_date" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">End Date & Time (Optional)</label>
                                <input type="datetime-local" id="end_date" name="end_date"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                            <input type="text" id="location" name="location"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="4"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="dashboard.php" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>