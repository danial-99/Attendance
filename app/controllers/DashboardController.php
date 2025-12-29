<?php
/**
 * Dashboard Controller
 */

class DashboardController extends Controller {
    
    public function index() {
        $user = $this->getCurrentUser();
        
        switch ($user['role']) {
            case 'admin':
                $this->adminDashboard();
                break;
            case 'teacher':
                $this->teacherDashboard();
                break;
            case 'student':
                $this->studentDashboard();
                break;
            default:
                $this->redirect('login');
        }
    }
    
    private function adminDashboard() {
        // Get dashboard statistics
        $stats = [
            'total_students' => $this->db->fetch("SELECT COUNT(*) as count FROM students")['count'],
            'total_teachers' => $this->db->fetch("SELECT COUNT(*) as count FROM teachers")['count'],
            'total_classes' => $this->db->fetch("SELECT COUNT(*) as count FROM classes")['count'],
            'total_subjects' => $this->db->fetch("SELECT COUNT(*) as count FROM subjects")['count']
        ];
        
        // Recent attendance
        $recentAttendance = $this->db->fetchAll(
            "SELECT a.date, c.name as class_name, s.name as subject_name,
                    COUNT(*) as total_students,
                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count
             FROM attendance a
             JOIN classes c ON a.class_id = c.id
             JOIN subjects s ON a.subject_id = s.id
             WHERE a.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAYS)
             GROUP BY a.date, a.class_id, a.subject_id
             ORDER BY a.date DESC
             LIMIT 10"
        );
        
        $this->view('dashboard/admin', [
            'user' => $this->getCurrentUser(),
            'stats' => $stats,
            'recent_attendance' => $recentAttendance
        ]);
    }
    
    private function teacherDashboard() {
        $teacherModel = new Teacher();
        $teacher = $teacherModel->findByUserId($_SESSION['user_id']);
        
        if (!$teacher) {
            die("Teacher profile not found");
        }
        
        // Get teacher assignments
        $assignments = $teacherModel->getAssignments($teacher['id']);
        
        // Get today's attendance summary
        $todayAttendance = [];
        foreach ($assignments as $assignment) {
            $attendance = $this->db->fetchAll(
                "SELECT a.status, COUNT(*) as count
                 FROM attendance a
                 WHERE a.class_id = ? AND a.subject_id = ? AND a.date = CURDATE()
                 GROUP BY a.status",
                [$assignment['class_id'], $assignment['subject_id']]
            );
            
            $todayAttendance[$assignment['class_id'] . '_' . $assignment['subject_id']] = $attendance;
        }
        
        $this->view('dashboard/teacher', [
            'user' => $this->getCurrentUser(),
            'teacher' => $teacher,
            'assignments' => $assignments,
            'today_attendance' => $todayAttendance
        ]);
    }
    
    private function studentDashboard() {
        $studentModel = new Student();
        $student = $studentModel->findByUserId($_SESSION['user_id']);
        
        if (!$student) {
            die("Student profile not found");
        }
        
        // Get attendance statistics
        $currentMonth = date('n');
        $currentYear = date('Y');
        
        $monthlyStats = $studentModel->getAttendanceStats($student['id'], $currentMonth, $currentYear);
        $overallStats = $studentModel->getAttendanceStats($student['id']);
        
        // Get recent attendance
        $recentAttendance = $this->db->fetchAll(
            "SELECT a.*, c.name as class_name, s.name as subject_name
             FROM attendance a
             JOIN classes c ON a.class_id = c.id
             JOIN subjects s ON a.subject_id = s.id
             WHERE a.student_id = ?
             ORDER BY a.date DESC
             LIMIT 10",
            [$student['id']]
        );
        
        $this->view('dashboard/student', [
            'user' => $this->getCurrentUser(),
            'student' => $student,
            'monthly_stats' => $monthlyStats,
            'overall_stats' => $overallStats,
            'recent_attendance' => $recentAttendance
        ]);
    }
}