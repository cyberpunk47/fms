<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Get all faculty for the dropdown
$allFaculty = getAllFaculty();

// If faculty_id is in the URL, pre-select that faculty
$selected_faculty_id = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : '';

// Get all performance metrics
$metrics = $pdo->query("SELECT * FROM performance_metrics WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process form submission
    try {
        $pdo->beginTransaction();
        
        // Create evaluation record
        $stmt = $pdo->prepare("
            INSERT INTO evaluations (
                faculty_id, evaluator_id, academic_year, semester, status, comments, created_at
            ) VALUES (?, ?, ?, ?, 'draft', ?, NOW())
        ");
        
        $stmt->execute([
            $_POST['faculty_id'],
            $_SESSION['user_id'], // Current user is the evaluator
            $_POST['academic_year'],
            $_POST['semester'],
            $_POST['comments'] ?? null
        ]);
        
        $evaluationId = $pdo->lastInsertId();
        
        // Add scores for each metric
        foreach ($metrics as $metric) {
            $metricId = $metric['metric_id'];
            if (isset($_POST['metric_' . $metricId])) {
                $score = $_POST['metric_' . $metricId];
                $comments = $_POST['metric_' . $metricId . '_comments'] ?? null;
                
                $scoreStmt = $pdo->prepare("
                    INSERT INTO evaluation_scores (evaluation_id, metric_id, score, comments)
                    VALUES (?, ?, ?, ?)
                ");
                
                $scoreStmt->execute([
                    $evaluationId,
                    $metricId,
                    $score,
                    $comments
                ]);
            }
        }
        
        // Calculate and update overall score
        $avgScore = $pdo->prepare("
            SELECT AVG(score) FROM evaluation_scores WHERE evaluation_id = ?
        ");
        $avgScore->execute([$evaluationId]);
        $overallScore = $avgScore->fetchColumn();
        
        $updateScore = $pdo->prepare("
            UPDATE evaluations SET overall_score = ? WHERE evaluation_id = ?
        ");
        $updateScore->execute([$overallScore, $evaluationId]);
        
        // Record audit trail
        recordAuditTrail(
            $_SESSION['user_id'],
            'INSERT',
            'evaluations',
            $evaluationId,
            null,
            [
                'faculty_id' => $_POST['faculty_id'],
                'academic_year' => $_POST['academic_year'],
                'semester' => $_POST['semester']
            ]
        );
        
        $pdo->commit();
        
        header("Location: evaluations.php?success=1");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = 'Error creating evaluation: ' . $e->getMessage();
    }
}

$pageTitle = "Create New Evaluation";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<div class="flex-1 sm:ml-64">
    <!-- Page Heading -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8">
            <h2 class="text-lg font-semibold text-gray-800">
                Create New Evaluation
            </h2>
        </div>
    </header>

    <!-- Page Content -->
    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Evaluation Form -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <?php if ($message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <!-- Basic Evaluation Information -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="faculty_id" class="block text-sm font-medium text-gray-700">Faculty Member</label>
                            <select id="faculty_id" name="faculty_id" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Faculty</option>
                                <?php foreach ($allFaculty as $faculty): ?>
                                <option value="<?php echo $faculty['faculty_id']; ?>" <?php echo ($selected_faculty_id == $faculty['faculty_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                            <select id="academic_year" name="academic_year" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Year</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2022-2023">2022-2023</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                            <select id="semester" name="semester" required
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Semester</option>
                                <option value="fall">Fall</option>
                                <option value="spring">Spring</option>
                                <option value="summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Performance Metrics -->
                    <h3 class="text-lg font-medium text-gray-900 pt-4">Performance Metrics</h3>
                    
                    <div class="space-y-4">
                        <?php foreach ($metrics as $metric): ?>
                        <div class="border border-gray-200 rounded-md p-4">
                            <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($metric['name']); ?></h4>
                            <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($metric['description']); ?></p>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="metric_<?php echo $metric['metric_id']; ?>" class="block text-sm font-medium text-gray-700">
                                        Score (1-5)
                                    </label>
                                    <select id="metric_<?php echo $metric['metric_id']; ?>" 
                                            name="metric_<?php echo $metric['metric_id']; ?>" 
                                            required
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Score</option>
                                        <option value="1">1 - Unsatisfactory</option>
                                        <option value="2">2 - Needs Improvement</option>
                                        <option value="3">3 - Meets Expectations</option>
                                        <option value="4">4 - Exceeds Expectations</option>
                                        <option value="5">5 - Outstanding</option>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="metric_<?php echo $metric['metric_id']; ?>_comments" class="block text-sm font-medium text-gray-700">
                                        Comments
                                    </label>
                                    <textarea id="metric_<?php echo $metric['metric_id']; ?>_comments" 
                                              name="metric_<?php echo $metric['metric_id']; ?>_comments" 
                                              rows="2"
                                              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Overall Comments -->
                    <div>
                        <label for="comments" class="block text-sm font-medium text-gray-700">Overall Comments</label>
                        <textarea id="comments" name="comments" rows="4"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex justify-end">
                        <a href="evaluations.php" class="bg-gray-200 py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Evaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>
