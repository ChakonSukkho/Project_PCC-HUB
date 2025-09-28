-- ================================
-- Create Database
-- ================================
CREATE DATABASE IF NOT EXISTS pcc_hub;
USE pcc_hub;

-- ================================
-- Users Table (Students)
-- ================================
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) UNIQUE NOT NULL,
    user_password VARCHAR(255) NOT NULL,
    matric_number VARCHAR(20) UNIQUE NOT NULL,
    user_phone VARCHAR(20),
    user_address TEXT,
    date_of_birth DATE NULL,
    program VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

-- =====================================
-- Add the profile
-- =====================================
ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER user_address;



-- ================================
-- Admins Table (Staff/Admins)
-- ================================
CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_name VARCHAR(100) NOT NULL,
    admin_email VARCHAR(100) UNIQUE NOT NULL,
    admin_password VARCHAR(255) NOT NULL,
    staff_id VARCHAR(20) UNIQUE NOT NULL,
    admin_phone VARCHAR(20),
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

-- ================================
-- Announcements Table
-- ================================
CREATE TABLE announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('academic', 'facility', 'technical', 'event') DEFAULT 'general',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    image VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (created_by) REFERENCES admins(admin_id) ON DELETE SET NULL
);

-- ================================
-- Activities Table
-- ================================
CREATE TABLE activities (
    activity_id INT AUTO_INCREMENT PRIMARY KEY,
    activity_code VARCHAR(20) UNIQUE NOT NULL,
    activity_name VARCHAR(200) NOT NULL,
    activity_type ENUM('sports', 'cultural', 'academic', 'community_service', 'leadership', 'other') DEFAULT 'other',
    activity_date DATE NOT NULL,
    activity_time TIME,
    activity_location VARCHAR(200),
    activity_description TEXT,
    max_participants INT DEFAULT 0,
    current_participants INT DEFAULT 0,
    registration_deadline DATE,
    image VARCHAR(255),
    certificate_file VARCHAR(255),
    status ENUM('draft', 'open', 'ongoing', 'completed', 'cancelled') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (created_by) REFERENCES admins(admin_id) ON DELETE SET NULL
);

-- ================================
-- Activity Registrations Table
-- ================================
CREATE TABLE activity_registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    attendance_status ENUM('not_marked', 'present', 'absent', 'late') DEFAULT 'not_marked',
    certificate_issued TINYINT(1) DEFAULT 0,
    notes TEXT,
    UNIQUE KEY unique_registration (activity_id, user_id),
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ================================
-- User Certificates Table
-- ================================
CREATE TABLE user_certificates (
    certificate_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_id INT NOT NULL,
    certificate_code VARCHAR(50) UNIQUE NOT NULL,
    certificate_name VARCHAR(200) NOT NULL,
    certificate_type ENUM('participation', 'completion', 'achievement', 'winner', 'runner_up') DEFAULT 'participation',
    achievement_details TEXT,
    position_achieved INT NULL,
    time_achieved TIME NULL,
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verification_code VARCHAR(100) UNIQUE NOT NULL,
    status ENUM('active', 'revoked') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE
);

-- ================================
-- User Activity Points Table
-- ================================
CREATE TABLE user_activity_points (
    point_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    activity_id INT NOT NULL,
    points INT DEFAULT 0,
    point_type ENUM('participation', 'achievement', 'leadership', 'bonus') DEFAULT 'participation',
    description VARCHAR(255),
    awarded_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    awarded_by INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(activity_id) ON DELETE CASCADE,
    FOREIGN KEY (awarded_by) REFERENCES admins(admin_id) ON DELETE SET NULL
);

-- ================================
-- Running Sessions Table
-- ================================
CREATE TABLE user_running_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_date DATE NOT NULL,
    start_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    total_duration INT NOT NULL COMMENT 'Duration in seconds',
    total_distance DECIMAL(10, 3) NOT NULL COMMENT 'Distance in kilometers',
    avg_pace TIME COMMENT 'Average pace per kilometer (MM:SS format)',
    avg_speed DECIMAL(5, 2) COMMENT 'Average speed in km/h',
    calories_burned INT DEFAULT 0,
    max_speed DECIMAL(5, 2) DEFAULT 0,
    route_data JSON COMMENT 'GPS coordinates path',
    weather_condition VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, session_date),
    INDEX idx_session_date (session_date)
);

-- ================================
-- Running Statistics Table
-- ================================
CREATE TABLE user_running_stats (
    stats_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    total_runs INT DEFAULT 0,
    total_distance DECIMAL(10, 3) DEFAULT 0.000 COMMENT 'Total distance in kilometers',
    total_duration INT DEFAULT 0 COMMENT 'Total duration in seconds',
    longest_run_duration INT DEFAULT 0 COMMENT 'Longest run duration in seconds',
    longest_run_distance DECIMAL(10, 3) DEFAULT 0.000 COMMENT 'Longest run distance in km',
    best_pace TIME COMMENT 'Best pace per kilometer',
    avg_pace TIME COMMENT 'Overall average pace',
    total_calories_burned INT DEFAULT 0,
    last_run_date DATE,
    current_streak INT DEFAULT 0 COMMENT 'Current running streak in days',
    best_streak INT DEFAULT 0 COMMENT 'Best running streak in days',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ================================
-- Running Goals Table
-- ================================
CREATE TABLE user_running_goals (
    goal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    goal_type ENUM('distance', 'duration', 'frequency', 'pace') NOT NULL,
    target_value DECIMAL(10, 3) NOT NULL,
    target_period ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL,
    current_progress DECIMAL(10, 3) DEFAULT 0,
    start_date DATE NOT NULL,
    target_date DATE,
    status ENUM('active', 'completed', 'paused', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ================================
-- Initialize Stats for All Existing Users
-- ================================
INSERT INTO user_running_stats (user_id)
SELECT user_id FROM users
WHERE user_id NOT IN (SELECT user_id FROM user_running_stats);



-- ✅ 1. Add indexes to improve query performance (faster filtering and searching)
CREATE INDEX idx_user_id ON activity_registrations (user_id);           -- Speeds up queries filtering by user
CREATE INDEX idx_activity_id ON activity_registrations (activity_id);   -- Speeds up queries filtering by activity
CREATE INDEX idx_goal_user ON user_running_goals (user_id, goal_type);  -- Speeds up goal progress queries per user

-- ✅ 2. Add soft delete support (optional but useful)
ALTER TABLE activities ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL;  
-- When you want to "delete" an activity without removing it, set deleted_at = NOW()

-- ✅ 3. Improve foreign key clarity (use NULL instead of 0 for optional fields)
-- Example: Change awarded_by column to allow NULL instead of 0
ALTER TABLE user_activity_points MODIFY awarded_by INT DEFAULT NULL;  
-- This avoids confusion when no admin awarded the points

-- ✅ 4. Add certificate file URL/path for download
ALTER TABLE user_certificates ADD COLUMN certificate_file VARCHAR(255) NULL;  
-- Store the file path or URL of the generated certificate

-- ✅ 5. Add audit fields to track who updated records
ALTER TABLE activities ADD COLUMN updated_by INT NULL;       -- Admin/user ID who updated the activity
ALTER TABLE announcements ADD COLUMN updated_by INT NULL;    -- Admin/user ID who updated the announcement
