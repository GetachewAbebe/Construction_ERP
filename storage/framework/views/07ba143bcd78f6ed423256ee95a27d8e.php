
<?php $__env->startSection('title', 'Inventory Items'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        
        <?php if($errors->any()): ?>
            <div class="alert alert-danger mb-3">
                <strong>There were problems with your submission:</strong>
                <ul class="mb-0 mt-2">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <div class="row mb-3">
            <div class="col d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-1 text-erp-deep">Inventory Items</h1>
                    <p class="text-muted small mb-0">
                        Browse and manage materials, equipment and other inventory items.
                    </p>
                </div>
                <a href="<?php echo e(route('inventory.items.create')); ?>" class="btn btn-sm btn-success">
                    Add Item
                </a>
            </div>
        </div>

        
        <div class="row">
            <div class="col">
                <div class="card shadow-soft border-0">
                    <div class="card-body">

                        
                        <form method="GET" class="row g-2 mb-3">
                            <div class="col-md-4">
                                <input
                                    type="text"
                                    name="q"
                                    value="<?php echo e(request('q')); ?>"
                                    class="form-control form-control-sm"
                                    placeholder="Search by name or code..."
                                >
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select form-select-sm">
                                    <option value="">Category (Any)</option>
                                    
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="">Status (Any)</option>
                                    <option value="in_stock" <?php if(request('status') === 'in_stock'): echo 'selected'; endif; ?>>In stock</option>
                                    <option value="low_stock" <?php if(request('status') === 'low_stock'): echo 'selected'; endif; ?>>Low stock</option>
                                    <option value="out_of_stock" <?php if(request('status') === 'out_of_stock'): echo 'selected'; endif; ?>>Out of stock</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-md-end">
                                <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                    Filter
                                </button>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Item</th>
                                        <th scope="col">Category</th>
                                        <th scope="col" class="text-end">Quantity</th>
                                        <th scope="col">Location</th>
                                        <th scope="col">Status</th>
                                        <th scope="col" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <?php echo e($item->name); ?>

                                                <?php if(!empty($item->code)): ?>
                                                    <div class="small text-muted">
                                                        <?php echo e($item->code); ?>

                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo e($item->category ?? '—'); ?></td>
                                            <td class="text-end"><?php echo e($item->quantity ?? 0); ?></td>
                                            <td><?php echo e($item->location ?? '—'); ?></td>
                                            <td>
                                                <?php
                                                    $status = $item->status ?? 'in_stock';
                                                ?>
                                                <?php if($status === 'in_stock'): ?>
                                                    <span class="badge bg-success-subtle text-erp-deep">In stock</span>
                                                <?php elseif($status === 'low_stock'): ?>
                                                    <span class="badge bg-warning-subtle text-erp-deep">Low stock</span>
                                                <?php elseif($status === 'out_of_stock'): ?>
                                                    <span class="badge bg-danger-subtle">Out of stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary-subtle"><?php echo e(ucfirst($status)); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="<?php echo e(route('inventory.items.edit', $item)); ?>"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        Edit
                                                    </a>
                                                    <form method="POST"
                                                          action="<?php echo e(route('inventory.items.destroy', $item)); ?>"
                                                          onsubmit="return confirm('Delete this item?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                No items found.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="mt-3">
                            <?php echo e($items->links()); ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\natanem-erp\resources\views/inventory/items/index.blade.php ENDPATH**/ ?>