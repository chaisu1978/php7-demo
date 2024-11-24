<?php
session_start();
require 'db.inc.php';

// Ensure user is authenticated
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$taskName = $_POST['task_name'];
$category = $_POST['category'] ?? null;
$dueDate = $_POST['due_date'] ?? null;
$isCompleted = $_POST['is_completed'] ?? 0;

// Insert the new task into the database
$sql = 'INSERT INTO tasks (user_id, task_name, category, due_date, is_completed) VALUES (?, ?, ?, ?, ?)';
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$userId, $taskName, $category, $dueDate, $isCompleted]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // Log and handle any errors
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while adding the task.']);
}
