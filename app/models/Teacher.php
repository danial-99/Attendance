<?php
/**
 * Teacher Model
 */

class Teacher {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        return $this->db->query(
            "INSERT INTO teachers (user_id, first_name, last_name, phone, address) 
             VALUES (?, ?, ?, ?, ?)",
            [
                $data['user_id'],
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null,
                $data['address'] ?? null
            ]
        );
    }
    
    public function findById($id) {
        return $this->db->fetch(
            "SELECT t.*, u.email 
             FROM teachers t 
             JOIN users u ON t.user_id = u.id 
             WHERE t.id = ?",
            [$id]
        );
    }
    
    public function findByUserId($userId) {
        return $this->db->fetch(
            "SELECT * FROM teachers WHERE user_id = ?",
            [$userId]
        );
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT t.*, u.email 
             FROM teachers t 
             JOIN users u ON t.user_id = u.id 
             ORDER BY t.first_name, t.last_name"
        );
    }
    
    public function getAssignments($teacherId) {
        return $this->db->fetchAll(
            "SELECT ta.*, c.name as class_name, s.name as subject_name, s.code as subject_code
             FROM teacher_assignments ta
             JOIN classes c ON ta.class_id = c.id
             JOIN subjects s ON ta.subject_id = s.id
             WHERE ta.teacher_id = ?
             ORDER BY c.name, s.name",
            [$teacherId]
        );
    }
    
    public function assignToClass($teacherId, $classId, $subjectId) {
        return $this->db->query(
            "INSERT INTO teacher_assignments (teacher_id, class_id, subject_id) 
             VALUES (?, ?, ?) 
             ON DUPLICATE KEY UPDATE teacher_id = teacher_id",
            [$teacherId, $classId, $subjectId]
        );
    }
    
    public function removeAssignment($teacherId, $classId, $subjectId) {
        return $this->db->query(
            "DELETE FROM teacher_assignments 
             WHERE teacher_id = ? AND class_id = ? AND subject_id = ?",
            [$teacherId, $classId, $subjectId]
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
            "UPDATE teachers SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    public function delete($id) {
        return $this->db->query(
            "DELETE FROM teachers WHERE id = ?",
            [$id]
        );
    }
}