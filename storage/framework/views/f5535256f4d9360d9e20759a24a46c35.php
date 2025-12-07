

<?php $__env->startSection('title', 'Edit Inventory Item'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Edit Item</h4>
    <a href="<?php echo e(route('inventory.items.index')); ?>" class="btn btn-outline-secondary btn-sm">Back to list</a>
  </div>

  <?php if($errors->any()): ?>
    <div class="alert alert-danger">
      <strong>Fix the following:</strong>
      <ul class="mb-0">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($e); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form method="POST" action="<?php echo e(route('inventory.items.update', $item)); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Item No <span class="text-danger">*</span></label>
            <input type="text" name="item_no" class="form-control" value="<?php echo e(old('item_no', $item->item_no)); ?>" required>
          </div>

          <div class="col-md-8">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $item->name)); ?>" required>
          </div>

          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" rows="3" class="form-control"><?php echo e(old('description', $item->description)); ?></textarea>
          </div>

          <div class="col-md-4">
            <label class="form-label">Unit of Measurement <span class="text-danger">*</span></label>
            <input type="text" name="unit_of_measurement" class="form-control" value="<?php echo e(old('unit_of_measurement', $item->unit_of_measurement)); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" name="quantity" class="form-control" value="<?php echo e(old('quantity', $item->quantity)); ?>" min="0" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">Store Location <span class="text-danger">*</span></label>
            <input type="text" name="store_location" class="form-control" value="<?php echo e(old('store_location', $item->store_location)); ?>" required>
          </div>

          <div class="col-md-4">
            <label class="form-label">In Date <span class="text-danger">*</span></label>
            <input type="date" name="in_date" class="form-control" value="<?php echo e(old('in_date', optional($item->in_date)->format('Y-m-d'))); ?>" required>
          </div>
        </div>

        <div class="d-flex gap-2 mt-4">
          <button type="submit" class="btn btn-primary">Update Item</button>
          <a href="<?php echo e(route('inventory.items.index')); ?>" class="btn btn-light">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/inventory/items/edit.blade.php ENDPATH**/ ?>