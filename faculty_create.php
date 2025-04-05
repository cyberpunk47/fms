<?php
// filepath: /opt/lampp/htdocs/fms/faculty_create.php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$departments = getAllDepartments();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    $userData = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'password' => $_POST['password'] ?? ''
    ];

    $facultyData = [
        'department_id' => $_POST['department_id'] ?? '',
        'position' => $_POST['position'] ?? '',
        'rank' => $_POST['rank'] ?? '',
        'hire_date' => $_POST['hire_date'] ?? '',
        'tenure_status' => $_POST['tenure_status'] ?? '',
        'bio' => $_POST['bio'] ?? '',
        'office_location' => $_POST['office_location'] ?? ''
    ];

    try {
        $result = addFaculty($userData, $facultyData);
        if ($result) {
            // Record in audit log
            recordAuditTrail($_SESSION['user_id'], 'INSERT', 'faculty', $result, null, $facultyData);
            header("Location: faculty.php?success=1");
            exit();
        } else {
            $message = 'Error adding faculty member. Please ensure all required fields are filled correctly.';
        }
    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}

$pageTitle = "Add New Faculty";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Add New Faculty
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Faculty Form -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <?php if ($message): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- User Information Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                            
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                                <input type="text" id="first_name" name="first_name" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                                <input type="text" id="last_name" name="last_name" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <input type="password" id="password" name="password" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <!-- Faculty Information Section -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Faculty Information</h3>
                            
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                <select id="department_id" name="department_id" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['department_id']; ?>">
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700">Position</label>
                                <input type="text" id="position" name="position" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="rank" class="block text-sm font-medium text-gray-700">Rank</label>
                                <select id="rank" name="rank" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Rank</option>
                                    <option value="assistant">Assistant Professor</option>
                                    <option value="associate">Associate Professor</option>
                                    <option value="full">Full Professor</option>
                                    <option value="emeritus">Professor Emeritus</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="hire_date" class="block text-sm font-medium text-gray-700">Hire Date</label>
                                <input type="date" id="hire_date" name="hire_date" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label for="tenure_status" class="block text-sm font-medium text-gray-700">Tenure Status</label>
                                <select id="tenure_status" name="tenure_status" required
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Tenure Status</option>
                                    <option value="tenured">Tenured</option>
                                    <option value="tenure_track">Tenure Track</option>
                                    <option value="non_tenure">Non-Tenure</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label for="office_location" class="block text-sm font-medium text-gray-700">Office Location</label>
                        <input type="text" id="office_location" name="office_location"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Biography</label>
                        <textarea id="bio" name="bio" rows="4"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <a href="faculty.php" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Add Faculty
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>