<?php 
$title = 'Attendance Reports';
$pageTitle = 'Attendance Reports';
include APP_PATH . '/views/layout/header.php'; 
?>

<div class="row mb-4">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Report Type</label>
                        <select class="form-control" id="type" name="type" onchange="toggleFilters()">
                            <option value="daily" <?= $report_type === 'daily' ? 'selected' : '' ?>>Daily Report</option>
                            <option value="monthly" <?= $report_type === 'monthly' ? 'selected' : '' ?>>Monthly Report</option>
                            <option value="class" <?= $report_type === 'class' ? 'selected' : '' ?>>Class Report</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2" id="date-filter">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" value="<?= $date ?>">
                    </div>
                    
                    <div class="col-md-2" id="month-filter" style="display: none;">
                        <label for="month" class="form-label">Month</label>
                        <select class="form-control" id="month" name="month">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i ?>" <?= $month == $i ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
                            </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2" id="year-filter" style="display: none;">
                        <label for="year" class="form-label">Year</label>
                        <select class="form-control" id="year" name="year">
                            <?php for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++): ?>
                            <option value="<?= $i ?>" <?= $year == $i ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3" id="class-filter" style="display: none;">
                        <label for="class" class="form-label">Class</label>
                        <select class="form-control" id="class" name="class">
                            <option value="">All Classes</option>
                            <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= $class_id == $class['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Generate Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if (isset($report_data) && !empty($report_data)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <?= ucfirst($report_type) ?> Attendance Report
                            <?php if ($report_type === 'daily'): ?>
                                - <?= date('F d, Y', strtotime($date)) ?>
                            <?php elseif ($report_type === 'monthly'): ?>
                                - <?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?>
                            <?php elseif ($report_type === 'class' && $class_id): ?>
                                - <?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?>
                            <?php endif; ?>
                        </h6>
                    </div>
                    <div class="col-auto">
                        <a href="<?= $url('api/export-csv?' . http_build_query($_GET)) ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <?php if ($report_type === 'daily'): ?>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Total Students</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Attendance %</th>
                                <?php elseif ($report_type === 'monthly'): ?>
                                    <th>Class</th>
                                    <th>Total Students</th>
                                    <th>Total Records</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Attendance %</th>
                                <?php elseif ($report_type === 'class'): ?>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                    <th>Total Days</th>
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Late</th>
                                    <th>Attendance %</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $record): ?>
                            <tr>
                                <?php if ($report_type === 'daily'): ?>
                                    <td><?= htmlspecialchars($record['class_name']) ?></td>
                                    <td><?= htmlspecialchars($record['subject_name']) ?></td>
                                    <td><?= $record['total_students'] ?></td>
                                    <td class="text-success"><?= $record['present_count'] ?></td>
                                    <td class="text-danger"><?= $record['absent_count'] ?></td>
                                    <td class="text-warning"><?= $record['late_count'] ?></td>
                                    <td>
                                        <?php 
                                        $percentage = ($record['present_count'] / $record['total_students']) * 100;
                                        $badgeClass = $percentage >= 80 ? 'success' : ($percentage >= 60 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?>">
                                            <?= number_format($percentage, 1) ?>%
                                        </span>
                                    </td>
                                <?php elseif ($report_type === 'monthly'): ?>
                                    <td><?= htmlspecialchars($record['class_name']) ?></td>
                                    <td><?= $record['total_students'] ?></td>
                                    <td><?= $record['total_records'] ?></td>
                                    <td class="text-success"><?= $record['present_count'] ?></td>
                                    <td class="text-danger"><?= $record['absent_count'] ?></td>
                                    <td class="text-warning"><?= $record['late_count'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $record['attendance_percentage'] >= 80 ? 'success' : ($record['attendance_percentage'] >= 60 ? 'warning' : 'danger') ?>">
                                            <?= $record['attendance_percentage'] ?>%
                                        </span>
                                    </td>
                                <?php elseif ($report_type === 'class'): ?>
                                    <td><?= htmlspecialchars($record['student_id']) ?></td>
                                    <td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td>
                                    <td><?= $record['total_days'] ?></td>
                                    <td class="text-success"><?= $record['present_days'] ?></td>
                                    <td class="text-danger"><?= $record['absent_days'] ?></td>
                                    <td class="text-warning"><?= $record['late_days'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $record['attendance_percentage'] >= 80 ? 'success' : ($record['attendance_percentage'] >= 60 ? 'warning' : 'danger') ?>">
                                            <?= $record['attendance_percentage'] ?>%
                                        </span>
                                    </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php elseif (isset($report_data)): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> No data found for the selected criteria.
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function toggleFilters() {
    const type = document.getElementById('type').value;
    const dateFilter = document.getElementById('date-filter');
    const monthFilter = document.getElementById('month-filter');
    const yearFilter = document.getElementById('year-filter');
    const classFilter = document.getElementById('class-filter');
    
    // Hide all filters first
    dateFilter.style.display = 'none';
    monthFilter.style.display = 'none';
    yearFilter.style.display = 'none';
    classFilter.style.display = 'none';
    
    // Show relevant filters based on type
    if (type === 'daily') {
        dateFilter.style.display = 'block';
    } else if (type === 'monthly') {
        monthFilter.style.display = 'block';
        yearFilter.style.display = 'block';
    } else if (type === 'class') {
        monthFilter.style.display = 'block';
        yearFilter.style.display = 'block';
        classFilter.style.display = 'block';
    }
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFilters();
});
</script>

<?php include APP_PATH . '/views/layout/footer.php'; ?>