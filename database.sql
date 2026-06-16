CREATE DATABASE IF NOT EXISTS study_planner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE study_planner;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    major VARCHAR(100) DEFAULT '',
    academic_year VARCHAR(50) DEFAULT '',
    phone VARCHAR(30) DEFAULT '',
    profile_image VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    subject_day VARCHAR(20) NOT NULL,
    subject_time TIME NOT NULL,
    room_location VARCHAR(100) DEFAULT '',
    subject_type VARCHAR(20) NOT NULL,
    note VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    task_name VARCHAR(120) NOT NULL,
    task_date DATE DEFAULT NULL,
    priority_level VARCHAR(20) NOT NULL,
    is_completed TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
