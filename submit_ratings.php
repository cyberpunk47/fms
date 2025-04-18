<?php
require_once 'includes/config.php';
try{
    $name = $_POST['student_name'] ?? '';
    $email = $_POST['student_id'] ?? '';
    $ui= $_POST['ui'] ?? null;
    $flow= $_POST['flow'] ?? 0;
    $feature = $_POST['feature'] ?? 0;
    $support = $_POST['support'] ?? 0;
    $remark = $_POST['comments'] ?? '';
    $stmt=$pdo->prepare("INSERT INTO rating (Name, email, ui, flow, feature, support, remark) 
                               VALUES (:name, :email, :ui, :flow, :feature, :support, :remark)");
    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':ui'      => $ui,
        ':flow'    => $flow,
        ':feature' => $feature,
        ':support' => $support,
        ':remark'  => $remark,
    ]);
    echo "<script>alert('Feedback submitted successfully!');</script>";
}catch (PDOException $e) {
    echo "<script>alert('Database error: " . $e->getMessage() . "');</script>";
}
?>