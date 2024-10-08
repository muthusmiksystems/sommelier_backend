
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.addonPageTitle')); ?>

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
                <span class="font-weight-normal mr-2"><?php echo e(__('storeDashboard.sphResultsFor')); ?> "<?php echo e($query); ?>"</span>
                <?php endif; ?>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewAddon"
                    data-toggle="modal" data-target="#addNewAddonModal">
                    <b><i class="icon-plus2"></i></b>
                    <?php echo e(__('storeDashboard.btnAddNewAddon')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <form action="<?php echo e(route('restaurant.post.searchAddons')); ?>" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input"
                placeholder="<?php echo e(__('storeDashboard.apSearchPlaceHolder')); ?>" name="query">
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
                            <th><?php echo e(__('storeDashboard.apTableName')); ?></th>
                            <th><?php echo e(__('storeDashboard.apTablePrice')); ?></th>
                            <th><?php echo e(__('storeDashboard.apTableAddonCategory')); ?></th>
                            <th style="width: 15%"><?php echo e(__('storeDashboard.apTableCreatedAt')); ?></th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $addons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($addon->name); ?></td>
                            <td><?php echo e($addon->price); ?></td>
                            <td><?php echo e($addon->addon_category->name); ?></td>
                            <td><?php echo e($addon->created_at->diffForHumans()); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-justified align-items-center">
                                    <a href="<?php echo e(route('restaurant.editAddon', $addon->id)); ?>"
                                        class="btn btn-sm btn-primary"> <?php echo e(__('storeDashboard.edit')); ?></a>
                                    <div class="checkbox checkbox-switchery ml-1" style="padding-top: 0.8rem;">
                                        <label>
                                            <input value="true" type="checkbox" class="action-switch"
                                                <?php if($addon->is_active): ?> checked="checked" <?php endif; ?>
                                            data-id="<?php echo e($addon->id); ?>">
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($addons->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div id="addNewAddonModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.btnAddNewAddon')); ?></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.saveNewAddon')); ?>" method="POST" enctype="multipart/form-data"
                    enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.apInputName')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="<?php echo e(__('storeDashboard.apInputNamePH')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.apInputPrice')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg price" name="price"
                                placeholder="<?php echo e(__('storeDashboard.apInputPricePH')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.apInputAddonCategory')); ?>:</label>
                        <div class="col-lg-9">
                            <select class="form-control select-search select" name="addon_category_id" required>
                                <?php $__currentLoopData = $addonCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addonCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($addonCategory->id); ?>" class="text-capitalize">
                                    <?php echo e($addonCategory->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <?php echo e(__('storeDashboard.save')); ?>

                            <i class="icon-database-insert ml-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('.select').select2();
         $('.price').numeric({allowThouSep:false, maxDecimalPlaces: 2 });
         //Switch Action Function
         if (Array.prototype.forEach) {
                var elems = Array.prototype.slice.call(document.querySelectorAll('.action-switch'));
                elems.forEach(function(html) {
                    var switchery = new Switchery(html, { color: '#8360c3' });
                });
            }
            else {
                var elems = document.querySelectorAll('.action-switch');
                for (var i = 0; i < elems.length; i++) {
                    var switchery = new Switchery(elems[i], { color: '#8360c3' });
                }
            }

          $('.action-switch').click(function(event) {
             let id = $(this).attr("data-id")
             let url = "<?php echo e(url('/store-owner/addon/disable/')); ?>/"+id;
             window.location.href = url;
          });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/addons.blade.php ENDPATH**/ ?>