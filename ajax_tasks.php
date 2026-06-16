<?php
require_once 'config.php';
require_login();

header('Content-Type: application/json');

$userId = current_user_id();
$action = $_POST['action'] ?? '';

try {
    if ($action === 'add_task') {
        $name = trim($_POST['task_name'] ?? '');
        $date = trim($_POST['task_date'] ?? '') ?: null;
        $priority = trim($_POST['task_priority'] ?? 'Medium');

        if ($name === '') {
            echo json_encode(['ok' => false, 'message' => 'Please enter a task']);
            exit;
        }

        $stmt = $conn->prepare('INSERT INTO tasks (user_id, task_name, task_date, priority_level) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('isss', $userId, $name, $date, $priority);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['ok' => true]);
        exit;
    }

    if ($action === 'toggle_task') {
        $taskId = (int)($_POST['id'] ?? 0);

        $stmt = $conn->prepare('UPDATE tasks SET is_completed = 1 - is_completed WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $taskId, $userId);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['ok' => true]);
        exit;
    }

    if ($action === 'delete_task') {
        $taskId = (int)($_POST['id'] ?? 0);

        $stmt = $conn->prepare('DELETE FROM tasks WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $taskId, $userId);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'message' => 'Invalid task action']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
?>