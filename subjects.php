<?php
require_once 'config.php';
require_login();

$userId = current_user_id();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subject'])) {
    $name = trim($_POST['subject_name'] ?? '');
    $day = trim($_POST['subject_day'] ?? '');
    $time = trim($_POST['subject_time'] ?? '');
    $room = trim($_POST['subject_room'] ?? '');
    $type = trim($_POST['subject_type'] ?? 'Lecture');
    $note = trim($_POST['subject_note'] ?? '');

    if ($name === '' || $time === '') {
        $error = 'Please enter the subject name and time';
    } else {
        $stmt = $conn->prepare('INSERT INTO subjects (user_id, subject_name, subject_day, subject_time, room_location, subject_type, note) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('issssss', $userId, $name, $day, $time, $room, $type, $note);
        $stmt->execute();
        $stmt->close();
        redirect('subjects.php');
    }
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $conn->prepare('DELETE FROM subjects WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $deleteId, $userId);
    $stmt->execute();
    $stmt->close();
    redirect('subjects.php');
}

$stmt = $conn->prepare('SELECT * FROM subjects WHERE user_id = ? ORDER BY FIELD(subject_day, "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday"), subject_time');
$stmt->bind_param('i', $userId);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$subjectCounter = count($subjects);
?>
<!DOCTYPE html>
<html>
<head>
<title>Subjects</title>
<link rel="stylesheet" href="style.css">
<style>
.container { width: 85%; margin: auto; }
h1 { margin-bottom: 20px; }
input, select { padding: 10px; margin: 5px; }
button { padding: 10px 15px; cursor: pointer; }
table { width: 100%; margin-top: 20px; border-collapse: collapse; background: white; }
th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: center; }
tr:hover { background-color: #f5f5f5; transform: scale(1.01); }
.lecture { color: blue; font-weight: bold; }
.lab { color: green; font-weight: bold; }
.tutorial { color: purple; font-weight: bold; }
.counter { margin-top: 15px; font-size: 18px; font-weight: bold; }
.form-row { display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.form-row input, .form-row select { flex:1 1 180px; max-width:none; }
.inline-form { display:inline; }
</style>
</head>
<body>
<div class="navbar">
<a href="dashboard.php">Dashboard</a>
<a href="subjects.php">Subjects</a>
<a href="tasks.php">Tasks</a>
<a href="profile.php">Profile</a>
<a href="logout.php">Logout</a>
</div>
<div class="container">
<h1>Weekly Study Planner 📚</h1>
<p class="error"><?= e($error) ?></p>
<form method="POST" action="" id="subjectForm">
<div class="form-row">
<input name="subject_name" placeholder="Subject Name">
<select name="subject_day">
<option>Sunday</option><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option>
</select>
<input type="time" name="subject_time">
<input name="subject_room" placeholder="Room / Location">
<select name="subject_type">
<option value="Lecture">Lecture</option><option value="Lab">Lab</option><option value="Tutorial">Tutorial</option>
</select>
<input name="subject_note" placeholder="Notes (optional)">
<button type="submit" name="add_subject">Add Subject</button>
</div>
</form>
<table>
<thead><tr><th>Subject</th><th>Day</th><th>Time</th><th>Location</th><th>Type</th><th>Notes</th><th>Delete</th></tr></thead>
<tbody>
<?php foreach ($subjects as $subject): ?>
<tr>
<td><?= e($subject['subject_name']) ?></td>
<td><?= e($subject['subject_day']) ?></td>
<td><?= e(date('h:i A', strtotime($subject['subject_time']))) ?></td>
<td><?= e($subject['room_location']) ?></td>
<td class="<?= strtolower(e($subject['subject_type'])) ?>"><?= e($subject['subject_type']) ?></td>
<td><?= e($subject['note']) ?></td>
<td><button type="button" class="ajax-delete-subject" data-id="<?= (int)$subject['id'] ?>">Delete</button></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<div class="counter">Total Subjects This Week: <span><?= $subjectCounter ?></span></div>
</div>
<script src="ajax.js"></script>
</body>
</html>
