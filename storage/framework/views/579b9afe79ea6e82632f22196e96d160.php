
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.mcpPageTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($count); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left"
                    data-toggle="modal" data-target="#addNewItemCategory">
                <b><i class="icon-plus2"></i></b>
                <?php echo e(__('storeDashboard.mcpAddNewCatBtn')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('storeDashboard.mcpTableCatId')); ?></th>
                            <th><?php echo e(__('storeDashboard.mcpTableName')); ?></th>
                            <th><?php echo e(__('storeDashboard.mcpTableNOI')); ?></th>
                            <th><?php echo e(__('storeDashboard.mcpTableStatus')); ?></th>
                            <th><?php echo e(__('storeDashboard.mcpTableCA')); ?></th>
                            <th class="text-center"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $itemCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($itemCategory->id); ?></td>
                            <td><?php echo e($itemCategory->name); ?></td>
                            <td><?php echo e($itemCategory->items_count); ?></td>
                            <td><span class="badge badge-flat border-grey-800 text-default text-capitalize"><?php if($itemCategory->is_enabled): ?> <?php echo e(__('storeDashboard.mcpEnabled')); ?> <?php else: ?> <?php echo e(__('storeDashboard.mcpDisabled')); ?> <?php endif; ?></span></td>
                            <td><?php echo e($itemCategory->created_at->diffForHumans()); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-justified align-items-center">
                                    <a href="javascript:void(0)" data-toggle="modal" data-target="#editItemCategory" data-catid="<?php echo e($itemCategory->id); ?>" data-catname="<?php echo e($itemCategory->name); ?>"
                                            class="btn btn-sm btn-primary editItemCategory"> <?php echo e(__('storeDashboard.edit')); ?> </a>
                                   <div class="checkbox checkbox-switchery ml-1" style="padding-top: 0.8rem;">
                                       <label>
                                       <input value="true" type="checkbox" class="action-switch"
                                       <?php if($itemCategory->is_enabled): ?> checked="checked" <?php endif; ?> data-id="<?php echo e($itemCategory->id); ?>">
                                       </label>
                                   </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="addNewItemCategory" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.mcpModalTitle')); ?></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.createItemCategory')); ?>" method="POST">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.mcpModalLabelName')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="<?php echo e(__('storeDashboard.mcpModalPlaceHolderName')); ?>" required>
                        </div>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                        <?php echo e(__('storeDashboard.save')); ?>

                        <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="editItemCategory" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.editItemCategoryName')); ?></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.updateItemCategory')); ?>" method="POST">
                    <input type="hidden" name="id" id="itemCatId">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.mcpModalLabelName')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="<?php echo e(__('storeDashboard.mcpModalPlaceHolderName')); ?>" required id="itemCatName">
                        </div>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                        <?php echo e(__('storeDashboard.save')); ?>

                        <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('.editItemCategory').click(function(event) {
            $('#itemCatId').val($(this).data("catid"));
            $('#itemCatName').val($(this).data("catname"));
        });
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
            let url = "<?php echo e(url('/store-owner/itemcategory/disable/')); ?>/"+id;
            window.location.href = url;
         });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/itemcategories.blade.php ENDPATH**/ ?>