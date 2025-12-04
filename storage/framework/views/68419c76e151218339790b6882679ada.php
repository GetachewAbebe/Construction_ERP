

<?php $__env->startSection('title', 'New Inventory Loan – Natanem ERP'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-8">

            <div class="card shadow-soft border-0">
                <div class="card-body">

                    <h1 class="h5 mb-1">New Item Loan</h1>
                    <p class="text-muted small mb-3">
                        Select an inventory item and the employee who will borrow it.
                        The request will be sent to the Administrator for approval.
                    </p>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <?php echo e($errors->first()); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('inventory.loans.store')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label class="form-label">Item</label>
                            <select name="inventory_item_id" class="form-select" required>
                                <option value="">Choose item...</option>
                                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->id); ?>"
                                        <?php echo e(old('inventory_item_id') == $item->id ? 'selected' : ''); ?>>
                                        <?php echo e($item->item_no); ?> – <?php echo e($item->name ?? $item->description); ?>

                                        (Qty: <?php echo e($item->quantity); ?>)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Employee (borrower)</label>
                            <select name="employee_id" class="form-select" required>
                                <option value="">Choose employee...</option>
                                <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($employee->id); ?>"
                                        <?php echo e(old('employee_id') == $employee->id ? 'selected' : ''); ?>>
                                        <?php echo e($employee->full_name ?? ($employee->first_name . ' ' . $employee->last_name)); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" min="1"
                                   class="form-control"
                                   value="<?php echo e(old('quantity', 1)); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Due date (optional)</label>
                            <input type="date" name="due_date" class="form-control"
                                   value="<?php echo e(old('due_date')); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Remarks (optional)</label>
                            <textarea name="remarks" class="form-control" rows="3"
                                      placeholder="Purpose, site, special notes..."><?php echo e(old('remarks')); ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="<?php echo e(route('inventory.loans.index')); ?>" class="btn btn-outline-secondary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-success">
                                Submit Loan Request
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\natanem-erp\resources\views/inventory/loans/create.blade.php ENDPATH**/ ?>