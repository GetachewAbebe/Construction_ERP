
<?php $__env->startSection('title','Attendance Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <div class="row mb-3">
        <div class="col d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="h4 mb-1 text-erp-deep">Attendance Management</h1>
                <p class="text-muted small mb-0">
                    Track daily check-in and check-out, monitor lateness and overall presence.
                </p>
            </div>

            <div class="d-flex flex-wrap gap-2 align-items-center justify-content-md-end">
                <?php if(isset($todayStats)): ?>
                    <div class="d-flex flex-wrap gap-2 me-md-2">
                        <div class="card border-0 shadow-sm py-1 px-2">
                            <div class="small text-muted mb-0">Present</div>
                            <div class="fw-bold"><?php echo e($todayStats['present'] ?? 0); ?></div>
                        </div>
                        <div class="card border-0 shadow-sm py-1 px-2">
                            <div class="small text-muted mb-0">Late</div>
                            <div class="fw-bold text-warning"><?php echo e($todayStats['late'] ?? 0); ?></div>
                        </div>
                        <div class="card border-0 shadow-sm py-1 px-2">
                            <div class="small text-muted mb-0">Absent</div>
                            <div class="fw-bold text-danger"><?php echo e($todayStats['absent'] ?? 0); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-attendance')): ?>
                    <a href="<?php echo e(route('hr.attendance.monthly-summary')); ?>" class="btn btn-outline-primary btn-sm">
                        Monthly Summary
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="row mb-3">
        <div class="col">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 small">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="row mb-4 g-3">
        <div class="col-lg-6">
            <div class="card shadow-soft border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                        <span class="badge bg-light text-muted small">
                            <?php echo e(now()->format('Y-m-d')); ?>

                        </span>
                    </div>
                    <p class="small text-muted mb-3">
                        Quickly check in employees for today’s attendance.
                    </p>

                    <form action="<?php echo e(route('hr.attendance.check-in')); ?>" method="POST" class="row gy-2 gx-2 align-items-end">
                        <?php echo csrf_field(); ?>

                        
                        <div class="col-12 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-attendance')): ?> col-md-8 <?php else: ?> col-md-12 <?php endif; ?>">
                            <label for="employee_id" class="form-label small fw-semibold">Employee</label>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-attendance')): ?>
                                <select name="employee_id" id="employee_id"
                                        class="form-select form-select-sm select2"
                                        data-placeholder="Select employee">
                                    <option value="">-- Select Employee --</option>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($employee->id); ?>"
                                            <?php echo e(old('employee_id') == $employee->id ? 'selected' : ''); ?>>
                                            <?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?>

                                            (<?php echo e($employee->department); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            <?php else: ?>
                                <input type="hidden" name="employee_id" value="<?php echo e(auth()->user()->employee_id); ?>">
                                <div class="form-control form-control-sm bg-light">
                                    <?php echo e(auth()->user()->employee->first_name); ?>

                                    <?php echo e(auth()->user()->employee->last_name); ?>

                                    (<?php echo e(auth()->user()->employee->department); ?>)
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-6 col-md-4 text-md-end">
                            <button type="submit"
                                    class="btn btn-primary btn-sm w-100"
                                    onclick="this.disabled = true; this.form.submit();">
                                Check In Now
                            </button>
                        </div>
                    </form>

                    
                    <?php if(isset($myOpenAttendance)): ?>
                        <hr class="my-3">
                        <form action="<?php echo e(route('hr.attendance.check-out', $myOpenAttendance->id)); ?>" method="POST"
                              onsubmit="return confirm('Confirm check out now?');">
                            <?php echo csrf_field(); ?>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small text-muted">
                                    You are checked in since
                                    <strong><?php echo e($myOpenAttendance->clock_in->format('H:i')); ?></strong>
                                </div>
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Check Out
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6">
            <div class="card shadow-soft border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0">Filters</h5>
                        <?php if(request()->anyFilled(['date_from', 'date_to', 'employee_filter', 'status'])): ?>
                            <a href="<?php echo e(route('hr.attendance.index')); ?>" class="small text-decoration-none">
                                Clear
                            </a>
                        <?php endif; ?>
                    </div>

                    <button class="btn btn-outline-secondary btn-sm d-md-none mb-2"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#attendanceFilters">
                        Filters
                    </button>

                    <div class="collapse d-md-block" id="attendanceFilters">
                        <form method="GET" action="<?php echo e(route('hr.attendance.index')); ?>" class="row g-2">
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">From</label>
                                <input type="date" name="date_from" value="<?php echo e(request('date_from')); ?>"
                                       class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">To</label>
                                <input type="date" name="date_to" value="<?php echo e(request('date_to')); ?>"
                                       class="form-control form-control-sm">
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">Employee</label>
                                <select name="employee_filter"
                                        class="form-select form-select-sm select2"
                                        data-placeholder="All employees">
                                    <option value="">All</option>
                                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($employee->id); ?>"
                                            <?php echo e(request('employee_filter') == $employee->id ? 'selected' : ''); ?>>
                                            <?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <label class="form-label small">Status</label>
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="present" <?php echo e(request('status') == 'present' ? 'selected' : ''); ?>>Present</option>
                                    <option value="late" <?php echo e(request('status') == 'late' ? 'selected' : ''); ?>>Late</option>
                                    <option value="absent" <?php echo e(request('status') == 'absent' ? 'selected' : ''); ?>>Absent</option>
                                </select>
                            </div>

                            <div class="col-12 d-flex justify-content-end mt-2">
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    Apply Filters
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    
    <div class="row">
        <div class="col">
            <div class="card shadow-soft border-0">
                <div class="card-body">

                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3 gap-2">
                        <h5 class="card-title mb-0">Attendance Records</h5>
                        <div class="small text-muted">
                            Showing <?php echo e($attendances->firstItem() ?? 0); ?>–<?php echo e($attendances->lastItem() ?? 0); ?>

                            of <?php echo e($attendances->total()); ?> records
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Employee</th>
                                    <th scope="col">Check In</th>
                                    <th scope="col">Check Out</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $attendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attendance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php
                                        $rowClass = match ($attendance->status) {
                                            'present' => 'table-success-subtle',
                                            'late'    => 'table-warning-subtle',
                                            'absent'  => 'table-secondary-subtle',
                                            default   => ''
                                        };
                                    ?>
                                    <tr class="<?php echo e($rowClass); ?>">
                                        <td><?php echo e($attendance->date->format('Y-m-d')); ?></td>
                                        <td>
                                            <div class="fw-semibold">
                                                <?php echo e($attendance->employee->first_name); ?> <?php echo e($attendance->employee->last_name); ?>

                                            </div>
                                            <div class="small text-muted">
                                                <?php echo e($attendance->employee->department); ?>

                                            </div>
                                        </td>
                                        <td><?php echo e($attendance->clock_in ? $attendance->clock_in->format('H:i') : '-'); ?></td>
                                        <td><?php echo e($attendance->clock_out ? $attendance->clock_out->format('H:i') : '-'); ?></td>
                                        <td>
                                            <span class="badge
                                                <?php if($attendance->status === 'present'): ?> bg-success
                                                <?php elseif($attendance->status === 'late'): ?> bg-warning text-dark
                                                <?php elseif($attendance->status === 'absent'): ?> bg-secondary
                                                <?php else: ?> bg-light text-muted
                                                <?php endif; ?>">
                                                <?php echo e(ucfirst($attendance->status)); ?>

                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if(!$attendance->clock_out): ?>
                                                <form action="<?php echo e(route('hr.attendance.check-out', $attendance->id)); ?>"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Check out this employee now?');">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Check Out
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted small">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            No attendance records found for the selected filters.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    
                    <div class="mt-3 d-flex justify-content-end">
                        <?php echo e($attendances->withQueryString()->links()); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .table-success-subtle { background-color: rgba(25,135,84,.04); }
        .table-warning-subtle { background-color: rgba(255,193,7,.04); }
        .table-secondary-subtle { background-color: rgba(108,117,125,.04); }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.jQuery && $.fn.select2) {
                $('.select2').select2({
                    width: '100%',
                    allowClear: true,
                    placeholder: function(){
                        return $(this).data('placeholder') || '';
                    }
                });
            }
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/hr/attendance/index.blade.php ENDPATH**/ ?>