

<?php $__env->startSection('title', 'Admin – Users'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">

    
    <div class="row mb-3">
        <div class="col d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h4 mb-1">System Users</h1>
                <p class="text-muted mb-0">
                    Manage user accounts and access roles for Natanem Engineering.
                </p>
            </div>
            <div>
                <a href="<?php echo e(route('admin.users.create')); ?>" class="btn btn-success">
                    + Add User
                </a>
            </div>
        </div>
    </div>

    
    <?php if(session('status')): ?>
        <div class="alert alert-success py-2">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    
    <div class="row mb-3">
        <div class="col-md-6">
            <form action="<?php echo e(route('admin.users.index')); ?>" method="GET" class="d-flex gap-2">
                <input
                    type="text"
                    name="q"
                    value="<?php echo e($q ?? ''); ?>"
                    class="form-control form-control-sm"
                    placeholder="Search by name or email..."
                >
                <button class="btn btn-outline-secondary btn-sm" type="submit">
                    Search
                </button>
            </form>
        </div>
    </div>

    
    <div class="card shadow-soft border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4">Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th class="text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-4"><?php echo e($user->name); ?></td>
                            <td><?php echo e($user->email); ?></td>
                            <td>
                                
                                <span class="badge bg-outline-success text-dark border">
                                    <?php echo e($user->role ?? '—'); ?>

                                </span>
                            </td>
                            <td class="text-end px-4">
                                <a href="<?php echo e(route('admin.users.edit', $user)); ?>"
                                   class="btn btn-sm btn-success me-1">
                                    Edit
                                </a>

                                <?php if($user->id !== auth()->id()): ?>
                                    <form action="<?php echo e(route('admin.users.destroy', $user)); ?>"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Delete this user?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit"
                                                class="btn btn-sm btn-outline-danger">
                                            Delete
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted small">This is you</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No users found.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($users->hasPages()): ?>
            <div class="card-footer border-0">
                <?php echo e($users->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/admin/users/index.blade.php ENDPATH**/ ?>