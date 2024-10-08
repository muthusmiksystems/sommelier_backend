<?php if(count($topStores) > 0): ?>
<?php $__currentLoopData = $topStores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topStore): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="top-store px-0 py-2">
    <?php if($loop->first): ?>
    <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/images/top-badge.svg"
        style="position: absolute; height: 3.2rem; right: 24px; top: 0px;">
    <?php endif; ?>

    <div class="d-flex justify-content-start">
        <div class="first-letter-icon custom-bg-<?php echo e($loop->iteration + 1); ?>">
            <?php echo e($loop->iteration); ?>

        </div>
        <div class="ml-2">
            <span><strong><?php echo e($topStore->data->name); ?></strong> <?php if($loop->first): ?> 🎉 <?php endif; ?></span>
            <br>
            <span class="small"><b><?php echo e(config('setting.currencyFormat')); ?><?php echo e($topStore->revenue); ?></b>
                revenue with <?php echo e($topStore->sales_count); ?> orders
            </span>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
<div class="text-center">
    <i class="icon-exclamation" style="font-size: 2.5rem; margin-top: 12px; opacity: 0.1;"></i>
    <p class="text-muted mb-0">No data to show</p>
</div>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/partials/dashboardTopStores.blade.php ENDPATH**/ ?>