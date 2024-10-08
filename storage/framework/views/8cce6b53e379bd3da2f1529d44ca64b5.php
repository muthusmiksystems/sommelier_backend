
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.ipPageTitle')); ?>

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
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewItem"
                    data-toggle="modal" data-target="#addNewItemModal">
                    <b><i class="icon-plus2"></i></b>
                    <?php echo e(__('storeDashboard.ipAddNewItemBtn')); ?>

                </button>
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="addBulkItem"
                    data-toggle="modal" data-target="#addBulkItemModal">
                    <b><i class="icon-database-insert"></i></b>
                    <?php echo e(__('storeDashboard.ipBulkCsvUpload')); ?>

                </button>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <form action="<?php echo e(route('restaurant.post.searchItems')); ?>" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input"
                placeholder="<?php echo e(__('storeDashboard.ipSearchPH')); ?>" name="query">
            <div class="form-control-feedback form-control-feedback-lg">
                <i class="icon-search4"></i>
            </div>
        </div>
        <?php echo csrf_field(); ?>
    </form>
    <?php if($agent->isDesktop()): ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('storeDashboard.ipTableImage')); ?></th>
                            <th><?php echo e(__('storeDashboard.ipTableName')); ?></th>
                            <th><?php echo e(__('storeDashboard.ipTableItemRestaurant')); ?></th>
                            <th><?php echo e(__('storeDashboard.ipTableItemCategory')); ?></th>
                            <th><?php echo e(__('storeDashboard.ipTablePrice')); ?></th>
                            <th><?php echo e(__('storeDashboard.ipTableAttributes')); ?></th>
                            <th style="width: 15%"><?php echo e(__('storeDashboard.ipTableCA')); ?></th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php if($item->image): ?>
                                <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?><?php echo e($item->image); ?>"
                                    alt="<?php echo e($item->name); ?>" height="80" width="80" style="border-radius: 0.275rem;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item->name); ?></td>
                            <td><?php echo e($item->restaurant->name); ?></td>
                            <td><?php echo e($item->item_category->name); ?></td>
                            <td><?php echo e($item->price); ?></td>
                            <td>
                                <?php if($item->is_recommended): ?>
                                <span class="badge badge-flat border-grey-800 text-danger text-capitalize mr-1">
                                    <?php echo e(__('storeDashboard.ipRowRecommended')); ?>

                                </span>
                                <?php endif; ?>
                                <?php if($item->is_popular): ?>
                                <span class="badge badge-flat border-grey-800 text-primary text-capitalize mr-1">
                                    <?php echo e(__('storeDashboard.ipRowPopular')); ?>

                                </span>
                                <?php endif; ?>
                                <?php if($item->is_new): ?>
                                <span class="badge badge-flat border-grey-800 text-default text-capitalize mr-1">
                                    <?php echo e(__('storeDashboard.ipRowNew')); ?>

                                </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($item->created_at->diffForHumans()); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-justified align-items-center">
                                    <a href="<?php echo e(route('restaurant.get.editItem', $item->id)); ?>"
                                        class="btn btn-sm btn-primary"> <?php echo e(__('storeDashboard.edit')); ?> </a>

                                    <div class="checkbox checkbox-switchery ml-1" style="padding-top: 0.8rem;">
                                        <label>
                                            <input value="true" type="checkbox" class="action-switch"
                                                <?php if($item->is_active): ?> checked="checked" <?php endif; ?>
                                            data-id="<?php echo e($item->id); ?>">
                                        </label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($items->appends($_GET)->links()); ?>

                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card">
        <div class="card-header pb-0">
            <div class="d-flex">
                <div>
                    <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?><?php echo e($item->image); ?>" alt="<?php echo e($item->name); ?>"
                        height="80" width="80" style="border-radius: 0.275rem;">
                </div>
                <div class="ml-3">
                    <h4 class="mb-0"><strong><?php echo e($item->name); ?></strong></h4>
                    <span><?php echo e($item->restaurant->name); ?></span><br>
                    <span><?php echo e($item->item_category->name); ?></span>
                </div>
            </div>
        </div>
        <hr>
        <div class="card-body pt-0 pb-2">
            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="mb-0"><strong><?php echo e(config('setting.currencyFormat')); ?><?php echo e($item->price); ?></strong></h4>
                </div>
                <div>
                    <?php if($item->is_recommended): ?>
                    <span class="badge badge-flat border-grey-800 text-danger text-capitalize mr-1">
                        <?php echo e(__('storeDashboard.ipRowRecommended')); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($item->is_popular): ?>
                    <span class="badge badge-flat border-grey-800 text-primary text-capitalize mr-1">
                        <?php echo e(__('storeDashboard.ipRowPopular')); ?>

                    </span>
                    <?php endif; ?>
                    <?php if($item->is_new): ?>
                    <span class="badge badge-flat border-grey-800 text-default text-capitalize mr-1">
                        <?php echo e(__('storeDashboard.ipRowNew')); ?>

                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <a href="<?php echo e(route('restaurant.get.editItem', $item->id)); ?>"
                        class="btn btn-secondary btn-labeled btn-labeled-left">
                        <b><i class="icon-database-edit2"></i></b>
                        <?php echo e(__('storeDashboard.edit')); ?>

                    </a>
                </div>
                <div>
                    <div class="checkbox checkbox-switchery" style="padding-top: 0.93rem;">
                        <label>
                            <input value="true" type="checkbox" class="action-switch-mobile" <?php if($item->is_active): ?>
                            checked="checked" <?php endif; ?> data-id="<?php echo e($item->id); ?>">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="mt-4">
        <?php echo e($items->appends($_GET)->links()); ?>

    </div>
    <?php endif; ?>
</div>
<div id="addNewItemModal" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.ipmTitle')); ?></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.saveNewItem')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.ipmLabelName')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="<?php echo e(__('storeDashboard.ipmPhName')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ipmLabelDescription')); ?></label>
                        <div class="col-lg-9">
                            <textarea class="summernote-editor" name="desc"
                                placeholder="<?php echo e(__('storeDashboard.ipmPhDescription')); ?>" rows="6"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">POS Product ID:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="bepoz_pid" placeholder="POS Product ID"
                                >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">POS Product Size:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="bepoz_psize" placeholder="POS Product Size"
                                >
                        </div>
                    </div> 
                    <div class="form-group row" style="display: none;" id="discountedTwoPrice">
                        <div class="col-lg-6">
                            <label class="col-form-label"><?php echo e(__('storeDashboard.ipmLabelMarkPrice')); ?>: <i
                                    class="icon-question3 ml-1" data-popup="tooltip"
                                    title="<?php echo e(__('storeDashboard.ipmMarkPriceToolTip')); ?>"
                                    data-placement="top"></i></label>
                            <input type="text" class="form-control form-control-lg price" name="old_price"
                                placeholder="<?php echo e(__('storeDashboard.ipmOldPricePh')); ?> <?php echo e(config('setting.currencyFormat')); ?>">
                        </div>
                        <div class="col-lg-6">
                            <label class="col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.ipmLabelSellingPrice')); ?>:</label>
                            <input type="text" class="form-control form-control-lg price" name="price"
                                placeholder="<?php echo e(__('storeDashboard.ipmOldPricePh')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                id="newSP">
                        </div>
                    </div>
                    <div class="form-group row" id="singlePrice">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.ipmLabelPrice')); ?>:</label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control form-control-lg price" name="price"
                                placeholder="<?php echo e(__('storeDashboard.ipmOldPricePh')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                required id="oldSP">
                        </div>
                        <div class="col-lg-3">
                            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2"
                                id="addDiscountedPrice">
                                <b><i class="icon-percent"></i></b>
                                <?php echo e(__('storeDashboard.ipmAddDiscountBtn')); ?>

                            </button>
                        </div>
                    </div>
                    <script>
                        $('#addDiscountedPrice').click(function(event) {
                            let price = $('#oldSP').val();
                            $('#newSP').val(price).attr('required', 'required');;
                            $('#singlePrice').remove();
                            $('#discountedTwoPrice').show();
                        });
                    </script>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.ipmLabelItemRestaurant')); ?>:</label>
                        <div class="col-lg-9">
                            <select class="form-control select" name="restaurant_id" required>
                                <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($restaurant->id); ?>" class="text-capitalize"><?php echo e($restaurant->name); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.ipmLabelItemCategory')); ?>:</label>
                        <div class="col-lg-9">
                            <select class="form-control select" name="item_category_id" required>
                                <?php $__currentLoopData = $itemCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($itemCategory->id); ?>" class="text-capitalize">
                                    <?php echo e($itemCategory->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ipmLabelAddonCategory')); ?>:</label>
                        <div class="col-lg-9">
                            <select multiple="multiple" class="form-control select" data-fouc
                                name="addon_category_item[]">
                                <?php $__currentLoopData = $addonCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addonCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($addonCategory->id); ?>" class="text-capitalize">
                                    <?php echo e($addonCategory->name); ?> <?php if($addonCategory->description != null): ?>->
                                    <?php echo e($addonCategory->description); ?> <?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ipmLabelImage')); ?>:</label>
                        <div class="col-lg-9">
                            <img class="slider-preview-image hidden" />
                            <div class="uploader">
                                <input type="file" class="form-control-lg form-control-uniform" name="image"
                                    accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                                <span class="help-text text-muted"><?php echo e(__('storeDashboard.ipmImageHelperText')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ipmLabelIsRecommended')); ?></label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary recommendeditem"
                                        checked="checked" name="is_recommended">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ipmLabelIsPopular')); ?></label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary popularitem"
                                        checked="checked" name="is_popular">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ipmLabelIsNew')); ?></label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary newitem"
                                        checked="checked" name="is_new">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label
                            class="col-lg-3 col-form-label display-block"><?php echo e(__('storeDashboard.itemVegNonVegLabel')); ?>:
                        </label>
                        <div class="col-lg-9 d-flex align-items-center">
                            <label class="radio-inline mr-2">
                                <input type="radio" name="is_veg" value="veg">
                                <?php echo e(__('storeDashboard.itemVegLabel')); ?>

                            </label>

                            <label class="radio-inline mr-2">
                                <input type="radio" name="is_veg" value="nonveg">
                                <?php echo e(__('storeDashboard.itemNonVegLabel')); ?>

                            </label>

                            <label class="radio-inline mr-2">
                                <input type="radio" name="is_veg" value="none" checked="checked">
                                <?php echo e(__('storeDashboard.itemIsVegNoneLabel')); ?>

                            </label>
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
<div id="addBulkItemModal" class="modal fade mt-5" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.ipmCsvTitle')); ?></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.itemBulkUpload')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><?php echo e(__('storeDashboard.ipmLabelCsvFile')); ?>: </label>
                        <div class="col-lg-10">
                            <div class="uploader">
                                <input type="file" accept=".csv" name="item_csv"
                                    class="form-control-uniform form-control-lg" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-left">
                        <button type="button" class="btn btn-primary" id="downloadSampleItemCsv">
                            <?php echo e(__('storeDashboard.ipmBtnCsvDownloadSample')); ?>

                            <i class="icon-file-download ml-1"></i>
                        </button>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <?php echo e(__('storeDashboard.ipmBtnCsvUpload')); ?>

                            <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function readURL(input) {
       if (input.files && input.files[0]) {
           let reader = new FileReader();
           reader.onload = function (e) {
               $('.slider-preview-image')
                   .removeClass('hidden')
                   .attr('src', e.target.result)
                   .width(120)
                   .height(120);
           };
           reader.readAsDataURL(input.files[0]);
       }
    }
    $(function () {
        $('.summernote-editor').summernote({
                   height: 200,
                   popover: {
                       image: [],
                       link: [],
                       air: []
                     }
            });
        
        $('.select').select2();
    
       var recommendeditem = document.querySelector('.recommendeditem');
       new Switchery(recommendeditem, { color: '#f44336' });
    
       var popularitem = document.querySelector('.popularitem');
       new Switchery(popularitem, { color: '#8360c3' });
    
       var newitem = document.querySelector('.newitem');
       new Switchery(newitem, { color: '#333' });

       
       $('.form-control-uniform').uniform({
            fileDefaultHtml: '<?php echo e(__('storeDashboard.fileSectionNoFileSelected')); ?>',
            fileButtonHtml: '<?php echo e(__('storeDashboard.fileSectionChooseFileButton')); ?>'
        });
       
        $('#downloadSampleItemCsv').click(function(event) {
           event.preventDefault();
           window.location.href = "<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/docs/items-sample-csv.csv";
       });
        $('.price').numeric({allowThouSep:false, maxDecimalPlaces: 2 });

        //Switch Action Function  
         var elems = document.querySelectorAll('.action-switch');
         for (var i = 0; i < elems.length; i++) {
             var switchery = new Switchery(elems[i], { color: '#8360c3' });
         }
         var elemsmb = document.querySelectorAll('.action-switch-mobile');
         for (var i = 0; i < elemsmb.length; i++) {
             var switchery = new Switchery(elemsmb[i], { color: '#8360c3' });
         }     

         $('.action-switch, .action-switch-mobile').click(function(event) {
           console.log("Clicked");
            let id = $(this).attr("data-id")
            let url = "<?php echo e(url('/store-owner/item/disable/')); ?>/"+id;
            let self = $(this);
           $.ajax({
               url: url,
               type: 'GET',
               dataType: 'JSON',
           })
           .done(function(data) {
               console.log(data);
               console.log(self);
               $.jGrowl("", {
                   position: 'bottom-center',
                   header: 'Operation Successful ✅',
                   theme: 'bg-success',
                   life: '1800'
               }); 
           })
           .fail(function(data) {
               console.log(data);
               $.jGrowl("", {
                   position: 'bottom-center',
                   header: 'Something went wrong, please try again.',
                   theme: 'bg-danger',
                   life: '1800'
               }); 
           })            
         });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/items.blade.php ENDPATH**/ ?>