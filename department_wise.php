<?php
require_once 'includes/config.php';
require_once 'includes/data_access.php';
global $pdo;

$results = [];

try {
    $sql = "SELECT 
                d.name as Department,
                d.code as `Dept. Code`,
                f.faculty_id as ID,
                fd.Name as Name,
                e.overall_score as Score 
            FROM faculty f
            JOIN departments d ON f.department_id = d.department_id 
            JOIN facultydata fd ON fd.f_id = f.faculty_id 
            JOIN evaluations e ON f.faculty_id = e.faculty_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("Database error in getEvaluations: " . $e->getMessage());
    $results = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Evaluation Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f8f9fa;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        th {
            background-color: #343a40;
            color: #fff;
        }
        tr:nth-child(even) {
            background-color: #f1f1f1;
        }
        h2 {
            margin-bottom: 20px;
            text-align:center;
        }
        .cont {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.back-btn {
    background-color: #6c757d;
    color: white;
    padding: 8px 16px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    transition: background-color 0.2s ease-in-out;
}

.back-btn:hover {
    background-color: #5a6268;
}
    </style>
</head>
<body>
    <div class="cont">
        <a href="reports.php" class="back-btn">Back to Reports</a>
    </div>
    <h2>Department Evaluation Report</h2>

    <?php if (!empty($results)): ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($results[0]) as $heading): ?>
                        <th><?php echo htmlspecialchars($heading); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <?php foreach ($row as $value): ?>
                            <td><?php echo htmlspecialchars($value?$value:' '); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No evaluation data found.</p>
    <?php endif; ?>

</body>
</html>
