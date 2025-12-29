<?php 
$title = 'Teacher Dashboard';
$pageTitle = 'Teacher Dashboard';
include APP_PATH . '/views/layout/header.php'; 
?>

<div class="row">
    <div class="col-lg-8">
        <!-- My Assignments -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Class Assignments</h6>
            </div>
            <div class="card-body">
                <?php if (empty($assignments)): ?>
                    <p class="text-muted">No class assignments found. Please contact the administrator.</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($assignments as $assignment): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card attendance-card border-left-primary">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <?= htmlspecialchars($assignment['class_name']) ?>
                                    </h6>
                                    <p class="card-text">
                                        <strong>Subject:</strong> <?= htmlspecialchars($assignment['subject_name']) ?><br>
                                        <strong>Code:</strong> <?= htmlspecialchars($assignment['subject_code']) ?>
                                    </p>
                                    
                                    <?php 
                                    $key = $assignment['class_id'] . '_' . $assignment['subject_id'];
                                    $todayStats = $today_attendance[$key] ?? [];
                                    ?>
                                    
                                    <?php if (!empty($todayStats)): ?>
                                    <div class="mt-2">
                                        <small class="text-muted">Today's Attendance:</small>
                                        <?php foreach ($todayStats as $stat): ?>
                                        <span class="badge bg-<?= $stat['status'] === 'present' ? 'success' : ($stat['status'] === 'absent' ? 'danger' : 'warning') ?> me-1">
                                            <?= ucfirst($stat['status']) ?>: <?= $stat['count'] ?>
                                        </span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-3">
                                        <a href="<?= $url('teacher/mark-attendance?class=' . $assignment['class_id'] . '&subject=' . $assignment['subject_id']) ?>" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-clipboard-check me-1"></i>
                                            Mark Attendance
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
    
    <div class="col-lg-4">
        <!-- Teacher Profile -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Profile</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-user-circle fa-3x text-gray-300"></i>
                </div>
                <p><strong>Name:</strong> <?= htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <?php if ($teacher['phone']): ?>
                <p><strong>Phone:</strong> <?= htmlspecialchars($teacher['phone']) ?></p>
                <?php endif; ?>
                <p><strong>Total Assignments:</strong> <?= count($assignments) ?></p>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-right">
                            <h4 class="text-primary"><?= count($assignments) ?></h4>
                            <small class="text-muted">Classes</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">
                            <?php
                            $totalToday = 0;
                            foreach ($today_attendance as $stats) {
                                foreach ($stats as $stat) {
                                    $totalToday += $stat['count'];
                                }
                            }
                            echo $totalToday;
                            ?>
                        </h4>
                        <small class="text-muted">Today's Records</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="<?= $url('teacher/attendance') ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-clipboard-check me-2"></i>
                            Mark Attendance
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= $url('teacher/attendance?view=history') ?>" class="btn btn-info btn-block">
                            <i class="fas fa-history me-2"></i>
                            View History
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="<?= $url('teacher/attendance?view=reports') ?>" class="btn btn-success btn-block">
                            <i class="fas fa-chart-line me-2"></i>
                            View Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layout/footer.php'; ?>