<?php
/**
 * Subject Model
 */

class Subject {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        return $this->db->query(
            "INSERT INTO subjects (name, code) VALUES (?, ?)",
            [$data['name'], $data['code']]
        );
    }
    
    public function findById($id) {
        return $this->db->fetch(
            "SELECT * FROM subjects WHERE id = ?",
            [$id]
        );
    }
    
    public function findByCode($code) {
        return $this->db->fetch(
            "SELECT * FROM subjects WHERE code = ?",
            [$code]
        );
    }
    
    public function getAll() {
        return $this->db->fetchAll(
            "SELECT * FROM subjects ORDER BY name"
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
            "UPDATE subjects SET " . implode(', ', $fields) . " WHERE id = ?",
            $values
        );
    }
    
    public function delete($id) {
        return $this->db->query(
            "DELETE FROM subjects WHERE id = ?",
            [$id]
        );
    }
    
    public function getTeachers($subjectId) {
        return $this->db->fetchAll(
            "SELECT DISTINCT t.*, u.email
             FROM teachers t
             JOIN users u ON t.user_id = u.id
             JOIN teacher_assignments ta ON t.id = ta.teacher_id
             WHERE ta.subject_id = ?
             ORDER BY t.first_name, t.last_name",
            [$subjectId]
        );
    }
    
    public function getClasses($subjectId) {
        return $this->db->fetchAll(
            "SELECT DISTINCT c.*
             FROM classes c
             JOIN teacher_assignments ta ON c.id = ta.class_id
             WHERE ta.subject_id = ?
             ORDER BY c.name",
            [$subjectId]
        );
    }
}