<?php 
$title = 'Attendance Management';
$pageTitle = 'Attendance Management';
include APP_PATH . '/views/layout/header.php'; 
?>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Class Assignments</h6>
            </div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        No class assignments found. Please contact the administrator to assign classes and subjects to your account.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($assignments as $assignment): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card attendance-card border-left-primary h-100">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-school me-2"></i>
                                        <?= htmlspecialchars($assignment['class_name']) ?>
                                    </h6>
                                    <p class="card-text">
                                        <strong><i class="fas fa-book me-1"></i> Subject:</strong> 
                                        <?= htmlspecialchars($assignment['subject_name']) ?><br>
                                        <strong><i class="fas fa-code me-1"></i> Code:</strong> 
                                        <?= htmlspecialchars($assignment['subject_code']) ?>
                                    </p>
                                    
                                    <div class="mt-3">
                                        <div class="row">
                                            <div class="col-6">
                                                <a href="<?= $url('teacher/mark-attendance?class=' . $assignment['class_id'] . '&subject=' . $assignment['subject_id']) ?>" 
                                                   class="btn btn-primary btn-sm w-100">
                                                    <i class="fas fa-clipboard-check me-1"></i>
                                                    Mark Today
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="<?= $url('teacher/mark-attendance?class=' . $assignment['class_id'] . '&subject=' . $assignment['subject_id'] . '&date=' . date('Y-m-d', strtotime('-1 day'))) ?>" 
                                                   class="btn btn-outline-secondary btn-sm w-100">
                                                    <i class="fas fa-history me-1"></i>
                                                    Yesterday
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2">
                                        <a href="<?= $url('teacher/mark-attendance?class=' . $assignment['class_id'] . '&subject=' . $assignment['subject_id'] . '&view=history') ?>" 
                                           class="btn btn-outline-info btn-sm w-100">
                                            <i class="fas fa-chart-line me-1"></i>
                                            View History
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-left-success">
                            <div class="card-body">
                                <i class="fas fa-calendar-day fa-2x text-success mb-2"></i>
                                <h6>Today's Attendance</h6>
                                <p class="text-muted small">Mark attendance for today's classes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-left-warning">
                            <div class="card-body">
                                <i class="fas fa-edit fa-2x text-warning mb-2"></i>
                                <h6>Edit Previous</h6>
                                <p class="text-muted small">Modify attendance for previous dates</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-left-info">
                            <div class="card-body">
                                <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                <h6>View Reports</h6>
                                <p class="text-muted small">Generate attendance reports</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center border-left-primary">
                            <div class="card-body">
                                <i class="fas fa-download fa-2x text-primary mb-2"></i>
                                <h6>Export Data</h6>
                                <p class="text-muted small">Download attendance as CSV</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Instructions -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Instructions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Marking Attendance</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success me-2"></i> Click "Mark Today" to mark attendance for current date</li>
                            <li><i class="fas fa-clock text-warning me-2"></i> Use "Yesterday" for previous day attendance</li>
                            <li><i class="fas fa-save text-info me-2"></i> Remember to save after marking all students</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-info">Attendance Status</h6>
                        <ul class="list-unstyled">
                            <li><span class="badge bg-success me-2">Present</span> Student attended the class</li>
                            <li><span class="badge bg-danger me-2">Absent</span> Student was not present</li>
                            <li><span class="badge bg-warning me-2">Late</span> Student arrived late</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layout/footer.php'; ?>