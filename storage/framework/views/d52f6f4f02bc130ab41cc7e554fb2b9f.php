<?php $__env->startSection('title', 'Human Resource Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        
        <div class="row mb-4">
            <div class="col">
                <div class="card shadow-soft border-0 bg-erp-soft">
                    <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                        <div>
                            <div class="small text-uppercase text-muted mb-1">
                                Human Resource Module
                            </div>
                            <h1 class="h4 mb-2 text-erp-deep">
                                Human Resource Dashboard
                            </h1>
                            <p class="mb-0 text-muted">
                                Overview of your employees and leave activity. Start here to navigate all HR-related tasks.
                            </p>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-end">
                            <span class="badge rounded-pill bg-success-subtle text-erp-deep border border-success-subtle px-3 py-2">
                                HR Home
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row g-3 mb-4">

            
            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <div class="small text-uppercase text-muted mb-1">
                            Total Employees
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-3 fw-semibold text-erp-deep">
                                <?php echo e($employeeCount ?? '—'); ?>

                            </span>
                            <span class="text-muted small">
                                in the system
                            </span>
                        </div>
                        <p class="text-muted small mb-0">
                            Number of employees currently registered in HR.
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <div class="small text-uppercase text-muted mb-1">
                            Active Leaves Today
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-3 fw-semibold text-erp-deep">
                                <?php echo e($activeLeavesToday ?? '—'); ?>

                            </span>
                            <span class="text-muted small">
                                employees on leave
                            </span>
                        </div>
                        <p class="text-muted small mb-0">
                            Employees currently away on approved leave.
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="col-md-4">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <div class="small text-uppercase text-muted mb-1">
                            Pending Approvals
                        </div>
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="fs-3 fw-semibold text-erp-deep">
                                <?php echo e($pendingLeaveApprovals ?? '—'); ?>

                            </span>
                            <span class="text-muted small">
                                requests awaiting action
                            </span>
                        </div>
                        <p class="text-muted small mb-0">
                            Leave requests that still need a decision from HR.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        
        <div class="row g-3">
            
            <div class="col-lg-7">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-erp-deep">What you can do here</h5>
                        <p class="card-text text-muted mb-3">
                            The Human Resource dashboard gives you a quick sense of what’s happening with
                            your people: how many are employed, who is on leave, and what actions are waiting.
                        </p>
                        <ul class="text-muted small mb-0">
                            <li>Keep an eye on overall headcount.</li>
                            <li>Watch today’s leave activity and coverage.</li>
                            <li>Identify leave requests that need your approval.</li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-5">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-2 text-erp-deep">Quick Actions</h5>
                        <p class="text-muted small mb-3">
                            Jump straight into the main HR sections.
                        </p>

                        <div class="d-grid gap-2">
                            <a href="<?php echo e(route('hr.employees.index')); ?>"
                               class="btn btn-sm btn-success d-flex justify-content-between align-items-center">
                                <span>Employees</span>
                                <span class="small text-white-50">View &amp; manage records</span>
                            </a>

                            <a href="<?php echo e(route('hr.leaves.index')); ?>"
                               class="btn btn-sm btn-outline-success d-flex justify-content-between align-items-center">
                                <span>Leave Requests</span>
                                <span class="small text-muted">Track submitted requests</span>
                            </a>

                            <a href="<?php echo e(route('hr.leaves.create')); ?>"
                               class="btn btn-sm btn-outline-success d-flex justify-content-between align-items-center">
                                <span>File Leave</span>
                                <span class="small text-muted">Create a new request</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\natanem-erp\resources\views/dashboards/hr.blade.php ENDPATH**/ ?>