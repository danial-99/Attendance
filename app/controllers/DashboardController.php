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
        // Get dashboard statistics - check if tables exist first
        $stats = [];
        
        try {
            $stats['total_students'] = $this->db->fetch("SELECT COUNT(*) as count FROM students")['count'] ?? 0;
        } catch (Exception $e) {
            $stats['total_students'] = 0;
        }
        
        try {
            $stats['total_teachers'] = $this->db->fetch("SELECT COUNT(*) as count FROM teachers")['count'] ?? 0;
        } catch (Exception $e) {
            $stats['total_teachers'] = 0;
        }
        
        try {
            $stats['total_classes'] = $this->db->fetch("SELECT COUNT(*) as count FROM classes")['count'] ?? 0;
        } catch (Exception $e) {
            $stats['total_classes'] = 0;
        }
        
        try {
            $stats['total_subjects'] = $this->db->fetch("SELECT COUNT(*) as count FROM subjects")['count'] ?? 0;
        } catch (Exception $e) {
            $stats['total_subjects'] = 0;
        }
        
        // Recent attendance - simplified query
        $recentAttendance = [];
        try {
            $recentAttendance = $this->db->fetchAll(
                "SELECT a.date, c.name as class_name, s.name as subject_name,
                        COUNT(*) as total_students,
                        SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_count
                 FROM attendance a
                 JOIN classes c ON a.class_id = c.id
                 JOIN subjects s ON a.subject_id = s.id
                 WHERE a.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                 GROUP BY a.date, a.class_id, a.subject_id
                 ORDER BY a.date DESC
                 LIMIT 10"
            );
        } catch (Exception $e) {
            // If attendance table doesn't exist or query fails, use empty array
            $recentAttendance = [];
        }
        
        $this->view('dashboard/admin', [
            'user' => $this->getCurrentUser(),
            'stats' => $stats,
            'recent_attendance' => $recentAttendance
        ]);
    }
    
    private function teacherDashboard() {
        try {
            $teacherModel = new Teacher();
            $teacher = $teacherModel->findByUserId($_SESSION['user_id']);
            
            if (!$teacher) {
                // If teacher profile doesn't exist, show basic dashboard
                $this->view('dashboard/teacher', [
                    'user' => $this->getCurrentUser(),
                    'teacher' => null,
                    'assignments' => [],
                    'today_attendance' => []
                ]);
                return;
            }
            
            // Get teacher assignments
            $assignments = $teacherModel->getAssignments($teacher['id']);
            
            // Get today's attendance summary
            $todayAttendance = [];
            foreach ($assignments as $assignment) {
                try {
                    $attendance = $this->db->fetchAll(
                        "SELECT a.status, COUNT(*) as count
                         FROM attendance a
                         WHERE a.class_id = ? AND a.subject_id = ? AND a.date = CURDATE()
                         GROUP BY a.status",
                        [$assignment['class_id'], $assignment['subject_id']]
                    );
                    
                    $todayAttendance[$assignment['class_id'] . '_' . $assignment['subject_id']] = $attendance;
                } catch (Exception $e) {
                    // Skip if attendance table doesn't exist
                    continue;
                }
            }
            
            $this->view('dashboard/teacher', [
                'user' => $this->getCurrentUser(),
                'teacher' => $teacher,
                'assignments' => $assignments,
                'today_attendance' => $todayAttendance
            ]);
        } catch (Exception $e) {
            // Fallback to basic teacher dashboard
            $this->view('dashboard/teacher', [
                'user' => $this->getCurrentUser(),
                'teacher' => null,
                'assignments' => [],
                'today_attendance' => []
            ]);
        }
    }
    
    private function studentDashboard() {
        try {
            $studentModel = new Student();
            $student = $studentModel->findByUserId($_SESSION['user_id']);
            
            if (!$student) {
                // If student profile doesn't exist, show basic dashboard
                $this->view('dashboard/student', [
                    'user' => $this->getCurrentUser(),
                    'student' => null,
                    'monthly_stats' => [],
                    'overall_stats' => [],
                    'recent_attendance' => []
                ]);
                return;
            }
            
            // Get attendance statistics
            $currentMonth = date('n');
            $currentYear = date('Y');
            
            $monthlyStats = $studentModel->getAttendanceStats($student['id'], $currentMonth, $currentYear);
            $overallStats = $studentModel->getAttendanceStats($student['id']);
            
            // Get recent attendance
            $recentAttendance = [];
            try {
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
            } catch (Exception $e) {
                // Skip if attendance table doesn't exist
                $recentAttendance = [];
            }
            
            $this->view('dashboard/student', [
                'user' => $this->getCurrentUser(),
                'student' => $student,
                'monthly_stats' => $monthlyStats,
                'overall_stats' => $overallStats,
                'recent_attendance' => $recentAttendance
            ]);
        } catch (Exception $e) {
            // Fallback to basic student dashboard
            $this->view('dashboard/student', [
                'user' => $this->getCurrentUser(),
                'student' => null,
                'monthly_stats' => [],
                'overall_stats' => [],
                'recent_attendance' => []
            ]);
        }
    }
}