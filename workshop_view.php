<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$workshop_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get workshop details
try {
    $stmt = $pdo->prepare("
        SELECT w.*, d.name as department_name, COUNT(r.registration_id) as registered_count
        FROM workshops w
        LEFT JOIN departments d ON w.department_id = d.department_id
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
    
    // Get registered participants
    $participantsStmt = $pdo->prepare("
        SELECT r.*, CONCAT(u.first_name, ' ', u.last_name) as participant_name, u.email,
               d.name as department_name
        FROM workshop_registrations r
        JOIN users u ON r.user_id = u.user_id
        LEFT JOIN faculty f ON u.user_id = f.faculty_id
        LEFT JOIN departments d ON f.department_id = d.department_id
        WHERE r.workshop_id = ?
        ORDER BY r.registration_date
    ");
    $participantsStmt->execute([$workshop_id]);
    $participants = $participantsStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$pageTitle = "Workshop Details";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                Workshop: <?php echo htmlspecialchars($workshop['title']); ?>
            </h2>
            <div>
                <?php if (strtotime($workshop['start_date']) > time()): ?>
                    <a href="workshop_register.php?id=<?php echo $workshop_id; ?>" class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 mr-2">
                        Register
                    </a>
                <?php endif; ?>
                <a href="development.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                    Back
                </a>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Workshop Information -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-800">Workshop Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php 
                                    echo date('M j, Y, g:i a', strtotime($workshop['start_date'])); 
                                    if ($workshop['end_date']) {
                                        echo ' - ';
                                        if (date('Y-m-d', strtotime($workshop['start_date'])) == date('Y-m-d', strtotime($workshop['end_date']))) {
                                            // Same day, just show end time
                                            echo date('g:i a', strtotime($workshop['end_date']));
                                        } else {
                                            // Different days, show full date/time
                                            echo date('M j, Y, g:i a', strtotime($workshop['end_date']));
                                        }
                                    }
                                    ?>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Location</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($workshop['location']); ?></dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Facilitator</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($workshop['facilitator']); ?></dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($workshop['department_name'] ?? 'All Departments'); ?></dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Registration</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <?php echo $workshop['registered_count']; ?> / <?php echo $workshop['max_participants']; ?> participants
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm">
                                    <?php 
                                    $status_color = 'gray';
                                    $status_text = ucfirst($workshop['status']);
                                    
                                    if ($workshop['status'] == 'scheduled') {
                                        $status_color = 'blue';
                                    } elseif ($workshop['status'] == 'in_progress') {
                                        $status_color = 'green';
                                    } elseif ($workshop['status'] == 'completed') {
                                        $status_color = 'indigo';
                                    } elseif ($workshop['status'] == 'cancelled') {
                                        $status_color = 'red';
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-<?php echo $status_color; ?>-100 text-<?php echo $status_color; ?>-800">
                                        <?php echo $status_text; ?>
                                    </span>
                                </dd>
                            </div>
                        </dl>
                        
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500">Description</h4>
                            <div class="mt-1 text-sm text-gray-900">
                                <?php echo nl2br(htmlspecialchars($workshop['description'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Participants -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-800">Participants</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                            <?php echo count($participants); ?> Registered
                        </span>
                    </div>
                    <div class="p-6">
                        <?php if (empty($participants)): ?>
                            <p class="text-gray-500">No participants registered yet.</p>
                        <?php else: ?>
                            <ul class="divide-y divide-gray-200">
                                <?php foreach ($participants as $participant): ?>
                                    <li class="py-3">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-xs font-medium text-gray-600">
                                                    <?php 
                                                    $name_parts = explode(' ', $participant['participant_name']);
                                                    echo strtoupper(substr($name_parts[0], 0, 1) . (isset($name_parts[1]) ? substr($name_parts[1], 0, 1) : '')); 
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($participant['participant_name']); ?>
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    <?php echo htmlspecialchars($participant['department_name'] ?? 'Unknown Department'); ?>
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0 text-xs text-gray-500">
                                                <?php echo date('M j, Y', strtotime($participant['registration_date'])); ?>
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