<div class="d-flex justify-content-between align-items-center">
    <div>
        <h4 style="color: #000;font-weight: 500;" class="move-handle mb-0"> What's new in Foodomaa </h4>
    </div>
    <div>
        <?php if($nonReadCount > 0): ?> <span class="badge badge-success mr-2" id="nonReadCounter"><?php echo e($nonReadCount); ?></span>
        <?php endif; ?>
        <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/images/announcement.png"
            style=" width: 36px;transform: scaleX(-1);transform: rotate();">
    </div>
</div>
<div class="card-body px-0 pb-0">
    <?php if(count($foodomaaNews) > 0): ?>
    <?php $__currentLoopData = $foodomaaNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="foodomaaSingleNewsBlock <?php if($news->is_read): ?> newsRead <?php else: ?> newsNotRead <?php endif; ?> p-2 mb-2">
        <a href="<?php echo e($news->link); ?>" data-id="<?php echo e($news->id); ?>" class="foodomaaSingleNews" target="_blank">
            <div class="d-flex justify-content-between align-items-center">
                <div class="mr-2">
                    <p class="font-weight-bold mb-0 newsTitle"><?php echo e($news->title); ?></p>
                    <p class="mb-0 newsContent"><?php echo e($news->content); ?></p>
                </div>
                <div class="flex-shrink-0">
                    <img src="<?php echo e($news->image); ?>" class="img-fluid">
                </div>
            </div>
        </a>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
    <div class="text-center">
        <i class="icon-exclamation" style="font-size: 2.5rem; margin-top: 12px; opacity: 0.1;"></i>
        <p class="text-muted mb-0">No data to show</p>
    </div>
    <?php endif; ?>
</div><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/partials/dashboardFoodomaaNews.blade.php ENDPATH**/ ?>