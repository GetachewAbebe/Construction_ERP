
<?php $__env->startSection('title','Employees'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">

        
        <div class="row mb-3">
            <div class="col d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h4 mb-1 text-erp-deep">Employees</h1>
                    <p class="text-muted small mb-0">
                        View and manage employee records.
                    </p>
                </div>
                <a href="<?php echo e(route('hr.employees.create')); ?>"
                   class="btn btn-sm btn-success">
                    New Employee
                </a>
            </div>
        </div>

        
        <div class="row">
            <div class="col">
                <div class="card shadow-soft border-0">
                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;"></th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Department</th>
                                        <th scope="col">Position</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Phone</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>
                                                <?php if($e->profile_picture): ?>
                                                    <img src="<?php echo e(asset('storage/' . $e->profile_picture)); ?>" 
                                                         class="rounded-circle border" 
                                                         width="32" height="32" 
                                                         style="object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center text-muted small" 
                                                         style="width: 32px; height: 32px;">
                                                        <?php echo e(substr($e->first_name, 0, 1)); ?>

                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?php echo e($e->first_name); ?> <?php echo e($e->last_name); ?></div>
                                                <div class="small text-muted"><?php echo e($e->email); ?></div>
                                            </td>
                                            <td><?php echo e($e->department); ?></td>
                                            <td><?php echo e($e->position); ?></td>
                                            <td>
                                                <?php
                                                    $badgeClass = match($e->status) {
                                                        'Active' => 'bg-success',
                                                        'On Leave' => 'bg-warning text-dark',
                                                        'Terminated' => 'bg-danger',
                                                        'Resigned' => 'bg-secondary',
                                                        default => 'bg-secondary'
                                                    };
                                                ?>
                                                <span class="badge <?php echo e($badgeClass); ?>"><?php echo e($e->status ?? 'N/A'); ?></span>
                                            </td>
                                            <td><?php echo e($e->phone ?? '-'); ?></td>
                                            <td class="text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="<?php echo e(route('hr.employees.edit', $e)); ?>"
                                                       class="btn btn-sm btn-outline-secondary">
                                                        Edit
                                                    </a>
                                                    <form method="POST"
                                                          action="<?php echo e(route('hr.employees.destroy', $e)); ?>"
                                                          onsubmit="return confirm('Delete this employee?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No employees yet.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="mt-3">
                            <?php echo e($employees->links()); ?>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/hr/employees/index.blade.php ENDPATH**/ ?>