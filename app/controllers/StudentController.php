<?php
/**
 * Student Controller
 */

class StudentController extends Controller {
    private $studentModel;
    private $attendanceModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('student');
        $this->studentModel = new Student();
        $this->attendanceModel = new Attendance();
    }
    
    public function attendance() {
        $student = $this->studentModel->findByUserId($_SESSION['user_id']);
        if (!$student) {
            die("Student profile not found");
        }
        
        // Get filter parameters
        $month = $_GET['month'] ?? date('n');
        $year = $_GET['year'] ?? date('Y');
        $view = $_GET['view'] ?? 'monthly';
        
        // Calculate date range
        if ($view === 'monthly') {
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));
        } else {
            // Full year or custom range
            $startDate = "$year-01-01";
            $endDate = "$year-12-31";
        }
        
        // Get attendance data
        $attendance = $this->attendanceModel->getStudentAttendance($student['id'], $startDate, $endDate);
        
        // Get statistics
        $monthlyStats = $this->studentModel->getAttendanceStats($student['id'], $month, $year);
        $overallStats = $this->studentModel->getAttendanceStats($student['id']);
        
        // Group attendance by date for calendar view
        $attendanceByDate = [];
        foreach ($attendance as $record) {
            $attendanceByDate[$record['date']][] = $record;
        }
        
        $this->view('student/attendance', [
            'student' => $student,
            'attendance' => $attendance,
            'attendance_by_date' => $attendanceByDate,
            'monthly_stats' => $monthlyStats,
            'overall_stats' => $overallStats,
            'current_month' => $month,
            'current_year' => $year,
            'view' => $view
        ]);
    }
}