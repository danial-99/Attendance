<?php
/**
 * Student Model
 */

class Student {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        return $this->db->query(
            "INSERT INTO students (user_id, student_id, first_name, last_name, class_id, phone, address) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['student_id'],
                $data['first_name'],
                $data['last_name'],
                $data['class_id'],
                $data['phone'] ?? null,
                $data['address'] ?? null
            ]
        );
    }
    
    public function findById($id) {
        return $this->db->fetch(
            "SELECT s.*, u.email, c.name as class_name 
             FROM students s 
             JOIN users u ON s.user_id = u.id 
             JOIN classes c ON s.class_id = c.id 
             WHERE s.id = ?",
            [$id]
        );
    }
    
    public function findByUserId($userId) {
        return $this->db->fetch(
            "SELECT s.*, c.name as class_name 
             FROM students s 
             JOIN classes c ON s.class_id = c.id 
             WHERE s.user_id = ?",
            [$userId]
        );
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT s.*, u.email, c.name as class_name 
             FROM students s 
             JOIN users u ON s.user_id = u.id 
             JOIN classes c ON s.class_id = c.id 
             ORDER BY s.first_name, s.last_name"
        );
    }
    
    public function getByClass($classId) {
        return $this->db->fetchAll(
            "SELECT s.*, u.email 
             FROM students s 
             JOIN users u ON s.user_id = u.id 
             WHERE s.class_id = ? 
             ORDER BY s.first_name, s.last_name",
            [$classId]
        );
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        return $this->db->query(
            "UPDATE students SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    public function delete($id) {
        return $this->db->query(
            "DELETE FROM students WHERE id = ?",
            [$id]
        );
    }
    
    public function getAttendanceStats($studentId, $month = null, $year = null) {
        $whereClause = "WHERE a.student_id = ?";
        $params = [$studentId];
        
        if ($month && $year) {
            $whereClause .= " AND MONTH(a.date) = ? AND YEAR(a.date) = ?";
            $params[] = $month;
            $params[] = $year;
        }
        
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total_days,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
             FROM attendance a 
             $whereClause",
            $params
        );
    }
}