
<?php $__env->startSection("title"); ?> Edit Item - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2">Editing</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"> <?php echo e($item->name); ?> </span>
                <span class="small">(Store: <a
                        href="<?php echo e(route('admin.get.editRestaurant', $item->restaurant->id)); ?>"><?php echo e($item->restaurant->name); ?>)</a>
                </span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="<?php echo e(route('admin.updateItem')); ?>" method="POST" enctype="multipart/form-data">
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        <i class="icon-address-book mr-2"></i> Item Details
                    </legend>
                    <input type="hidden" name="id" value="<?php echo e($item->id); ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Item Name:</label>
                        <div class="col-lg-9">
                            <input value="<?php echo e($item->name); ?>" type="text" class="form-control form-control-lg"
                                name="name" placeholder="Item Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Item Description:</label>
                        <div class="col-lg-9">
                            <textarea class="summernote-editor" name="desc" placeholder="Item Description"
                                rows="6"><?php echo e($item->desc); ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">POS Product ID:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="bepoz_pid" placeholder="POS Product ID" value="<?php echo e($item->bepoz_pid); ?>"
                                >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">POS Product Size:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="bepoz_psize" placeholder="POS Product Size" value="<?php echo e($item->bepoz_psize); ?>"
                                >
                        </div>
                    </div>

                    <?php if($item->old_price == 0): ?>
                    <div class="form-group row" style="display: none;" id="discountedTwoPrice">
                        <div class="col-lg-6">
                            <label class="col-form-label">Mark Price:</label>
                            <input type="text" class="form-control form-control-lg price" name="old_price"
                                placeholder="Item Price in <?php echo e(config('setting.currencyFormat')); ?>"
                                value="<?php echo e($item->old_price); ?>">
                        </div>
                        <div class="col-lg-6">
                            <label class="col-form-label"><span class="text-danger">*</span>Selling Price:</label>
                            <input type="text" class="form-control form-control-lg price" name="price"
                                placeholder="Item Price in <?php echo e(config('setting.currencyFormat')); ?>" id="newSP"
                                value="<?php echo e($item->price); ?>">
                        </div>
                    </div>

                    <div class="form-group row" id="singlePrice">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Price:</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control form-control-lg price" name="price"
                                placeholder="Item Price in <?php echo e(config('setting.currencyFormat')); ?>" required id="oldSP"
                                value="<?php echo e($item->price); ?>">
                        </div>
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2"
                                id="addDiscountedPrice">
                                <b><i class="icon-percent"></i></b>
                                Add Dicounted Price
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
                    <?php else: ?>
                    <div class="form-group row">
                        <div class="col-lg-6">
                            <label class="col-form-label">Mark Price: <i class="icon-question3 ml-1"
                                    data-popup="tooltip" title="Make this filed empty or zero if not required"
                                    data-placement="top"></i> </label>
                            <input type="text" class="form-control form-control-lg price" name="old_price"
                                placeholder="Item Price in <?php echo e(config('setting.currencyFormat')); ?>"
                                value="<?php echo e($item->old_price); ?>">
                        </div>
                        <div class="col-lg-6">
                            <label class="col-form-label"><span class="text-danger">*</span>Selling Price:</label>
                            <input type="text" class="form-control form-control-lg price" name="price"
                                placeholder="Item Price in <?php echo e(config('setting.currencyFormat')); ?>" id="newSP"
                                value="<?php echo e($item->price); ?>">
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Item's
                            Restaurant:</label>
                        <div class="col-lg-9">
                            <select class="form-control select" name="restaurant_id" required>
                                <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($restaurant->id); ?>" class="text-capitalize" <?php if($item->restaurant_id
                                    == $restaurant->id): ?> selected="selected" <?php endif; ?>><?php echo e($restaurant->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Item's
                            Category:</label>
                        <div class="col-lg-9">
                            <select class="form-control select" name="item_category_id" required>
                                <?php $__currentLoopData = $itemCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $itemCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($itemCategory->id); ?>" class="text-capitalize" <?php if($item->
                                    item_category_id == $itemCategory->id): ?> selected="selected"
                                    <?php endif; ?>><?php echo e($itemCategory->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Item's Addon Categories:</label>
                        <div class="col-lg-9">

                            <select multiple="multiple" class="form-control addonCategorySelect" data-fouc
                                name="addon_category_item[]">
                                <?php $__currentLoopData = $addonCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addonCategory): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($addonCategory->id); ?>" class="text-capitalize"
                                    <?php echo e(isset($item) &&  in_array($item->id, $addonCategory->items()->pluck('item_id')->toArray()) ? 'selected' : ''); ?>>
                                    <?php echo e($addonCategory->name); ?> <?php if($addonCategory->description != null): ?>->
                                    <?php echo e($addonCategory->description); ?> <?php endif; ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Image:</label>
                        <div class="col-lg-9">
                            <?php if($item->image): ?>
                            <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?><?php echo e($item->image); ?>" alt="Image"
                                width="160" style="border-radius: 0.275rem;">
                            <br>
                            <span id="removeItemImage" class="cursor-pointer text-warning"><u>Remove
                                    Image</u></span>
                            <script>
                                $('#removeItemImage').click(function(event) {
                                conf = confirm('Are you sure?');
                                    if (conf == true) {
                                        window.location.href="<?php echo e(route('admin.removeItemImage', $item->id)); ?>";
                                    } 
                                });
                            </script>
                            <?php endif; ?>
                            <img class="slider-preview-image hidden" style="border-radius: 0.275rem;" />
                            <div class="uploader">
                                <input type="hidden" name="old_image" value="<?php echo e($item->image); ?>">
                                <input type="file" class="form-control-lg form-control-uniform" name="image"
                                    accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                                <span class="help-text text-muted">Image dimension 486x355</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Is Recommended?</label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary recommendeditem"
                                        <?php if($item->is_recommended): ?> checked="checked" <?php endif; ?> name="is_recommended">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Is Popular?</label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary popularitem"
                                        <?php if($item->is_popular): ?> checked="checked" <?php endif; ?> name="is_popular">
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Is New?</label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary newitem"
                                        <?php if($item->is_new): ?> checked="checked" <?php endif; ?> name="is_new">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label display-block">Veg/Non-Veg: </label>
                        <div class="col-lg-9 d-flex align-items-center">
                            <label class="radio-inline mr-2">
                                <input type="radio" <?php if($item->is_veg): ?> checked="checked" <?php endif; ?> name="is_veg"
                                value="veg">
                                Veg
                            </label>

                            <label class="radio-inline mr-2">
                                <input type="radio" <?php if(!$item->is_veg): ?> checked="checked" <?php endif; ?> name="is_veg"
                                value="nonveg">
                                Non-Veg
                            </label>

                            <label class="radio-inline mr-2">
                                <input type="radio" <?php if(is_null($item->is_veg)): ?> checked="checked" <?php endif; ?> name="is_veg"
                                value="none">
                                None
                            </label>
                        </div>
                    </div>

                    <?php echo csrf_field(); ?>
                    <div class="text-left">
                        <div class="btn-group btn-group-justified" style="width: 150px">
                            <?php if($item->is_active): ?>
                            <a class="btn btn-danger" href="<?php echo e(route('admin.disableItem', $item->id)); ?>">
                                DISABLE
                                <i class="icon-switch2 ml-1"></i>
                            </a>
                            <?php else: ?>
                            <a class="btn btn-primary" href="<?php echo e(route('admin.disableItem', $item->id)); ?>">
                                ENABLE
                                <i class="icon-switch2 ml-1"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            UPDATE
                            <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
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
        $('.addonCategorySelect').select2({
            closeOnSelect: false
        })
    
         var recommendeditem = document.querySelector('.recommendeditem');
        new Switchery(recommendeditem, { color: '#f44336' });
    
        var popularitem = document.querySelector('.popularitem');
        new Switchery(popularitem, { color: '#8360c3' });
    
        var newitem = document.querySelector('.newitem');
        new Switchery(newitem, { color: '#333' });

        
        $('.form-control-uniform').uniform();
        $('.price').numeric({allowThouSep:false, maxDecimalPlaces: 2 });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/editItem.blade.php ENDPATH**/ ?>