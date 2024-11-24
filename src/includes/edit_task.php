<?php
session_start();
require 'db.inc.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$taskId = $_POST['task_id'];
$taskName = $_POST['task_name'];
$category = $_POST['category'] ?? null;
$dueDate = $_POST['due_date'] ?? null;
$isCompleted = $_POST['is_completed'] ?? 0;

// Update the task in the database
$sql = 'UPDATE tasks SET task_name = ?, category = ?, due_date = ?, is_completed = ? WHERE id = ? AND user_id = ?';
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$taskName, $category, $dueDate, $isCompleted, $taskId, $userId]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes were detected in the task.']);
    }
} catch (PDOException $e) {
    // Log and handle any errors
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the task.']);
}
