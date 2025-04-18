<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';

if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$faculty_id = isset($_GET['faculty_id']) ? (int) $_GET['faculty_id'] : null;
$evaluations = getEvaluations($faculty_id);
$faculty_name = "";
if ($faculty_id) {
    $faculty = getFacultyById($faculty_id);
    if ($faculty) {
        $faculty_name = $faculty['first_name'] . ' ' . $faculty['last_name'];
    }
}

$pageTitle = "Evaluations";
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="flex-1 sm:ml-64">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">
                <?php echo $faculty_id ? "Evaluations for " . htmlspecialchars($faculty_name) : "All Evaluations"; ?>
            </h2>
            <a href="evaluation_create.php<?php echo $faculty_id ? "?faculty_id={$faculty_id}" : ""; ?>"
                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                New Evaluation
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <?php if (!$faculty_id): ?>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Faculty
                                    </th>
                                <?php endif; ?>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Academic Period
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Evaluator
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($evaluations)): ?>
                                <tr>
                                    <td colspan="<?php echo $faculty_id ? '5' : '6'; ?>"
                                        class="px-6 py-4 text-center text-gray-500">
                                        No evaluations found.
                                        <a href="evaluation_create.php<?php echo $faculty_id ? "?faculty_id={$faculty_id}" : ""; ?>"
                                            class="text-blue-600 hover:underline">
                                            Create new evaluation
                                        </a>.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($evaluations as $eval): ?>
                                    <tr>
                                        <?php if (!$faculty_id): ?>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($eval['faculty_name']); ?>
                                                </div>
                                            </td>
                                        <?php endif; ?>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo ucfirst(htmlspecialchars($eval['semester'])) . ' ' . htmlspecialchars($eval['academic_year']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($eval['evaluator_name']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php
                                            $statusColors = [
                                                'draft' => 'gray',
                                                'submitted' => 'yellow',
                                                'reviewed' => 'blue',
                                                'approved' => 'green'
                                            ];
                                            $color = $statusColors[$eval['status']] ?? 'gray';
                                            ?>
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?php echo $color; ?>-100 text-<?php echo $color; ?>-800">
                                                <?php echo ucfirst(htmlspecialchars($eval['status'])); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php
                                            if ($eval['overall_score'] !== null) {
                                                echo number_format($eval['overall_score'], 2);
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="evaluation_view.php?id=<?php echo $eval['evaluation_id']; ?>"
                                                class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                            <?php if ($eval['status'] === 'draft'): ?>
                                                <a href="evaluation_edit.php?id=<?php echo $eval['evaluation_id']; ?>"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'includes/footer.php'; ?>