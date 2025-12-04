

<?php $__env->startSection('title', 'Item Lending – Inventory'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        
        <div class="row mb-3">
            <div class="col d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h4 mb-1">Item Lending</h1>
                    <p class="text-muted mb-0">
                        Track which items are lent to which employees, and follow up on returns.
                    </p>
                </div>
                <div>
                    <a href="<?php echo e(route('inventory.loans.create')); ?>" class="btn btn-success">
                        Record / Request Loan
                    </a>
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

        
        <div class="card shadow-soft border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="h6 mb-0 text-erp-deep">Lending Records</h2>
                    <span class="badge bg-light text-muted">
                        Total: <?php echo e($loans->total()); ?>

                    </span>
                </div>

                <?php if($loans->isEmpty()): ?>
                    <p class="text-muted mb-0">
                        No lending records yet. Use <strong>Record / Request Loan</strong> to start tracking issued items.
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Employee</th>
                                    <th>Qty</th>
                                    <th>Status</th>
                                    <th>Requested</th>
                                    <th>Expected Return</th>
                                    <th>Approved By</th>
                                    <th>Returned</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $badge = match($loan->status) {
                                            'approved' => 'success',
                                            'returned' => 'secondary',
                                            'rejected' => 'danger',
                                            default    => 'warning',
                                        };
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">
                                                <?php echo e($loan->item->name ?? '—'); ?>

                                            </div>
                                            <div class="small text-muted">
                                                Item No: <?php echo e($loan->item->item_no ?? '—'); ?>

                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">
                                                <?php echo e($loan->employee->full_name ?? '—'); ?>

                                            </div>
                                            <div class="small text-muted">
                                                <?php echo e($loan->employee->position ?? ''); ?>

                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold"><?php echo e($loan->quantity); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($badge); ?>">
                                                <?php echo e(ucfirst($loan->status)); ?>

                                            </span>
                                        </td>
                                        <td class="small text-muted">
                                            
                                            <?php echo e(optional($loan->created_at)->format('Y-m-d H:i') ?? '—'); ?>

                                        </td>
                                        <td class="small text-muted">
                                            
                                            <?php echo e($loan->expected_return_date?->format('Y-m-d') ?? '—'); ?>

                                        </td>
                                        <td class="small">
                                            <?php echo e($loan->approvedBy->name ?? '—'); ?>

                                        </td>
                                        <td class="small text-muted">
                                            
                                            <?php echo e($loan->actual_return_date?->format('Y-m-d') ?? '—'); ?>

                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo e(route('inventory.loans.show', $loan)); ?>"
                                                   class="btn btn-outline-secondary">
                                                    View
                                                </a>

                                                <?php if($loan->status === 'pending'): ?>
                                                    <a href="<?php echo e(route('inventory.loans.edit', $loan)); ?>"
                                                       class="btn btn-outline-primary">
                                                        Edit
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
                                                          onsubmit="return confirm('Mark this loan as returned?');">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-outline-success">
                                                            Mark Returned
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($loans->links()); ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\natanem-erp\resources\views/inventory/loans/index.blade.php ENDPATH**/ ?>