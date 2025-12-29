<?php
/**
 * Class Model
 */

class ClassModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        return $this->db->query(
            "INSERT INTO classes (name, description) VALUES (?, ?)",
            [$data['name'], $data['description'] ?? null]
        );
    }
    
    public function findById($id) {
        return $this->db->fetch(
            "SELECT * FROM classes WHERE id = ?",
            [$id]
        );
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(s.id) as student_count
             FROM classes c
             LEFT JOIN students s ON c.id = s.class_id
             GROUP BY c.id
             ORDER BY c.name"
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
            "UPDATE classes SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    public function delete($id) {
        return $this->db->query(
            "DELETE FROM classes WHERE id = ?",
            [$id]
        );
    }
    
    public function getStudents($classId) {
        return $this->db->fetchAll(
            "SELECT s.*, u.email 
             FROM students s 
             JOIN users u ON s.user_id = u.id 
             WHERE s.class_id = ? 
             ORDER BY s.first_name, s.last_name",
            [$classId]
        );
    }
    
    public function getSubjects($classId) {
        return $this->db->fetchAll(
            "SELECT DISTINCT s.* 
             FROM subjects s
             JOIN teacher_assignments ta ON s.id = ta.subject_id
             WHERE ta.class_id = ?
             ORDER BY s.name",
            [$classId]
        );
    }
}