<?php
/**
 * API Controller for AJAX requests and exports
 */

class ApiController extends Controller {
    private $attendanceModel;
    private $studentModel;
    
    public function __construct() {
        parent::__construct();
        $this->attendanceModel = new Attendance();
        $this->studentModel = new Student();
    }
    
    public function saveAttendance() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            $result = $this->attendanceModel->markAttendance([
                'student_id' => $input['student_id'],
                'class_id' => $input['class_id'],
                'subject_id' => $input['subject_id'],
                'teacher_id' => $input['teacher_id'],
                'date' => $input['date'],
                'status' => $input['status'],
                'remarks' => $input['remarks'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Attendance saved']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save attendance: ' . $e->getMessage()]);
        }
    }
    
    public function exportCsv() {
        $type = $_GET['type'] ?? 'student';
        $studentId = $_GET['student'] ?? null;
        $classId = $_GET['class'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance_report_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        try {
            if ($type === 'student' && $studentId) {
                $this->exportStudentAttendance($output, $studentId, $startDate, $endDate);
            } elseif ($type === 'class' && $classId) {
                $this->exportClassAttendance($output, $classId, $startDate, $endDate);
            } elseif ($type === 'daily') {
                $this->exportDailyAttendance($output, $startDate);
            } else {
                fputcsv($output, ['Error: Invalid export parameters']);
            }
        } catch (Exception $e) {
            fputcsv($output, ['Error: ' . $e->getMessage()]);
        }
        
        fclose($output);
    }
    
    private function exportStudentAttendance($output, $studentId, $startDate, $endDate) {
        // Get student info
        $student = $this->studentModel->findById($studentId);
        if (!$student) {
            throw new Exception('Student not found');
        }
        
        // CSV headers
        fputcsv($output, ['Student Attendance Report']);
        fputcsv($output, ['Student: ' . $student['first_name'] . ' ' . $student['last_name']]);
        fputcsv($output, ['Student ID: ' . $student['student_id']]);
        fputcsv($output, ['Class: ' . $student['class_name']]);
        fputcsv($output, ['Period: ' . $startDate . ' to ' . $endDate]);
        fputcsv($output, []);
        fputcsv($output, ['Date', 'Subject', 'Status', 'Remarks']);
        
        // Get attendance data
        $attendance = $this->attendanceModel->getStudentAttendance($studentId, $startDate, $endDate);
        
        foreach ($attendance as $record) {
            fputcsv($output, [
                $record['date'],
                $record['subject_name'],
                ucfirst($record['status']),
                $record['remarks'] ?? ''
            ]);
        }
        
        // Add summary
        $stats = $this->studentModel->getAttendanceStats($studentId);
        if ($stats && $stats['total_days'] > 0) {
            fputcsv($output, []);
            fputcsv($output, ['Summary']);
            fputcsv($output, ['Total Days', $stats['total_days']]);
            fputcsv($output, ['Present Days', $stats['present_days']]);
            fputcsv($output, ['Absent Days', $stats['absent_days']]);
            fputcsv($output, ['Late Days', $stats['late_days']]);
            fputcsv($output, ['Attendance Percentage', $stats['attendance_percentage'] . '%']);
        }
    }
    
    private function exportClassAttendance($output, $classId, $startDate, $endDate) {
        // Get class info
        $class = $this->db->fetch("SELECT * FROM classes WHERE id = ?", [$classId]);
        if (!$class) {
            throw new Exception('Class not found');
        }
        
        // CSV headers
        fputcsv($output, ['Class Attendance Report']);
        fputcsv($output, ['Class: ' . $class['name']]);
        fputcsv($output, ['Period: ' . $startDate . ' to ' . $endDate]);
        fputcsv($output, []);
        fputcsv($output, ['Student ID', 'Student Name', 'Total Days', 'Present', 'Absent', 'Late', 'Attendance %']);
        
        // Get attendance data
        $report = $this->attendanceModel->getClassAttendanceReport($classId, $startDate, $endDate);
        
        foreach ($report as $record) {
            fputcsv($output, [
                $record['student_id'],
                $record['first_name'] . ' ' . $record['last_name'],
                $record['total_days'],
                $record['present_days'],
                $record['absent_days'],
                $record['late_days'],
                $record['attendance_percentage'] . '%'
            ]);
        }
    }
    
    private function exportDailyAttendance($output, $date) {
        // CSV headers
        fputcsv($output, ['Daily Attendance Report']);
        fputcsv($output, ['Date: ' . $date]);
        fputcsv($output, []);
        fputcsv($output, ['Class', 'Subject', 'Total Students', 'Present', 'Absent', 'Late', 'Attendance %']);
        
        // Get attendance data
        $report = $this->attendanceModel->getDailyAttendanceReport($date);
        
        foreach ($report as $record) {
            $percentage = ($record['present_count'] / $record['total_students']) * 100;
            fputcsv($output, [
                $record['class_name'],
                $record['subject_name'],
                $record['total_students'],
                $record['present_count'],
                $record['absent_count'],
                $record['late_count'],
                number_format($percentage, 2) . '%'
            ]);
        }
    }
}