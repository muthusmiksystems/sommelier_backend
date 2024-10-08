
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.addonCategoriesTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <?php if(empty($query)): ?>
                    <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                    <i class="icon-circle-right2 mr-2"></i>
                    <span class="font-weight-bold mr-2"><?php echo e($count); ?></span>
                <?php else: ?>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($count); ?></span>
                <br>
                <span class="font-weight-normal mr-2"><?php echo e(__('storeDashboard.sphResultFor')); ?> "<?php echo e($query); ?>"</span>
                <?php endif; ?>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <a class="btn btn-secondary btn-labeled btn-labeled-left" href="<?php echo e(route('restaurant.newAddonCategory')); ?>">
                    <b><i class="icon-plus2"></i></b>
                     <?php echo e(__('storeDashboard.btnAddNewCat')); ?> 
                </a>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <form action="<?php echo e(route('restaurant.post.searchAddonCategories')); ?>" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input"
                placeholder="<?php echo e(__('storeDashboard.acpSearchPlaceHolder')); ?>" name="query">
            <div class="form-control-feedback form-control-feedback-lg">
                <i class="icon-search4"></i>
            </div>
        </div>
        <?php echo csrf_field(); ?>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('storeDashboard.acpTableName')); ?></th>
                            <th><?php echo e(__('storeDashboard.acpTableType')); ?></th>
                            <th><?php echo e(__('storeDashboard.acpTableNOA')); ?></th>
                            <th style="width: 15%"><?php echo e(__('storeDashboard.acpTableCA')); ?></th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $addonCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addonCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($addonCategory->name); ?></td>
                            <td>
                                <?php if($addonCategory->type == "SINGLE"): ?>
                                 <span class="btn btn-xs btn-info p-1">
                                <?php echo e(__('storeDashboard.acpRowSingleSelection')); ?>

                                </span>
                                <?php endif; ?>
                                <?php if($addonCategory->type == "MULTI"): ?>
                                <span class="btn btn-xs btn-secondary p-1">
                                <?php echo e(__('storeDashboard.acpRowMultipleSelection')); ?>

                                </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($addonCategory->addons_count); ?></td>
                            <td><?php echo e($addonCategory->created_at->diffForHumans()); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-justified">
                                    <a href="<?php echo e(route('restaurant.editAddonCategory', $addonCategory->id)); ?>"
                                        class="btn btn-sm btn-primary"> <?php echo e(__('storeDashboard.edit')); ?></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($addonCategories->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/addonCategories.blade.php ENDPATH**/ ?>