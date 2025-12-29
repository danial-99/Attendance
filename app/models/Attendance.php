<?php
/**
 * Attendance Model
 */

class Attendance {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function markAttendance($data) {
        return $this->db->query(
            "INSERT INTO attendance (student_id, class_id, subject_id, teacher_id, date, status, remarks) 
             VALUES (?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE 
             status = VALUES(status), 
             remarks = VALUES(remarks), 
             updated_at = CURRENT_TIMESTAMP",
            [
                $data['student_id'],
                $data['class_id'],
                $data['subject_id'],
                $data['teacher_id'],
                $data['date'],
                $data['status'],
                $data['remarks'] ?? null
            ]
        );
    }
    
    public function getAttendanceByDate($classId, $subjectId, $date) {
        return $this->db->fetchAll(
            "SELECT a.*, s.student_id, s.first_name, s.last_name
             FROM attendance a
             JOIN students s ON a.student_id = s.id
             WHERE a.class_id = ? AND a.subject_id = ? AND a.date = ?
             ORDER BY s.first_name, s.last_name",
            [$classId, $subjectId, $date]
        );
    }
    
    public function getStudentAttendance($studentId, $startDate = null, $endDate = null) {
        $whereClause = "WHERE a.student_id = ?";
        $params = [$studentId];
        
        if ($startDate) {
            $whereClause .= " AND a.date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $whereClause .= " AND a.date <= ?";
            $params[] = $endDate;
        }
        
        return $this->db->fetchAll(
            "SELECT a.*, c.name as class_name, s.name as subject_name, s.code as subject_code
             FROM attendance a
             JOIN classes c ON a.class_id = c.id
             JOIN subjects s ON a.subject_id = s.id
             $whereClause
             ORDER BY a.date DESC, s.name",
            $params
        );
    }
    
    public function getClassAttendanceReport($classId, $startDate, $endDate) {
        return $this->db->fetchAll(
            "SELECT 
                s.student_id,
                s.first_name,
                s.last_name,
                COUNT(a.id) as total_days,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
                ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 2) as attendance_percentage
             FROM students s
             LEFT JOIN attendance a ON s.id = a.student_id 
                AND a.date BETWEEN ? AND ?
             WHERE s.class_id = ?
             GROUP BY s.id
             ORDER BY s.first_name, s.last_name",
            [$startDate, $endDate, $classId]
        );
    }
    
    public function getDailyAttendanceReport($date) {
        return $this->db->fetchAll(
            "SELECT 
                c.name as class_name,
                sub.name as subject_name,
                COUNT(a.id) as total_students,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count
             FROM attendance a
             JOIN classes c ON a.class_id = c.id
             JOIN subjects sub ON a.subject_id = sub.id
             WHERE a.date = ?
             GROUP BY a.class_id, a.subject_id
             ORDER BY c.name, sub.name",
            [$date]
        );
    }
    
    public function getMonthlyStats($month, $year) {
        return $this->db->fetchAll(
            "SELECT 
                c.name as class_name,
                COUNT(DISTINCT a.student_id) as total_students,
                COUNT(a.id) as total_records,
                SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count,
                SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_count,
                SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_count,
                ROUND((SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 2) as attendance_percentage
             FROM attendance a
             JOIN classes c ON a.class_id = c.id
             WHERE MONTH(a.date) = ? AND YEAR(a.date) = ?
             GROUP BY a.class_id
             ORDER BY c.name",
            [$month, $year]
        );
    }
    
    public function deleteAttendance($studentId, $classId, $subjectId, $date) {
        return $this->db->query(
            "DELETE FROM attendance 
             WHERE student_id = ? AND class_id = ? AND subject_id = ? AND date = ?",
            [$studentId, $classId, $subjectId, $date]
        );
    }
}