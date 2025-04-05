<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$evaluation_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get evaluation details
try {
    $stmt = $pdo->prepare("
        SELECT e.*, 
               CONCAT(f_user.first_name, ' ', f_user.last_name) as faculty_name,
               CONCAT(e_user.first_name, ' ', e_user.last_name) as evaluator_name,
               d.name as department_name
        FROM evaluations e
        JOIN faculty f ON e.faculty_id = f.faculty_id
        JOIN users f_user ON f.faculty_id = f_user.user_id
        JOIN users e_user ON e.evaluator_id = e_user.user_id
        JOIN departments d ON f.department_id = d.department_id
        WHERE e.evaluation_id = ?
    ");
    $stmt->execute([$evaluation_id]);
    $evaluation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$evaluation) {
        header("Location: evaluations.php");
        exit();
    }
    
    // Get evaluation metrics and scores
    try {
        $scoreStmt = $pdo->prepare("
            SELECT m.metric_id, m.name as metric_name, m.description, m.weight,
                   s.score, s.comments
            FROM evaluation_scores s
            JOIN performance_metrics m ON s.metric_id = m.metric_id
            WHERE s.evaluation_id = ?
            ORDER BY m.weight DESC
        ");
        $scoreStmt->execute([$evaluation_id]);
        $metrics = $scoreStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Calculate average score
        $totalWeight = 0;
        $weightedScore = 0;
        
        foreach ($metrics as $metric) {
            $totalWeight += $metric['weight'];
            $weightedScore += ($metric['score'] * $metric['weight']);
        }
        
        $averageScore = $totalWeight > 0 ? $weightedScore / $totalWeight : 0;
        
    } catch (PDOException $e) {
        $metrics = [];
        $averageScore = 0;
        error_log("Error fetching evaluation scores: " . $e->getMessage());
    }
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$pageTitle = "View Evaluation";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                Evaluation: <?php echo htmlspecialchars($evaluation['faculty_name']); ?> (<?php echo $evaluation['semester'] . ' ' . $evaluation['academic_year']; ?>)
            </h2>
            <div>
                <?php if ($evaluation['status'] === 'draft' && $_SESSION['user_id'] === $evaluation['evaluator_id']): ?>
                    <a href="evaluation_edit.php?id=<?php echo $evaluation_id; ?>" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 mr-2">
                        Edit
                    </a>
                <?php endif; ?>
                <a href="evaluations.php<?php echo isset($_GET['faculty_id']) ? '?faculty_id=' . $_GET['faculty_id'] : ''; ?>" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300">
                    Back to List
                </a>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Evaluation Information -->
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-800">Evaluation Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Faculty</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($evaluation['faculty_name']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Department</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($evaluation['department_name']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Academic Year</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($evaluation['academic_year']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Semester</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars(ucfirst($evaluation['semester'])); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <p class="mt-1 text-sm">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    <?php 
                                    switch ($evaluation['status']) {
                                        case 'draft': echo 'bg-gray-100 text-gray-800'; break;
                                        case 'submitted': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'reviewed': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'approved': echo 'bg-green-100 text-green-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo ucfirst($evaluation['status']); ?>
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Evaluator</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($evaluation['evaluator_name']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Created On</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo date('F j, Y', strtotime($evaluation['created_at'])); ?></p>
                        </div>
                        
                        <?php if ($evaluation['status'] !== 'draft'): ?>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Submitted On</h4>
                                <p class="mt-1 text-sm text-gray-900"><?php echo !empty($evaluation['submitted_at']) ? date('F j, Y', strtotime($evaluation['submitted_at'])) : 'Not submitted yet'; ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Overall Score</h4>
                            <div class="mt-1 flex items-center">
                                <div class="text-3xl font-bold text-indigo-600"><?php echo number_format($averageScore, 2); ?></div>
                                <div class="ml-2 text-sm text-gray-500">/ 5.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Evaluation Details -->
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-800">Evaluation Metrics</h3>
                    </div>
                    <div class="p-6">
                        <?php if (empty($metrics)): ?>
                            <p class="text-gray-500">No metrics have been recorded for this evaluation.</p>
                        <?php else: ?>
                            <div class="space-y-6">
                                <?php foreach ($metrics as $metric): ?>
                                    <div class="border border-gray-200 rounded-md p-4">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($metric['metric_name']); ?></h4>
                                            <div class="flex items-center">
                                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-800 font-medium text-sm">
                                                    <?php echo number_format($metric['score'], 1); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500"><?php echo htmlspecialchars($metric['description']); ?></p>
                                        <?php if (!empty($metric['comments'])): ?>
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <h5 class="text-xs font-medium text-gray-700 mb-1">Evaluator Comments:</h5>
                                                <p class="text-sm text-gray-700"><?php echo nl2br(htmlspecialchars($metric['comments'])); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Overall Comments -->
                <?php if (!empty($evaluation['comments'])): ?>
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg mt-6">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-medium text-gray-800">Overall Comments</h3>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                <?php echo nl2br(htmlspecialchars($evaluation['comments'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_id'] === $evaluation['evaluator_id']): ?>
                    <div class="mt-6 flex justify-end">
                        <?php if ($evaluation['status'] === 'draft' && $_SESSION['user_id'] === $evaluation['evaluator_id']): ?>
                            <form method="POST" action="evaluation_update_status.php" class="inline">
                                <input type="hidden" name="evaluation_id" value="<?php echo $evaluation_id; ?>">
                                <input type="hidden" name="new_status" value="submitted">
                                <button type="submit" class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Submit Evaluation
                                </button>
                            </form>
                        <?php elseif ($evaluation['status'] === 'submitted' && $_SESSION['user_role'] === 'admin'): ?>
                            <form method="POST" action="evaluation_update_status.php" class="inline">
                                <input type="hidden" name="evaluation_id" value="<?php echo $evaluation_id; ?>">
                                <input type="hidden" name="new_status" value="reviewed">
                                <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                                    Mark as Reviewed
                                </button>
                            </form>
                            <form method="POST" action="evaluation_update_status.php" class="inline">
                                <input type="hidden" name="evaluation_id" value="<?php echo $evaluation_id; ?>">
                                <input type="hidden" name="new_status" value="approved">
                                <button type="submit" class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve Evaluation
                                </button>
                            </form>
                        <?php elseif ($evaluation['status'] === 'reviewed' && $_SESSION['user_role'] === 'admin'): ?>
                            <form method="POST" action="evaluation_update_status.php" class="inline">
                                <input type="hidden" name="evaluation_id" value="<?php echo $evaluation_id; ?>">
                                <input type="hidden" name="new_status" value="approved">
                                <button type="submit" class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    Approve Evaluation
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>