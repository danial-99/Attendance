<?php 
$title = 'Mark Attendance';
$pageTitle = 'Mark Attendance - ' . $class['name'] . ' - ' . $subject['name'];
include APP_PATH . '/views/layout/header.php'; 
?>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= $_SESSION['success'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['success']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $_SESSION['error'] ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['error']); endif; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <?= htmlspecialchars($class['name']) ?> - <?= htmlspecialchars($subject['name']) ?>
                        </h6>
                        <small class="text-muted">Date: <?= date('F d, Y', strtotime($date)) ?></small>
                    </div>
                    <div class="col-auto">
                        <input type="date" id="attendance-date" class="form-control" value="<?= $date ?>" 
                               onchange="changeDate(this.value)">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <p class="text-muted">No students found in this class.</p>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Student ID</th>
                                        <th width="30%">Name</th>
                                        <th width="30%">Attendance</th>
                                        <th width="15%">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $index => $student): ?>
                                    <?php 
                                    $currentAttendance = $attendance_map[$student['id']] ?? null;
                                    $currentStatus = $currentAttendance ? $currentAttendance['status'] : 'present';
                                    $currentRemarks = $currentAttendance ? $currentAttendance['remarks'] : '';
                                    ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($student['student_id']) ?></td>
                                        <td><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="attendance[<?= $student['id'] ?>]" 
                                                       id="present_<?= $student['id'] ?>" value="present" 
                                                       <?= $currentStatus === 'present' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="present_<?= $student['id'] ?>">
                                                    <i class="fas fa-check"></i> Present
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="attendance[<?= $student['id'] ?>]" 
                                                       id="absent_<?= $student['id'] ?>" value="absent"
                                                       <?= $currentStatus === 'absent' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="absent_<?= $student['id'] ?>">
                                                    <i class="fas fa-times"></i> Absent
                                                </label>
                                                
                                                <input type="radio" class="btn-check" name="attendance[<?= $student['id'] ?>]" 
                                                       id="late_<?= $student['id'] ?>" value="late"
                                                       <?= $currentStatus === 'late' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-warning btn-sm" for="late_<?= $student['id'] ?>">
                                                    <i class="fas fa-clock"></i> Late
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" 
                                                   name="remarks[<?= $student['id'] ?>]" 
                                                   value="<?= htmlspecialchars($currentRemarks) ?>"
                                                   placeholder="Optional">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success" onclick="markAll('present')">
                                        <i class="fas fa-check-double"></i> Mark All Present
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="markAll('absent')">
                                        <i class="fas fa-times-circle"></i> Mark All Absent
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="<?= $url('teacher/attendance') ?>" class="btn btn-secondary me-2">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Attendance
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function changeDate(newDate) {
    const url = new URL(window.location);
    url.searchParams.set('date', newDate);
    window.location.href = url.toString();
}

function markAll(status) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}

// Auto-save functionality (optional)
document.addEventListener('change', function(e) {
    if (e.target.type === 'radio' && e.target.name.startsWith('attendance[')) {
        // Visual feedback
        const row = e.target.closest('tr');
        row.style.backgroundColor = '#f8f9fa';
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 500);
    }
});
</script>

<?php include APP_PATH . '/views/layout/footer.php'; ?>