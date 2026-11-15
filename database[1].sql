CREATE DATABASE IF NOT EXISTS leaphigher_attendance
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE leaphigher_attendance;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  login_id VARCHAR(60) NOT NULL UNIQUE,
  full_name VARCHAR(120) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','faculty','hod') NOT NULL DEFAULT 'faculty',
  department VARCHAR(100) DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS campus_locations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  campus_name VARCHAR(120) NOT NULL,
  latitude DECIMAL(10,7) NOT NULL DEFAULT 0,
  longitude DECIMAL(10,7) NOT NULL DEFAULT 0,
  radius_meters INT NOT NULL DEFAULT 150,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS qr_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  subject VARCHAR(120) NOT NULL,
  token CHAR(64) NOT NULL UNIQUE,
  starts_at DATETIME NOT NULL,
  ends_at DATETIME NOT NULL,
  created_by INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_qr_creator FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS faculty_attendance (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  faculty_user_id INT NOT NULL,
  qr_session_id INT NOT NULL,
  scanned_at DATETIME NOT NULL,
  latitude DECIMAL(10,7) NOT NULL,
  longitude DECIMAL(10,7) NOT NULL,
  distance_meters DECIMAL(10,2) NOT NULL,
  status ENUM('PRESENT') NOT NULL DEFAULT 'PRESENT',
  UNIQUE KEY unique_faculty_session (faculty_user_id, qr_session_id),
  CONSTRAINT fk_att_faculty FOREIGN KEY (faculty_user_id) REFERENCES users(id),
  CONSTRAINT fk_att_session FOREIGN KEY (qr_session_id) REFERENCES qr_sessions(id)
);

INSERT INTO campus_locations (campus_name, latitude, longitude, radius_meters)
SELECT 'LEAPHIGHER Campus', 0, 0, 150
WHERE NOT EXISTS (SELECT 1 FROM campus_locations);
