

<?php $__env->startSection('title', 'Item Lending Requests – Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    
    <div class="row mb-3">
        <div class="col">
            <h1 class="h4 mb-1">Item Lending Requests</h1>
            <p class="text-muted mb-0">
                Approve or reject inventory items requested by employees.
            </p>
        </div>
    </div>

    
    <?php if(session('status')): ?>
        <div class="alert alert-success py-2">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger py-2">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    
    <div class="card shadow-soft border-0">
        <div class="card-body">
            <?php if($loans->isEmpty()): ?>
                <p class="text-muted mb-0">
                    No item lending requests yet. When inventory managers submit lending records,
                    they will appear here for review.
                </p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Requested At</th>
                                <th>Item</th>
                                <th>Employee</th>
                                <th class="text-center">Quantity</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $status = $loan->status ?? 'pending';
                                $badgeClass = match($status) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default     => 'bg-warning text-dark',
                                };
                            ?>
                            <tr>
                                <td>
                                    <div class="small text-muted">
                                        <?php echo e($loan->created_at?->format('Y-m-d H:i') ?? '—'); ?>

                                    </div>
                                </td>
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
                                        ID: <?php echo e($loan->employee->id ?? '—'); ?>

                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php echo e($loan->quantity); ?>

                                </td>
                                <td>
                                    <span class="badge <?php echo e($badgeClass); ?>">
                                        <?php echo e(ucfirst($status)); ?>

                                    </span>
                                </td>
                                <td class="text-end">
                                    <?php if($status === 'pending'): ?>
                                        <form method="POST"
                                              action="<?php echo e(route('admin.requests.items.approve', $loan)); ?>"
                                              class="d-inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                    class="btn btn-sm btn-success">
                                                Approve
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="<?php echo e(route('admin.requests.items.reject', $loan)); ?>"
                                              class="d-inline ms-1">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">
                                                Reject
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="small text-muted">
                                            No actions
                                        </span>
                                    <?php endif; ?>
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/admin/requests/items.blade.php ENDPATH**/ ?>