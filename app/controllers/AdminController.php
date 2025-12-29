<?php
/**
 * Admin Controller
 */

class AdminController extends Controller {
    private $userModel;
    private $studentModel;
    private $teacherModel;
    private $attendanceModel;
    
    public function __construct() {
        parent::__construct();
        $this->requireRole('admin');
        $this->userModel = new User();
        $this->studentModel = new Student();
        $this->teacherModel = new Teacher();
        $this->attendanceModel = new Attendance();
    }
    
    public function users() {
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create_user':
                    $this->createUser();
                    break;
                case 'update_user':
                    $this->updateUser();
                    break;
                case 'delete_user':
                    $this->deleteUser();
                    break;
            }
        }
        
        // Get all users with their profiles
        $users = $this->db->fetchAll(
            "SELECT u.*, 
                    CASE 
                        WHEN u.role = 'student' THEN CONCAT(s.first_name, ' ', s.last_name)
                        WHEN u.role = 'teacher' THEN CONCAT(t.first_name, ' ', t.last_name)
                        ELSE 'Admin User'
                    END as full_name,
                    CASE 
                        WHEN u.role = 'student' THEN s.student_id
                        ELSE NULL
                    END as student_id,
                    CASE 
                        WHEN u.role = 'student' THEN c.name
                        ELSE NULL
                    END as class_name
             FROM users u
             LEFT JOIN students s ON u.id = s.user_id
             LEFT JOIN teachers t ON u.id = t.user_id
             LEFT JOIN classes c ON s.class_id = c.id
             ORDER BY u.created_at DESC"
        );
        
        // Get classes for student creation
        $classes = $this->db->fetchAll("SELECT * FROM classes ORDER BY name");
        
        $this->view('admin/users', [
            'users' => $users,
            'classes' => $classes,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    public function classes() {
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create_class':
                    $this->createClass();
                    break;
                case 'update_class':
                    $this->updateClass();
                    break;
                case 'delete_class':
                    $this->deleteClass();
                    break;
            }
        }
        
        $classes = $this->db->fetchAll(
            "SELECT c.*, COUNT(s.id) as student_count
             FROM classes c
             LEFT JOIN students s ON c.id = s.class_id
             GROUP BY c.id
             ORDER BY c.name"
        );
        
        $this->view('admin/classes', [
            'classes' => $classes,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    public function subjects() {
        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrf();
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'create_subject':
                    $this->createSubject();
                    break;
                case 'update_subject':
                    $this->updateSubject();
                    break;
                case 'delete_subject':
                    $this->deleteSubject();
                    break;
                case 'assign_teacher':
                    $this->assignTeacher();
                    break;
            }
        }
        
        $subjects = $this->db->fetchAll("SELECT * FROM subjects ORDER BY name");
        $teachers = $this->teacherModel->getAll();
        $classes = $this->db->fetchAll("SELECT * FROM classes ORDER BY name");
        
        // Get teacher assignments
        $assignments = $this->db->fetchAll(
            "SELECT ta.*, t.first_name, t.last_name, c.name as class_name, s.name as subject_name
             FROM teacher_assignments ta
             JOIN teachers t ON ta.teacher_id = t.id
             JOIN classes c ON ta.class_id = c.id
             JOIN subjects s ON ta.subject_id = s.id
             ORDER BY t.first_name, c.name, s.name"
        );
        
        $this->view('admin/subjects', [
            'subjects' => $subjects,
            'teachers' => $teachers,
            'classes' => $classes,
            'assignments' => $assignments,
            'csrf_token' => $this->generateCsrf()
        ]);
    }
    
    public function reports() {
        $reportType = $_GET['type'] ?? 'daily';
        $date = $_GET['date'] ?? date('Y-m-d');
        $month = $_GET['month'] ?? date('n');
        $year = $_GET['year'] ?? date('Y');
        $classId = $_GET['class'] ?? '';
        
        $data = [
            'report_type' => $reportType,
            'date' => $date,
            'month' => $month,
            'year' => $year,
            'class_id' => $classId,
            'classes' => $this->db->fetchAll("SELECT * FROM classes ORDER BY name")
        ];
        
        switch ($reportType) {
            case 'daily':
                $data['report_data'] = $this->attendanceModel->getDailyAttendanceReport($date);
                break;
            case 'monthly':
                $data['report_data'] = $this->attendanceModel->getMonthlyStats($month, $year);
                break;
            case 'class':
                if ($classId) {
                    $startDate = "$year-$month-01";
                    $endDate = date('Y-m-t', strtotime($startDate));
                    $data['report_data'] = $this->attendanceModel->getClassAttendanceReport($classId, $startDate, $endDate);
                }
                break;
        }
        
        $this->view('admin/reports', $data);
    }
    
    private function createUser() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        
        if (empty($email) || empty($password) || empty($role) || empty($firstName) || empty($lastName)) {
            $_SESSION['error'] = 'All fields are required';
            return;
        }
        
        // Check if email exists
        if ($this->userModel->findByEmail($email)) {
            $_SESSION['error'] = 'Email already exists';
            return;
        }
        
        try {
            $userId = $this->userModel->create([
                'email' => $email,
                'password' => $password,
                'role' => $role
            ]);
            
            // Create profile based on role
            if ($role === 'student') {
                $studentId = $_POST['student_id'] ?? '';
                $classId = $_POST['class_id'] ?? '';
                
                if (empty($studentId) || empty($classId)) {
                    throw new Exception('Student ID and Class are required for students');
                }
                
                $this->studentModel->create([
                    'user_id' => $userId,
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'class_id' => $classId,
                    'phone' => $_POST['phone'] ?? null,
                    'address' => $_POST['address'] ?? null
                ]);
            } elseif ($role === 'teacher') {
                $this->teacherModel->create([
                    'user_id' => $userId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $_POST['phone'] ?? null,
                    'address' => $_POST['address'] ?? null
                ]);
            }
            
            $_SESSION['success'] = 'User created successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error creating user: ' . $e->getMessage();
        }
    }
    
    private function createClass() {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        if (empty($name)) {
            $_SESSION['error'] = 'Class name is required';
            return;
        }
        
        try {
            $this->db->query(
                "INSERT INTO classes (name, description) VALUES (?, ?)",
                [$name, $description]
            );
            $_SESSION['success'] = 'Class created successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error creating class';
        }
    }
    
    private function createSubject() {
        $name = trim($_POST['name'] ?? '');
        $code = trim($_POST['code'] ?? '');
        
        if (empty($name) || empty($code)) {
            $_SESSION['error'] = 'Subject name and code are required';
            return;
        }
        
        try {
            $this->db->query(
                "INSERT INTO subjects (name, code) VALUES (?, ?)",
                [$name, $code]
            );
            $_SESSION['success'] = 'Subject created successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error creating subject';
        }
    }
    
    private function assignTeacher() {
        $teacherId = $_POST['teacher_id'] ?? '';
        $classId = $_POST['class_id'] ?? '';
        $subjectId = $_POST['subject_id'] ?? '';
        
        if (empty($teacherId) || empty($classId) || empty($subjectId)) {
            $_SESSION['error'] = 'All fields are required for assignment';
            return;
        }
        
        try {
            $this->teacherModel->assignToClass($teacherId, $classId, $subjectId);
            $_SESSION['success'] = 'Teacher assigned successfully';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error assigning teacher';
        }
    }
}