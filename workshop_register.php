<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$workshop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Get workshop details
try {
    $stmt = $pdo->prepare("
        SELECT w.*, COUNT(r.registration_id) as registered_count
        FROM workshops w
        LEFT JOIN workshop_registrations r ON w.workshop_id = r.workshop_id
        WHERE w.workshop_id = ?
        GROUP BY w.workshop_id
    ");
    $stmt->execute([$workshop_id]);
    $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$workshop) {
        header("Location: development.php");
        exit();
    }
    
    // Check if user is already registered
    $checkStmt = $pdo->prepare("SELECT * FROM workshop_registrations WHERE workshop_id = ? AND user_id = ?");
    $checkStmt->execute([$workshop_id, $user_id]);
    $existingRegistration = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    // Process registration if form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        // Check if workshop is full
        if ($workshop['registered_count'] >= $workshop['max_participants']) {
            $message = "Sorry, this workshop is already at full capacity.";
            $messageType = "error";
        } 
        // Check if user is already registered
        else if ($existingRegistration) {
            $message = "You are already registered for this workshop.";
            $messageType = "info";
        } 
        // Register the user
        else {
            try {
                $regStmt = $pdo->prepare("
                    INSERT INTO workshop_registrations (workshop_id, user_id, registration_date, status, notes)
                    VALUES (?, ?, NOW(), 'confirmed', ?)
                ");
                
                $notes = $_POST['notes'] ?? '';
                $result = $regStmt->execute([$workshop_id, $user_id, $notes]);
                
                if ($result) {
                    $message = "You have successfully registered for this workshop.";
                    $messageType = "success";
                    
                    // Re-check registration status
                    $checkStmt->execute([$workshop_id, $user_id]);
                    $existingRegistration = $checkStmt->fetch(PDO::FETCH_ASSOC);
                } else {
                    $message = "Registration failed. Please try again.";
                    $messageType = "error";
                }
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $messageType = "error";
            }
        }
    }
    
    // Process cancellation if requested
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel'])) {
        if ($existingRegistration) {
            try {
                $cancelStmt = $pdo->prepare("DELETE FROM workshop_registrations WHERE workshop_id = ? AND user_id = ?");
                $result = $cancelStmt->execute([$workshop_id, $user_id]);
                
                if ($result) {
                    $message = "Your registration has been cancelled.";
                    $messageType = "info";
                    
                    // Update registration status
                    $existingRegistration = null;
                } else {
                    $message = "Cancellation failed. Please try again.";
                    $messageType = "error";
                }
            } catch (PDOException $e) {
                $message = "Database error: " . $e->getMessage();
                $messageType = "error";
            }
        } else {
            $message = "You are not registered for this workshop.";
            $messageType = "error";
        }
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$pageTitle = "Workshop Registration";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Workshop Registration: <?php echo htmlspecialchars($workshop['title']); ?>
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
                
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Workshop Details</h3>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                            <dd class="text-sm text-gray-900">
                                <?php 
                                echo date('M j, Y, g:i a', strtotime($workshop['start_date'])); 
                                if ($workshop['end_date']) {
                                    echo ' - ';
                                    if (date('Y-m-d', strtotime($workshop['start_date'])) == date('Y-m-d', strtotime($workshop['end_date']))) {
                                        echo date('g:i a', strtotime($workshop['end_date']));
                                    } else {
                                        echo date('M j, Y, g:i a', strtotime($workshop['end_date']));
                                    }
                                }
                                ?>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Location</dt>
                            <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($workshop['location']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Facilitator</dt>
                            <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($workshop['facilitator']); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Available Spots</dt>
                            <dd class="text-sm text-gray-900">
                                <?php 
                                $available = $workshop['max_participants'] - $workshop['registered_count'];
                                echo $available > 0 ? $available : 'Full';
                                ?> 
                                (<?php echo $workshop['registered_count']; ?>/<?php echo $workshop['max_participants']; ?> registered)
                            </dd>
                        </div>
                    </dl>
                </div>
                
                <div class="border-t border-gray-200 pt-6">
                    <?php if ($existingRegistration): ?>
                        <!-- Already registered -->
                        <div class="bg-green-50 p-4 rounded-md mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Registration Confirmed</h3>
                                    <div class="mt-2 text-sm text-green-700">
                                        <p>You are registered for this workshop. Your registration date was <?php echo date('F j, Y', strtotime($existingRegistration['registration_date'])); ?>.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cancel registration -->
                        <form method="POST" class="mt-4">
                            <p class="text-sm text-gray-600 mb-4">If you can no longer attend this workshop, you can cancel your registration:</p>
                            <div class="flex justify-end">
                                <a href="workshop_view.php?id=<?php echo $workshop_id; ?>" class="mr-2 bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Back to Workshop
                                </a>
                                <button type="submit" name="cancel" class="bg-red-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Cancel Registration
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <?php if ($workshop['registered_count'] < $workshop['max_participants'] && strtotime($workshop['start_date']) > time()): ?>
                            <!-- Registration form -->
                            <form method="POST" class="space-y-6">
                                <div>
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes (Optional)</label>
                                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Please mention any specific requirements or questions you have about the workshop.</p>
                                </div>
                                
                                <div class="flex justify-end">
                                    <a href="workshop_view.php?id=<?php echo $workshop_id; ?>" class="mr-2 bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Back to Workshop
                                    </a>
                                    <button type="submit" name="register" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Register for Workshop
                                    </button>
                                </div>
                            </form>
                        <?php elseif ($workshop['registered_count'] >= $workshop['max_participants']): ?>
                            <!-- Workshop is full -->
                            <div class="bg-yellow-50 p-4 rounded-md mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-yellow-800">Workshop Full</h3>
                                        <div class="mt-2 text-sm text-yellow-700">
                                            <p>This workshop has reached its maximum capacity of <?php echo $workshop['max_participants']; ?> participants. Please check back later as spots may open up if someone cancels.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <a href="workshop_view.php?id=<?php echo $workshop_id; ?>" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Back to Workshop
                                </a>
                            </div>
                        <?php elseif (strtotime($workshop['start_date']) <= time()): ?>
                            <!-- Workshop has started/passed -->
                            <div class="bg-red-50 p-4 rounded-md mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Registration Closed</h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <p>This workshop has already started or has passed. Registration is no longer available.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <a href="workshop_view.php?id=<?php echo $workshop_id; ?>" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Back to Workshop
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>