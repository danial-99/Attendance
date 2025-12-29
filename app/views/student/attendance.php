<?php 
$title = 'My Attendance';
$pageTitle = 'My Attendance';
include APP_PATH . '/views/layout/header.php'; 
?>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">Attendance Filters</h6>
                    </div>
                    <div class="col-auto">
                        <a href="<?= $url('api/export-csv?type=student&student=' . $student['id'] . '&start_date=' . $current_year . '-' . str_pad($current_month, 2, '0', STR_PAD_LEFT) . '-01&end_date=' . date('Y-m-t', mktime(0, 0, 0, $current_month, 1, $current_year))) ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="view" class="form-label">View</label>
                        <select class="form-control" id="view" name="view">
                            <option value="monthly" <?= $view === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                            <option value="yearly" <?= $view === 'yearly' ? 'selected' : '' ?>>Yearly</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-control" id="month" name="month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $current_month == $i ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-control" id="year" name="year">
                            <?php for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++): ?>
                            <option value="<?= $i ?>" <?= $current_year == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Monthly Attendance (<?= date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year)) ?>)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $monthly_stats && $monthly_stats['total_days'] > 0 ? $monthly_stats['attendance_percentage'] . '%' : 'N/A' ?>
                        </div>
                        <?php if ($monthly_stats && $monthly_stats['total_days'] > 0): ?>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: <?= $monthly_stats['attendance_percentage'] ?>%"></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Overall Attendance
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $overall_stats && $overall_stats['total_days'] > 0 ? $overall_stats['attendance_percentage'] . '%' : 'N/A' ?>
                        </div>
                        <?php if ($overall_stats && $overall_stats['total_days'] > 0): ?>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?= $overall_stats['attendance_percentage'] ?>%"></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<?php if ($monthly_stats && $monthly_stats['total_days'] > 0): ?>
<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Monthly Statistics - <?= date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year)) ?>
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border-right">
                            <h4 class="text-primary"><?= $monthly_stats['total_days'] ?></h4>
                            <small class="text-muted">Total Days</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-right">
                            <h4 class="text-success"><?= $monthly_stats['present_days'] ?></h4>
                            <small class="text-muted">Present</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border-right">
                            <h4 class="text-danger"><?= $monthly_stats['absent_days'] ?></h4>
                            <small class="text-muted">Absent</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning"><?= $monthly_stats['late_days'] ?></h4>
                        <small class="text-muted">Late</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Attendance Records -->
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Attendance Records</h6>
            </div>
            <div class="card-body">
                <?php if (empty($attendance)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No attendance records found for the selected period.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance as $record): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($record['date'])) ?></td>
                                    <td>
                                        <?= htmlspecialchars($record['subject_name']) ?>
                                        <small class="text-muted d-block"><?= htmlspecialchars($record['subject_code']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $record['status'] === 'present' ? 'success' : ($record['status'] === 'absent' ? 'danger' : 'warning') ?>">
                                            <?= ucfirst($record['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= $record['remarks'] ? htmlspecialchars($record['remarks']) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include APP_PATH . '/views/layout/footer.php'; ?>