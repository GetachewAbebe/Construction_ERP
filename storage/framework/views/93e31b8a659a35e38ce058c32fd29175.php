

<?php $__env->startSection('title', 'File Leave'); ?>

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
                                File Leave
                            </h1>
                            <p class="mb-0 text-muted">
                                Create and submit a new leave request for an employee.
                            </p>
                        </div>
                        <div class="mt-3 mt-md-0 text-md-end">
                            <a href="<?php echo e(route('hr.leaves.index')); ?>"
                               class="btn btn-sm btn-outline-success">
                                Back to Leave Requests
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-soft border-0">
                    <div class="card-body">
                        <h5 class="card-title mb-3 text-erp-deep">Leave Request Details</h5>

                        
                        <form method="POST" action="<?php echo e(route('hr.leaves.store') ?? '#'); ?>">
                            <?php echo csrf_field(); ?>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Employee</label>
                                <select class="form-select form-select-sm">
                                    <option value="">Select employee...</option>
                                    
                                    <option>Example Employee</option>
                                    
                                </select>
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small text-muted">Leave Type</label>
                                    <select class="form-select form-select-sm">
                                        <option value="">Select type...</option>
                                        <option>Annual</option>
                                        <option>Sick</option>
                                        <option>Unpaid</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">From</label>
                                    <input type="date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small text-muted">To</label>
                                    <input type="date" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small text-muted">Reason</label>
                                <textarea class="form-control form-control-sm" rows="3"
                                          placeholder="Brief reason for leave"></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="reset" class="btn btn-sm btn-outline-secondary">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-sm btn-success">
                                    Submit Request
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 mt-3 mt-lg-0">
                <div class="card shadow-soft border-0 h-100">
                    <div class="card-body">
                        <h6 class="card-title mb-2 text-erp-deep">Guidelines</h6>
                        <ul class="text-muted small mb-0">
                            <li>Confirm dates with your project lead before submitting.</li>
                            <li>Ensure the leave type matches the company policy.</li>
                            <li>Provide clear reasons for auditing and approvals.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/hr/leaves/create.blade.php ENDPATH**/ ?>