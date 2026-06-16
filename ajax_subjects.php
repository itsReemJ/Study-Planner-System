<?php
require_once 'config.php';
require_login();

header('Content-Type: application/json');

$userId = current_user_id();
$action = $_POST['action'] ?? '';

try {
    if ($action === 'add_subject') {
        $name = trim($_POST['subject_name'] ?? '');
        $day = trim($_POST['subject_day'] ?? '');
        $time = trim($_POST['subject_time'] ?? '');
        $room = trim($_POST['subject_room'] ?? '');
        $type = trim($_POST['subject_type'] ?? 'Lecture');
        $note = trim($_POST['subject_note'] ?? '');

        if ($name === '' || $time === '') {
            echo json_encode(['ok' => false, 'message' => 'Please enter the subject name and time']);
            exit;
        }

        $stmt = $conn->prepare('INSERT INTO subjects (user_id, subject_name, subject_day, subject_time, room_location, subject_type, note) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issssss', $userId, $name, $day, $time, $room, $type, $note);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['ok' => true]);
        exit;
    }

    if ($action === 'delete_subject') {
        $deleteId = (int)($_POST['id'] ?? 0);

        $stmt = $conn->prepare('DELETE FROM subjects WHERE id = ? AND user_id = ?');
        $stmt->bind_param('ii', $deleteId, $userId);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['ok' => true]);
        exit;
    }

    echo json_encode(['ok' => false, 'message' => 'Invalid subject action']);
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => 'Server error']);
}
?>