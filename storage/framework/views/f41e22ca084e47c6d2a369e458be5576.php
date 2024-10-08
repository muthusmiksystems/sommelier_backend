
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.apePagetitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.apeTitleEditing')); ?></span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($addon->name); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('restaurant.updateAddon')); ?>" method="POST" enctype="multipart/form-data">
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        <i class="icon-list2 mr-2"></i> <?php echo e(__('storeDashboard.apeTitleDetails')); ?>

                    </legend>
                    <input type="hidden" name="id" value="<?php echo e($addon->id); ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.apeLabelName')); ?>:</label>
                        <div class="col-lg-9">
                            <input value="<?php echo e($addon->name); ?>" type="text" class="form-control form-control-lg"
                                name="name" placeholder="<?php echo e(__('storeDashboard.apePlaceHolderName')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.apeLabelPrice')); ?>:</label>
                        <div class="col-lg-9">
                            <input value="<?php echo e($addon->price); ?>" type="text" class="form-control form-control-lg price"
                                name="price"
                                placeholder="<?php echo e(__('storeDashboard.apePlaceHolderPrice')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.apInputAddonCategory')); ?>:</label>
                        <div class="col-lg-9">
                            <select class="form-control select-search" name="addon_category_id" required>
                                <?php $__currentLoopData = $addonCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addonCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($addonCategory->id); ?>" class="text-capitalize" <?php if($addon->
                                    addon_category->id == $addonCategory->id): ?> selected="selected" <?php endif; ?>
                                    ><?php echo e($addonCategory->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <?php echo e(__('storeDashboard.update')); ?>

                            <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.price').numeric({allowThouSep:false, maxDecimalPlaces: 2 });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/editAddon.blade.php ENDPATH**/ ?>