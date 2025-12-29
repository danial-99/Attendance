<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?route=login');
    exit;
}

$user_role = $_SESSION['user_role'] ?? 'student';
$user_email = $_SESSION['user_email'] ?? 'Unknown';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Attendance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-graduation-cap me-2"></i>
                Attendance Portal
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Welcome, <?= htmlspecialchars($user_email) ?> (<?= ucfirst($user_role) ?>)
                </span>
                <a href="index.php?route=logout" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>
                    Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">
                        <i class="fas fa-check-circle me-2"></i>
                        Login Successful!
                    </h4>
                    <p>You have successfully logged in to the Attendance Management Portal.</p>
                    <hr>
                    <p class="mb-0">
                        <strong>User:</strong> <?= htmlspecialchars($user_email) ?><br>
                        <strong>Role:</strong> <?= ucfirst($user_role) ?><br>
                        <strong>Session ID:</strong> <?= $_SESSION['user_id'] ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-user me-2"></i>
                        Profile
                    </div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
                        <p><strong>Role:</strong> <?= ucfirst($user_role) ?></p>
                        <p><strong>Status:</strong> <span class="badge bg-success">Active</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-cog me-2"></i>
                        System Status
                    </div>
                    <div class="card-body">
                        <p><strong>Login System:</strong> <span class="badge bg-success">Working</span></p>
                        <p><strong>Session:</strong> <span class="badge bg-success">Active</span></p>
                        <p><strong>Database:</strong> <span class="badge bg-success">Connected</span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card border-warning">
                    <div class="card-header bg-warning text-dark">
                        <i class="fas fa-tools me-2"></i>
                        Quick Actions
                    </div>
                    <div class="card-body">
                        <a href="index.php?route=login" class="btn btn-outline-primary btn-sm mb-2 d-block">
                            <i class="fas fa-sign-in-alt me-1"></i>
                            Login Page
                        </a>
                        <a href="index.php?route=logout" class="btn btn-outline-danger btn-sm d-block">
                            <i class="fas fa-sign-out-alt me-1"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($user_role === 'admin'): ?>
        <div class="row">
            <div class="col-12">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-crown me-2"></i>
                        Admin Features
                    </div>
                    <div class="card-body">
                        <p>As an admin, you would have access to:</p>
                        <ul>
                            <li>User Management</li>
                            <li>Class Management</li>
                            <li>Subject Management</li>
                            <li>Attendance Reports</li>
                            <li>System Configuration</li>
                        </ul>
                        <p class="text-muted">
                            <small>Note: Full admin features require complete database setup.</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>