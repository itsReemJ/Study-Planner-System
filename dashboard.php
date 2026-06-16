<?php
require_once 'config.php';
require_login();

$userId = current_user_id();

$subjectCount = 0;
$taskCount = 0;
$completedCount = 0;
$progressPercent = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM subjects WHERE user_id = $userId");
if ($row = $result->fetch_assoc()) {
    $subjectCount = (int)$row['total'];
}

$result = $conn->query("SELECT COUNT(*) AS total, SUM(is_completed) AS completed FROM tasks WHERE user_id = $userId");
if ($row = $result->fetch_assoc()) {
    $taskCount = (int)$row['total'];
    $completedCount = (int)($row['completed'] ?? 0);
}

if ($taskCount > 0) {
    $progressPercent = (int)round(($completedCount / $taskCount) * 100);
}

$messages = [
    'Success starts with organizing your time 📚',
    'Small progress each day adds up to big results 🚀',
    'Stay focused and finish your tasks early ✅',
    'You are closer to graduation than yesterday 🎓'
];
$randomMessage = $messages[array_rand($messages)];
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
<style>
.container { width: 85%; margin: auto; text-align: center; }
.dashboard-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 30px; }
.card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); transition: 0.3s; }
.card:hover { transform: scale(1.05); }
.today-box { margin-top: 40px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0px 4px 10px rgba(0,0,0,0.1); }
.progress-bar { width: 80%; height: 20px; background: #eee; margin: auto; border-radius: 10px; overflow: hidden; }
.progress-fill { height: 100%; width: <?= $progressPercent ?>%; background: linear-gradient(to right, purple, hotpink); }
@media (max-width: 900px) { .dashboard-grid { grid-template-columns: repeat(2, 1fr); } }
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
<h1>Student Dashboard 🎓</h1>
<p>Today: <?= date('D, d M Y') ?></p>
<div class="dashboard-grid">
<div class="card"><h2>Total Subjects 📚</h2><p><?= $subjectCount ?></p></div>
<div class="card"><h2>Total Tasks 📝</h2><p><?= $taskCount ?></p></div>
<div class="card"><h2>Completed Tasks ✅</h2><p><?= $completedCount ?></p></div>
<div class="card"><h2>Weekly Progress 📊</h2><div class="progress-bar"><div class="progress-fill"></div></div><p style="margin-top: 10px;"><?= $progressPercent ?>%</p></div>
</div>
<div class="today-box">
<h2>Today's Reminder 📅</h2>
<p>Check your subjects and finish today's tasks early!</p>
</div>
<div class="today-box">
<h2>Motivation Message ✨</h2>
<p><?= e($randomMessage) ?></p>
<p><strong> Made by Gradutes of Class 2026 UPM 🎓🎉. Enjoy!!!</strong></p>
</div>
</div>
</body>
</html>
