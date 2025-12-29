-- Attendance Management Portal Database Schema

CREATE DATABASE IF NOT EXISTS attendance_portal;
USE attendance_portal;

-- Users table (for authentication)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Classes table
CREATE TABLE classes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Subjects table
CREATE TABLE subjects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers table
CREATE TABLE teachers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Students table
CREATE TABLE students (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    student_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    class_id INT NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);

-- Teacher-Class-Subject assignments
CREATE TABLE teacher_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (teacher_id, class_id, subject_id)
);

-- Attendance table
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    class_id INT NOT NULL,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'late') NOT NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (student_id, class_id, subject_id, date)
);

-- Insert sample data
-- Password hashes: admin123, teacher123, student123
INSERT INTO users (email, password, role) VALUES
('admin@school.com', '$2y$10$E.OyK2QlzJ5iVBa6oP8.2eJ5FkW8mGHjmxVx.Qr7zL3nN9pM1sT6u', 'admin'),
('teacher@school.com', '$2y$10$F.PzL3RmzK6jWCb7pQ9.3fK6GlX9nHIknxWy.Rs8aM4oO0qN2tU7v', 'teacher'),
('student@school.com', '$2y$10$G.QaM4SnaN7kXDc8qR0.4gL7HmY0oIJloYXz.St9bN5pP1rO3uV8w', 'student');

INSERT INTO classes (name, description) VALUES
('Class 10A', 'Grade 10 Section A'),
('Class 10B', 'Grade 10 Section B'),
('Class 11A', 'Grade 11 Section A');

INSERT INTO subjects (name, code) VALUES
('Mathematics', 'MATH101'),
('Physics', 'PHY101'),
('Chemistry', 'CHEM101'),
('English', 'ENG101');

INSERT INTO teachers (user_id, first_name, last_name, phone) VALUES
(2, 'John', 'Doe', '1234567890');

INSERT INTO students (user_id, student_id, first_name, last_name, class_id, phone) VALUES
(3, 'STU001', 'Jane', 'Smith', 1, '0987654321');

INSERT INTO teacher_assignments (teacher_id, class_id, subject_id) VALUES
(1, 1, 1),
(1, 1, 2);