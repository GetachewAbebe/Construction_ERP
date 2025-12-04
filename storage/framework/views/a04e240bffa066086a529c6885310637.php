<?php $__env->startSection('title', 'Finance Dashboard – Natanem Engineering'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-3 py-md-4">
    
    <div class="row mb-3">
        <div class="col">
            <h1 class="h4 fw-bold mb-1">Finance Dashboard</h1>
            <p class="text-muted mb-0">
                Monitor invoices, payments and financial reports for Natanem Engineering.
            </p>
        </div>
    </div>

    
    <ul class="nav nav-pills mb-4">
        <li class="nav-item">
            <a href="<?php echo e(route('finance.dashboard')); ?>"
               class="nav-link <?php echo e(request()->routeIs('finance.dashboard') ? 'active' : ''); ?>">
                Overview
            </a>
        </li>
        
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Invoices</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Payments</a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link disabled" aria-disabled="true">Reports</a>
        </li>
    </ul>

    
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-1">Billing</h6>
                    <p class="small text-muted mb-0">
                        Centralize project invoices and client billing details.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-1">Payments</h6>
                    <p class="small text-muted mb-0">
                        Track incoming and outgoing payments across sites.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="text-muted text-uppercase small mb-1">Reporting</h6>
                    <p class="small text-muted mb-0">
                        Build financial summaries for management and project owners.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Projects\Natanem\resources\views/dashboards/finance.blade.php ENDPATH**/ ?>