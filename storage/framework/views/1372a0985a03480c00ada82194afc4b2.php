
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.spPageTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .delivery-div {
        background-color: #fafafa;
        padding: 1rem;
    }

    .location-search-block {
        position: relative;
        top: -26rem;
        z-index: 999;
    }
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <span class="badge badge-primary badge-pill animated flipInX"><?php echo e(count($restaurants)); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewRestaurant"
                    data-toggle="modal" data-target="#addNewRestaurantModal">
                    <b><i class="icon-plus2"></i></b>
                    <?php echo e(__('storeDashboard.spAddNewStoreBtn')); ?>

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
                            <th style="width: 5%;"><?php echo e(__('storeDashboard.spTableID')); ?></th>
                            <th style="width: 10%;"><?php echo e(__('storeDashboard.spTableImage')); ?></th>
                            <th style="width: 15%;"><?php echo e(__('storeDashboard.spTableName')); ?></th>
                            <th><?php echo e(__('storeDashboard.spTableAddress')); ?></th>
                            <th><?php echo e(__('storeDashboard.spTableStatus')); ?></th>
                            <th style="width: 15%"><?php echo e(__('storeDashboard.spTableCA')); ?></th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($restaurant->id); ?></td>
                            <td><img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?><?php echo e($restaurant->image); ?>"
                                    alt="<?php echo e($restaurant->name); ?>" height="80" width="80"
                                    style="border-radius: 0.275rem;"></td>
                            <td><?php echo e($restaurant->name); ?></td>
                            <td><?php echo e($restaurant->address); ?></td>
                            <td>
                                <?php if(!$restaurant->is_accepted): ?>
                                <span class="badge badge-flat border-grey-800 text-default text-capitalize">
                                    <?php echo e(__('storeDashboard.spRowPending')); ?>

                                </span>
                                <?php endif; ?>
                                <span class="badge badge-flat border-grey-800 text-default text-capitalize">
                                    <?php if($restaurant->is_active): ?> <?php echo e(__('storeDashboard.spRowActive')); ?> <?php else: ?>
                                    <?php echo e(__('storeDashboard.spRowInActive')); ?> <?php endif; ?>
                                </span>
                            </td>
                            <td><?php echo e($restaurant->created_at->diffForHumans()); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-justified">
                                    <a href="<?php echo e(route('restaurant.get.editRestaurant', $restaurant->id)); ?>"
                                        class="btn btn-sm btn-primary"> <?php echo e(__('storeDashboard.edit')); ?></a>
                                        <a href="<?php echo e(route('restaurant.get.tableShiftRestaurant', $restaurant->id)); ?>"
                                        class="badge badge-primary badge-icon ml-1"> <?php echo e(__('storeDashboard.resTableShift')); ?> <i
                                        class="icon-cog3 ml-1"></i></a>
                                    <a href="<?php echo e(route('restaurant.get.settingsRestaurant', $restaurant->id)); ?>"
                                        class="badge badge-primary badge-icon ml-1"> <?php echo e(__('storeDashboard.resSetting')); ?> <i
                                        class="icon-cog3 ml-1"></i></a>
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
<div id="addNewRestaurantModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.spAddNewStoreBtn')); ?></span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.saveNewRestaurant')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.seLblStoreName')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="<?php echo e(__('storeDashboard.sePhStoreName')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.seLblDescription')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="description"
                                placeholder="<?php echo e(__('storeDashboard.sePhDescription')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.seLblImage')); ?>:</label>
                        <div class="col-lg-9">
                            <img class="slider-preview-image hidden" />
                            <div class="uploader">
                                <input type="file" class="form-control-lg form-control-uniform" name="image" required
                                    accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                                <span class="help-text text-muted"><?php echo e(__('storeDashboard.sePhImage')); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.seLblApproxDeliveryTime')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg delivery_time" name="delivery_time"
                                placeholder="<?php echo e(__('storeDashboard.sePhApproxDeliveryTime')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.seLblApproxPriceForTwo')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg price_range" name="price_range"
                                placeholder="<?php echo e(__('storeDashboard.sePhApproxPriceForTwo')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                required>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.seLblFullAddress')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="address"
                                placeholder="<?php echo e(__('storeDashboard.sePhFullAddress')); ?>" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label" data-popup="tooltip"
                            title="<?php echo e(__('storeDashboard.seToolTipPincode')); ?>"
                            data-placement="bottom"><?php echo e(__('storeDashboard.seLblPincode')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="pincode"
                                placeholder="<?php echo e(__('storeDashboard.seToolTipPincode')); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblLandMark')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="landmark"
                                placeholder="<?php echo e(__('storeDashboard.sePhLandMark')); ?>">
                        </div>
                    </div>

                    <?php if(config('setting.googleApiKeyNoRestriction') != null): ?>
                    <fieldset class="gllpLatlonPicker">
                        <div width="100%" id="map" class="gllpMap" style="position: relative; overflow: hidden;"></div>
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLat')); ?>:</label><input
                                    type="text" class="form-control form-control-lg gllpLatitude latitude"
                                    value="40.6976701" name="latitude"
                                    placeholder="<?php echo e(__('storeDashboard.storeLatitudeFieldPlaceholder')); ?>"
                                    required="required" readonly="readonly">
                            </div>
                            <div class="col-lg-6">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLong')); ?>:</label><input
                                    type="text" class="form-control form-control-lg gllpLongitude longitude"
                                    value="-74.2598672" name="longitude"
                                    placeholder="<?php echo e(__('storeDashboard.storeLongitudeFieldPlaceholder')); ?>"
                                    required="required" readonly="readonly">
                            </div>
                        </div>
                        <input type="hidden" class="gllpZoom" value="20">
                        <div class="d-flex justify-content-center">
                            <div class="col-lg-9 d-flex location-search-block">
                                <input type="text" class="form-control form-control-lg gllpSearchField"
                                    placeholder="<?php echo e(__('storeDashboard.locationSearchPlaceholder')); ?>">
                                <button type="button"
                                    class="btn btn-primary gllpSearchButton"><?php echo e(__('storeDashboard.locationSearchBtnTxt')); ?></button>
                            </div>
                        </div>
                    </fieldset>
                    <?php else: ?>

                    <fieldset class="gllpLatlonPicker">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLat')); ?>:</label><input
                                    type="text" class="form-control form-control-lg gllpLatitude latitude"
                                    value="40.6976701" name="latitude"
                                    placeholder="<?php echo e(__('storeDashboard.storeLatitudeFieldPlaceholder')); ?>"
                                    required="required" readonly="readonly">
                            </div>
                            <div class="col-lg-6">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLong')); ?>:</label><input
                                    type="text" class="form-control form-control-lg gllpLongitude longitude"
                                    value="-74.2598672" name="longitude"
                                    placeholder="<?php echo e(__('storeDashboard.storeLongitudeFieldPlaceholder')); ?>"
                                    required="required" readonly="readonly">
                            </div>
                        </div>
                        <span class="text-muted"><?php echo e(__('storeDashboard.sePhTextLatLong1')); ?> <a
                                href="https://www.mapcoordinates.net/en"
                                target="_blank">https://www.mapcoordinates.net/en</a></span> <br>
                        <?php echo e(__('storeDashboard.sePhTextLatLong2')); ?>

                    </fieldset>
                    <?php endif; ?>

                    <hr>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblCertificate')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="certificate"
                                placeholder="<?php echo e(__('storeDashboard.sePhCertificate')); ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblStoreCharge')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg restaurant_charges"
                                name="restaurant_charges"
                                placeholder="<?php echo e(__('storeDashboard.sePhStoreCharge')); ?> <?php echo e(config('setting.currencyFormat')); ?>">
                        </div>
                    </div>

                    <?php if(config("setting.enSPU") == "true"): ?>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span
                                class="text-danger">*</span><?php echo e(__('storeDashboard.storeDeliveryTypeLabel')); ?>:</label>
                        <div class="col-lg-9">
                            <select class="form-control select-search" name="delivery_type" required>
                                <option value="1" class="text-capitalize">
                                    <?php echo e(__('storeDashboard.storeDeliveryTypeDeliveryOption')); ?></option>
                                <option value="2" class="text-capitalize">
                                    <?php echo e(__('storeDashboard.storeDeliveryTypeSelfPickupOption')); ?></option>
                                <option value="3" class="text-capitalize">
                                    <?php echo e(__('storeDashboard.storeDeliveryTypeBothOption')); ?></option>
                            </select>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblPureVeg')); ?></label>
                        <div class="col-lg-9">
                            <div class="checkbox checkbox-switchery mt-2">
                                <label>
                                    <input value="true" type="checkbox" class="switchery-primary" checked="checked"
                                        name="is_pureveg">
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblMinOrderPrice')); ?>:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg min_order_price"
                                name="min_order_price"
                                placeholder="<?php echo e(__('storeDashboard.sePhMinOrderPrice')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                value="0">
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
    function readURL(input) {
       if (input.files && input.files[0]) {
           let reader = new FileReader();
           reader.onload = function (e) {
               $('.slider-preview-image')
                   .removeClass('hidden')
                   .attr('src', e.target.result)
                   .width(160)
                   .height(117)
                   .css('borderRadius', '0.275rem');
           };
           reader.readAsDataURL(input.files[0]);
       }
    }
    $(function () {
       $('.select-search').select2({
           minimumResultsForSearch: Infinity,
           placeholder: 'Select Location',
       });
    
       var primary = document.querySelector('.switchery-primary');
       var switchery = new Switchery(primary, { color: '#2196F3' });
       
       $('.form-control-uniform').uniform({
            fileDefaultHtml: '<?php echo e(__('storeDashboard.fileSectionNoFileSelected')); ?>',
            fileButtonHtml: '<?php echo e(__('storeDashboard.fileSectionChooseFileButton')); ?>'
        });

       $('.delivery_time').numeric({allowThouSep:false});
       $('.price_range').numeric({allowThouSep:false});
       $('.latitude').numeric({allowThouSep:false});
       $('.longitude').numeric({allowThouSep:false});
       $('.restaurant_charges').numeric({ allowThouSep:false, maxDecimalPlaces: 2 });
       $('.delivery_charges').numeric({ allowThouSep:false, maxDecimalPlaces: 2 });
        $('.min_order_price').numeric({ allowThouSep:false, maxDecimalPlaces: 2, allowMinus: false });
    });
    
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/restaurants.blade.php ENDPATH**/ ?>