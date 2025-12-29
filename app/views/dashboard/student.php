<?php 
$title = 'Student Dashboard';
$pageTitle = 'Student Dashboard';
include APP_PATH . '/views/layout/header.php'; 
?>

<div class="row">
    <div class="col-lg-8">
        <!-- Attendance Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Attendance Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Monthly Stats -->
                    <div class="col-md-6">
                        <h6 class="text-primary">This Month (<?= date('F Y') ?>)</h6>
                        <?php if ($monthly_stats && $monthly_stats['total_days'] > 0): ?>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= $monthly_stats['attendance_percentage'] ?>%">
                                <?= $monthly_stats['attendance_percentage'] ?>%
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-success"><?= $monthly_stats['present_days'] ?></h5>
                                <small class="text-muted">Present</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-danger"><?= $monthly_stats['absent_days'] ?></h5>
                                <small class="text-muted">Absent</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-warning"><?= $monthly_stats['late_days'] ?></h5>
                                <small class="text-muted">Late</small>
                            </div>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No attendance records for this month.</p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Overall Stats -->
                    <div class="col-md-6">
                        <h6 class="text-primary">Overall</h6>
                        <?php if ($overall_stats && $overall_stats['total_days'] > 0): ?>
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: <?= $overall_stats['attendance_percentage'] ?>%">
                                <?= $overall_stats['attendance_percentage'] ?>%
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-success"><?= $overall_stats['present_days'] ?></h5>
                                <small class="text-muted">Present</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-danger"><?= $overall_stats['absent_days'] ?></h5>
                                <small class="text-muted">Absent</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-warning"><?= $overall_stats['late_days'] ?></h5>
                                <small class="text-muted">Late</small>
                            </div>
                        </div>
                        <?php else: ?>
                        <p class="text-muted">No attendance records found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Attendance -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Attendance</h6>
            </div>
            <div class="card-body">
                <?php if (empty($recent_attendance)): ?>
                    <p class="text-muted">No attendance records found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_attendance as $record): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($record['date'])) ?></td>
                                    <td><?= htmlspecialchars($record['subject_name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($record['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Student Profile -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Profile</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-graduate fa-3x text-gray-300"></i>
                </div>
                <p><strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></p>
                <p><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></p>
                <p><strong>Class:</strong> <?= htmlspecialchars($student['class_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <?php if ($student['phone']): ?>
                <p><strong>Phone:</strong> <?= htmlspecialchars($student['phone']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Attendance Summary -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Attendance Summary</h6>
            </div>
            <div class="card-body">
                <?php if ($overall_stats && $overall_stats['total_days'] > 0): ?>
                <div class="text-center">
                    <div class="mb-3">
                        <h3 class="text-primary"><?= $overall_stats['attendance_percentage'] ?>%</h3>
                        <small class="text-muted">Overall Attendance</small>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-2">
                            <div class="d-flex justify-content-between">
                                <span>Total Days:</span>
                                <strong><?= $overall_stats['total_days'] ?></strong>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-success">Present:</span>
                                <strong><?= $overall_stats['present_days'] ?></strong>
                            </div>
                        </div>
                        <div class="col-12 mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-danger">Absent:</span>
                                <strong><?= $overall_stats['absent_days'] ?></strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <span class="text-warning">Late:</span>
                                <strong><?= $overall_stats['late_days'] ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <p class="text-muted text-center">No attendance data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="<?= $url('student/attendance') ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-calendar-check me-2"></i>
                            View Full Attendance
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= $url('student/attendance?month=' . date('n') . '&year=' . date('Y')) ?>" class="btn btn-info btn-block">
                            <i class="fas fa-calendar-alt me-2"></i>
                            This Month
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= $url('api/export-csv?student=' . $student['id']) ?>" class="btn btn-success btn-block">
                            <i class="fas fa-download me-2"></i>
                            Download Report
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layout/footer.php'; ?>