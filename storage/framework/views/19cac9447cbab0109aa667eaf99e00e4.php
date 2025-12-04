

<?php $__env->startSection('title', 'Loan Details – Inventory'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        
        <div class="row mb-3">
            <div class="col d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?php echo e(route('inventory.loans.index')); ?>" class="text-decoration-none small text-muted">
                        ← Back to Item Lending
                    </a>
                    <h1 class="h4 mb-1 mt-1">Loan Details</h1>
                    <p class="text-muted mb-0">
                        Detailed information about this item lending record.
                    </p>
                </div>

                <div class="text-end">
                    <?php
                        $badge = match($loan->status) {
                            'approved' => 'success',
                            'returned' => 'secondary',
                            'rejected' => 'danger',
                            default    => 'warning',
                        };
                    ?>
                    <span class="badge bg-<?php echo e($badge); ?> px-3 py-2">
                        Status: <?php echo e(ucfirst($loan->status)); ?>

                    </span>
                </div>
            </div>
        </div>

        
        <?php if(session('status')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('status')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e(session('error')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        
        <div class="card shadow-soft border-0 mb-4">
            <div class="card-body">
                <div class="row g-4">

                    
                    <div class="col-md-6 border-end-md">
                        <h2 class="h6 text-erp-deep mb-3">Item Information</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Item</dt>
                            <dd class="col-sm-8">
                                <div class="fw-semibold">
                                    <?php echo e($loan->item->name ?? '—'); ?>

                                </div>
                                <div class="small text-muted">
                                    Item No: <?php echo e($loan->item->item_no ?? '—'); ?>

                                </div>
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Quantity</dt>
                            <dd class="col-sm-8 mt-3">
                                <span class="fw-semibold"><?php echo e($loan->quantity); ?></span>
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Store Location</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->item->store_location ?? '—'); ?>

                            </dd>
                        </dl>

                        <hr class="my-4">

                        <h2 class="h6 text-erp-deep mb-3">Employee</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Name</dt>
                            <dd class="col-sm-8">
                                <?php echo e($loan->employee->full_name ?? '—'); ?>

                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Position</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->employee->position ?? '—'); ?>

                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Project / Site</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->employee->project_site ?? '—'); ?>

                            </dd>
                        </dl>
                    </div>

                    
                    <div class="col-md-6">
                        <h2 class="h6 text-erp-deep mb-3">Lending & Return</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Requested at</dt>
                            <dd class="col-sm-8">
                                <?php echo e(optional($loan->created_at)->format('Y-m-d H:i') ?? '—'); ?>

                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Lend date</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->lend_date ? optional($loan->lend_date)->format('Y-m-d') : '—'); ?>

                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Expected return</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->expected_return_date ? optional($loan->expected_return_date)->format('Y-m-d') : '—'); ?>

                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Actual return</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->actual_return_date ? optional($loan->actual_return_date)->format('Y-m-d') : '—'); ?>

                            </dd>
                        </dl>

                        <hr class="my-4">

                        <h2 class="h6 text-erp-deep mb-3">Approval trail</h2>

                        <dl class="row mb-0">
                            <dt class="col-sm-4 small text-muted">Approved by</dt>
                            <dd class="col-sm-8">
                                <?php echo e($loan->approvedBy->name ?? '—'); ?>

                                <?php if($loan->approved_at): ?>
                                    <div class="small text-muted">
                                        <?php echo e(optional($loan->approved_at)->format('Y-m-d H:i')); ?>

                                    </div>
                                <?php endif; ?>
                            </dd>

                            <dt class="col-sm-4 small text-muted mt-3">Rejected by</dt>
                            <dd class="col-sm-8 mt-3">
                                <?php echo e($loan->rejectedBy->name ?? '—'); ?>

                                <?php if($loan->rejected_at): ?>
                                    <div class="small text-muted">
                                        <?php echo e(optional($loan->rejected_at)->format('Y-m-d H:i')); ?>

                                    </div>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>

                </div>
            </div>
        </div>

        
        <div class="d-flex justify-content-between">
            <a href="<?php echo e(route('inventory.loans.index')); ?>" class="btn btn-outline-secondary">
                ← Back to Lending List
            </a>

            <div class="btn-group">
                <?php if($loan->status === 'pending'): ?>
                    <a href="<?php echo e(route('inventory.loans.edit', $loan)); ?>" class="btn btn-outline-primary">
                        Edit Request
                    </a>
                    <form action="<?php echo e(route('inventory.loans.destroy', $loan)); ?>"
                          method="POST"
                          onsubmit="return confirm('Delete this pending loan request?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-outline-danger">
                            Delete
                        </button>
                    </form>
                <?php elseif($loan->status === 'approved'): ?>
                    <form action="<?php echo e(route('inventory.loans.mark-returned', $loan)); ?>"
                          method="POST"
                          onsubmit="return confirm('Mark this loan as returned and update item quantity?');">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-success">
                            Mark as Returned
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/inventory/loans/show.blade.php ENDPATH**/ ?>