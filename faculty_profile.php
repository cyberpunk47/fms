<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

$faculty_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$faculty = getFacultyById($faculty_id);

if (!$faculty) {
    header("Location: faculty.php");
    exit();
}
function viewEvaluations($facultyId = null)
{
    global $pdo;
    try {
        $sql = "SELECT f.f_id as ID, f.Name, e.overall_score as Score, e.comments as Comments, 
       e.status as status, e.semester, e.academic_year,e.evaluation_id
FROM facultydata f 
JOIN evaluations e ON f.f_id = e.faculty_id 
WHERE f.f_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$facultyId]);
return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in getEvaluations: " . $e->getMessage());
        return [];
    }
}
$evaluation=viewEvaluations($faculty_id);
$evaluations = getEvaluations($faculty_id);

$pageTitle = "Faculty Profile";
include 'includes/header_fac.php';
?>
<html>
<head>
    <style>
        body{
            background:url('background.png');
        }
    </style>
</head>
<body>
<div class="flex-1">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800 text-center flex-1">
                <?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?>
            </h2>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-800">Personal Information</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-center mb-6">
                            <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-800 text-xl font-bold">
                                <?php echo strtoupper(substr($faculty['first_name'], 0, 1) . substr($faculty['last_name'], 0, 1)); ?>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Full Name</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($faculty['first_name'] . ' ' . $faculty['last_name']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($faculty['email']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Department</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($faculty['department_name']); ?></p>
                        </div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm rounded-lg mt-6">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-medium text-gray-800">Academic Status</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Position</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($faculty['position']); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Rank</h4>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo ucfirst(htmlspecialchars($faculty['rank'])); ?>
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Tenure Status</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo str_replace('_', ' ', ucfirst(htmlspecialchars($faculty['tenure_status']))); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Hire Date</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo date('F j, Y', strtotime($faculty['hire_date'])); ?></p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Office Location</h4>
                            <p class="mt-1 text-sm text-gray-900"><?php echo htmlspecialchars($faculty['office_location'] ?? 'Not specified'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex">
                            <a href="#" class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm tab-link active" data-tab="biography">
                                Biography
                            </a>
                            <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm tab-link" data-tab="evaluations">
                                Evaluations (<?php echo count($evaluations); ?>)
                            </a>
                            <a href="#" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm tab-link" data-tab="development">
                                Development
                            </a>
                        </nav>
                    </div>
                    <div class="p-6">
                        <div id="biography-tab" class="tab-content">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Faculty Biography</h3>
                            
                            <?php if (!empty($faculty['bio'])): ?>
                                <div class="prose max-w-none">
                                    <p><?php echo nl2br(htmlspecialchars($faculty['bio'])); ?></p>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-500 italic">No biography information available.</p>
                            <?php endif; ?>
                        </div>
                        <div id="evaluations-tab" class="tab-content hidden">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Evaluations</h3>
                            
                            <?php if (count($evaluation) >0): ?>
                                <div class="space-y-4">
                                    <?php foreach ($evaluation as $eval): ?>
                                        <div class="border border-gray-200 rounded-md p-4">
                                            <div class="flex justify-between">
                                                <h4 class="text-sm font-medium text-gray-900">
                                                    <?php echo ucfirst($eval['semester']) . ' ' . $eval['academic_year']; ?>
                                                </h4>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    <?php 
                                                    switch ($eval['status']) {
                                                        case 'draft': echo 'bg-gray-100 text-gray-800'; break;
                                                        case 'submitted': echo 'bg-yellow-100 text-yellow-800'; break;
                                                        case 'reviewed': echo 'bg-blue-100 text-blue-800'; break;
                                                        case 'approved': echo 'bg-green-100 text-green-800'; break;
                                                        default: echo 'bg-gray-100 text-gray-800';
                                                    }
                                                    ?>">
                                                    <?php echo ucfirst($eval['status']); ?>
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-2">
                                                Name: <?php echo htmlspecialchars($eval['Name']); ?>
                                            </p>
                                            <?php if (!empty($eval['Score'])): ?>
                                                <div class="mt-2 flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">Score: </div>
                                                    <div class="ml-2 text-sm text-gray-700"><?php echo number_format($eval['Score'], 2); ?>/5.00</div>
                                                </div>
                                            <?php endif; ?>
                                            <div class="mt-3">
                                                <a href="evaluation_view.php?id=<?php echo $eval['evaluation_id']; ?>" class="text-sm text-indigo-600 hover:text-indigo-900">
                                                    View Details →
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($evaluation) > 3): ?>
                                    <div class="mt-4 text-right">
                                        <a href="evaluations.php?faculty_id=<?php echo $faculty_id; ?>" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                            View All Evaluations →
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="text-gray-500 italic">No evaluations available for this faculty member.</p>
                                <div class="mt-4">
                                    <a href="evaluation_create.php?faculty_id=<?php echo $faculty_id; ?>" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        Create New Evaluation
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div id="development-tab" class="tab-content hidden">
                            <h3 class="text-lg font-medium text-gray-800 mb-4">Professional Development</h3>
                            
                            <?php
                            try {
                                $devStmt = $pdo->prepare("
                                    SELECT p.*, CONCAT(r.first_name, ' ', r.last_name) as reviewer_name
                                    FROM promotion_requests p
                                    LEFT JOIN users r ON p.reviewer_id = r.user_id
                                    WHERE p.faculty_id = ?
                                    ORDER BY p.submission_date DESC
                                ");
                                $devStmt->execute([$faculty_id]);
                                $promotions = $devStmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                $workshopStmt = $pdo->prepare("
                                    SELECT w.*, r.registration_date, r.status as registration_status
                                    FROM workshop_registrations r
                                    JOIN workshops w ON r.workshop_id = w.workshop_id
                                    WHERE r.user_id = ?
                                    ORDER BY w.start_date DESC
                                ");
                                $workshopStmt->execute([$faculty_id]);
                                $workshops = $workshopStmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                $promotions = [];
                                $workshops = [];
                            }
                            ?>
                        
                            <?php if (!empty($promotions)): ?>
                                <div class="mb-6">
                                    <h4 class="text-md font-medium text-gray-700 mb-3">Promotion Requests</h4>
                                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                        <ul class="divide-y divide-gray-200">
                                            <?php foreach ($promotions as $promo): ?>
                                                <li class="px-4 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900">
                                                                <?php echo ucfirst($promo['current_rank']); ?> to <?php echo ucfirst($promo['requested_rank']); ?> Professor
                                                            </p>
                                                            <p class="text-xs text-gray-500">
                                                                Submitted: <?php echo date('F j, Y', strtotime($promo['submission_date'])); ?>
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <span class="px-2 py-1 text-xs rounded-full 
                                                                <?php 
                                                                switch ($promo['status']) {
                                                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                                    case 'under_review': echo 'bg-blue-100 text-blue-800'; break;
                                                                    case 'approved': echo 'bg-green-100 text-green-800'; break;
                                                                    case 'denied': echo 'bg-red-100 text-red-800'; break;
                                                                    default: echo 'bg-gray-100 text-gray-800';
                                                                }
                                                                ?>">
                                                                <?php echo str_replace('_', ' ', ucfirst($promo['status'])); ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($workshops)): ?>
                                <div>
                                    <h4 class="text-md font-medium text-gray-700 mb-3">Workshop Participation</h4>
                                    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                                        <ul class="divide-y divide-gray-200">
                                            <?php foreach ($workshops as $workshop): ?>
                                                <li class="px-4 py-4">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($workshop['title']); ?></p>
                                                            <p class="text-xs text-gray-500">
                                                                <?php echo date('F j, Y', strtotime($workshop['start_date'])); ?>
                                                                <?php if (!empty($workshop['location'])): ?> • <?php echo htmlspecialchars($workshop['location']); ?><?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <a href="workshop_view.php?id=<?php echo $workshop['workshop_id']; ?>" class="text-xs text-indigo-600 hover:text-indigo-900">
                                                                View Details
                                                            </a>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (empty($promotions) && empty($workshops)): ?>
                                <p class="text-gray-500 italic">No development records found for this faculty member.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>

<?php include 'includes/footer.php'; ?>