<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Attendance Management Portal' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #adb5bd;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #495057;
        }
        .main-content {
            margin-left: 0;
        }
        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }
        }
        .attendance-card {
            transition: transform 0.2s;
        }
        .attendance-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">Attendance Portal</h5>
                        <small class="text-muted"><?= ucfirst($_SESSION['user_role']) ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('dashboard') ?>">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('admin/users') ?>">
                                <i class="fas fa-users me-2"></i>
                                Manage Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('admin/classes') ?>">
                                <i class="fas fa-school me-2"></i>
                                Manage Classes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('admin/subjects') ?>">
                                <i class="fas fa-book me-2"></i>
                                Manage Subjects
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('admin/reports') ?>">
                                <i class="fas fa-chart-bar me-2"></i>
                                Reports
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['user_role'] === 'teacher'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('teacher/attendance') ?>">
                                <i class="fas fa-clipboard-check me-2"></i>
                                Mark Attendance
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($_SESSION['user_role'] === 'student'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('student/attendance') ?>">
                                <i class="fas fa-calendar-check me-2"></i>
                                My Attendance
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <hr class="text-muted">
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= $url('logout') ?>">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?= $pageTitle ?? 'Dashboard' ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="text-muted">Welcome, <?= $_SESSION['user_email'] ?></span>
                        </div>
                    </div>
                </div>
    <?php endif; ?>