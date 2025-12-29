<?php
/**
 * Teacher Controller
 */

class TeacherController extends Controller {
    private $teacherModel;
    private $attendanceModel;
    private $studentModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('teacher');
        $this->teacherModel = new Teacher();
        $this->attendanceModel = new Attendance();
        $this->studentModel = new Student();
    }
    
    public function attendance() {
        $teacher = $this->teacherModel->findByUserId($_SESSION['user_id']);
        if (!$teacher) {
            die("Teacher profile not found");
        }
        
        $assignments = $this->teacherModel->getAssignments($teacher['id']);
        
        $this->view('teacher/attendance', [
            'teacher' => $teacher,
            'assignments' => $assignments
        ]);
    }
    
    public function markAttendance() {
        $teacher = $this->teacherModel->findByUserId($_SESSION['user_id']);
        if (!$teacher) {
            die("Teacher profile not found");
        }
        
        $classId = $_GET['class'] ?? null;
        $subjectId = $_GET['subject'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d');
        
        if (!$classId || !$subjectId) {
            $this->redirect('teacher/attendance');
        }
        
        // Verify teacher has access to this class/subject
        $hasAccess = $this->db->fetch(
            "SELECT 1 FROM teacher_assignments 
             WHERE teacher_id = ? AND class_id = ? AND subject_id = ?",
            [$teacher['id'], $classId, $subjectId]
        );
        
        if (!$hasAccess) {
            die("Access denied to this class/subject");
        }
        
        // Get class and subject info
        $classInfo = $this->db->fetch("SELECT * FROM classes WHERE id = ?", [$classId]);
        $subjectInfo = $this->db->fetch("SELECT * FROM subjects WHERE id = ?", [$subjectId]);
        
        // Get students in this class
        $students = $this->studentModel->getByClass($classId);
        
        // Get existing attendance for this date
        $existingAttendance = $this->attendanceModel->getAttendanceByDate($classId, $subjectId, $date);
        $attendanceMap = [];
        foreach ($existingAttendance as $record) {
            $attendanceMap[$record['student_id']] = $record;
        }
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            
            $attendanceData = $_POST['attendance'] ?? [];
            $success = true;
            
            foreach ($students as $student) {
                $status = $attendanceData[$student['id']] ?? 'absent';
                $remarks = $_POST['remarks'][$student['id']] ?? '';
                
                try {
                    $this->attendanceModel->markAttendance([
                        'student_id' => $student['id'],
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacher['id'],
                        'date' => $date,
                        'status' => $status,
                        'remarks' => $remarks
                    ]);
                } catch (Exception $e) {
                    $success = false;
                    error_log("Attendance marking error: " . $e->getMessage());
                }
            }
            
            if ($success) {
                $_SESSION['success'] = 'Attendance marked successfully';
            } else {
                $_SESSION['error'] = 'Error marking attendance';
            }
            
            $this->redirect("teacher/mark-attendance?class=$classId&subject=$subjectId&date=$date");
        }
        
        $this->view('teacher/mark-attendance', [
            'teacher' => $teacher,
            'class' => $classInfo,
            'subject' => $subjectInfo,
            'students' => $students,
            'attendance_map' => $attendanceMap,
            'date' => $date,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
}