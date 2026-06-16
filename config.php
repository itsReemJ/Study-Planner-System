<?php
session_start();

// Local defaults for XAMPP. You can also set these as environment variables.
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'study_planner';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php');
    }
}

function current_user_id(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
