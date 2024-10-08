
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
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.sePageTitleEditing')); ?></span>
                <span class="badge badge-primary badge-pill animated flipInX"><?php echo e($restaurant->name); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>

        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <a href="<?php echo e(route('restaurant.sortMenusAndItems', $restaurant->id)); ?>"
                    class="btn btn-secondary btn-labeled btn-labeled-left mr-2">
                    <b><i class="icon-sort"></i></b>
                    <?php echo e(__('storeDashboard.sortMenuAndItemButton')); ?>

                </a>
            </div>
        </div>

    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('restaurant.updateRestaurant')); ?>" method="POST"
                        enctype="multipart/form-data">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <i class="icon-store2 mr-2"></i> <?php echo e(__('storeDashboard.sePageTitleStoreDetails')); ?>

                        </legend>
                        <input type="hidden" name="id" value="<?php echo e($restaurant->id); ?>">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.seLblStoreName')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->name); ?>" type="text" class="form-control form-control-lg"
                                    name="name" placeholder="<?php echo e(__('storeDashboard.sePhStoreName')); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.seLblDescription')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->description); ?>" type="text"
                                    class="form-control form-control-lg" name="description"
                                    placeholder="<?php echo e(__('storeDashboard.sePhDescription')); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblImage')); ?>:</label>
                            <div class="col-lg-9">
                                <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?><?php echo e($restaurant->image); ?>"
                                    alt="Image" width="160" style="border-radius: 0.275rem;">
                                <img class="slider-preview-image hidden" style="border-radius: 0.275rem;" />
                                <div class="uploader">
                                    <input type="hidden" name="old_image" value="<?php echo e($restaurant->image); ?>">
                                    <input type="file" class="form-control-uniform" name="image"
                                        accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                                    <span class="help-text text-muted"><?php echo e(__('storeDashboard.sePhImage')); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.seLblApproxDeliveryTime')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->delivery_time); ?>" type="text"
                                    class="form-control form-control-lg delivery_time" name="delivery_time"
                                    placeholder="<?php echo e(__('storeDashboard.sePhApproxDeliveryTime')); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.seLblApproxPriceForTwo')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->price_range); ?>" type="text"
                                    class="form-control form-control-lg price_range" name="price_range"
                                    placeholder="<?php echo e(__('storeDashboard.sePhApproxPriceForTwo')); ?> <?php echo e(config('setting.currencyFormat')); ?>"
                                    required>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.seLblFullAddress')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->address); ?>" type="text"
                                    class="form-control form-control-lg" name="address"
                                    placeholder="<?php echo e(__('storeDashboard.sePhFullAddress')); ?>" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" data-popup="tooltip"
                                title="<?php echo e(__('storeDashboard.seToolTipPincode')); ?>"
                                data-placement="bottom"><?php echo e(__('storeDashboard.seLblPincode')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->pincode); ?>" type="text"
                                    class="form-control form-control-lg" name="pincode"
                                    placeholder="<?php echo e(__('storeDashboard.seToolTipPincode')); ?>" readonly="readonly">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblLandMark')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->landmark); ?>" type="text"
                                    class="form-control form-control-lg" name="landmark"
                                    placeholder="<?php echo e(__('storeDashboard.sePhLandMark')); ?>" readonly="readonly">
                            </div>
                        </div>

                        <?php if(config('setting.googleApiKeyNoRestriction') != null): ?>
                        <fieldset class="gllpLatlonPicker">
                            <div width="100%" id="map" class="gllpMap" style="position: relative; overflow: hidden;">
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLat')); ?>:</label><input
                                        type="text" class="form-control form-control-lg gllpLatitude latitude"
                                        value="<?php echo e($restaurant->latitude); ?>" name="latitude"
                                        placeholder="<?php echo e(__('storeDashboard.storeLatitudeFieldPlaceholder')); ?>"
                                        required="required">
                                </div>
                                <div class="col-lg-6">
                                    <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLong')); ?>:</label><input
                                        type="text" class="form-control form-control-lg gllpLongitude longitude"
                                        value="<?php echo e($restaurant->longitude); ?>" name="longitude"
                                        placeholder="<?php echo e(__('storeDashboard.storeLongitudeFieldPlaceholder')); ?>"
                                        required="required">
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
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLat')); ?>:</label><input
                                    type="text" class="form-control form-control-lg gllpLatitude latitude"
                                    value="<?php echo e($restaurant->latitude); ?>" name="latitude"
                                    placeholder="<?php echo e(__('storeDashboard.storeLatitudeFieldPlaceholder')); ?>"
                                    required="required">
                            </div>
                            <div class="col-lg-6">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seLblLong')); ?>:</label><input
                                    type="text" class="form-control form-control-lg gllpLongitude longitude"
                                    value="<?php echo e($restaurant->longitude); ?>" name="longitude"
                                    placeholder="<?php echo e(__('storeDashboard.storeLongitudeFieldPlaceholder')); ?>"
                                    required="required">
                            </div>
                        </div>
                        <span class="text-muted"><?php echo e(__('storeDashboard.sePhTextLatLong1')); ?> <a
                                href="https://www.mapcoordinates.net/en"
                                target="_blank">https://www.mapcoordinates.net/en</a></span> <br>
                        <?php echo e(__('storeDashboard.sePhTextLatLong2')); ?>

                        <?php endif; ?>

                        <hr>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblCertificate')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->certificate); ?>" type="text"
                                    class="form-control form-control-lg" name="certificate"
                                    placeholder="<?php echo e(__('storeDashboard.sePhCertificate')); ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblStoreCharge')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->restaurant_charges); ?>" type="text"
                                    class="form-control form-control-lg restaurant_charges" name="restaurant_charges"
                                    placeholder="<?php echo e(__('storeDashboard.sePhStoreCharge')); ?> <?php echo e(config('setting.currencyFormat')); ?>">
                            </div>
                        </div>

                        <?php if(config("setting.enSPU") == "true"): ?>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><span
                                    class="text-danger">*</span><?php echo e(__('storeDashboard.storeDeliveryTypeLabel')); ?>:</label>
                            <div class="col-lg-9">
                                <select class="form-control select" name="delivery_type" required>
                                    <option value="1" class="text-capitalize" <?php if($restaurant->delivery_type == "1"): ?>
                                        selected="selected"
                                        <?php endif; ?>><?php echo e(__('storeDashboard.storeDeliveryTypeDeliveryOption')); ?></option>
                                    <option value="2" class="text-capitalize" <?php if($restaurant->delivery_type == "2"): ?>
                                        selected="selected"
                                        <?php endif; ?>><?php echo e(__('storeDashboard.storeDeliveryTypeSelfPickupOption')); ?></option>
                                    <option value="3" class="text-capitalize" <?php if($restaurant->delivery_type == "3"): ?>
                                        selected="selected" <?php endif; ?>><?php echo e(__('storeDashboard.storeDeliveryTypeBothOption')); ?>

                                    </option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(config('setting.allowPaymentGatewaySelection') == "true"): ?>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label"><?php echo e(__('storeDashboard.seLblSelectPaymentGateways')); ?>


                                <?php if(count($restaurant->payment_gateways) == 0): ?>
                                <p class="text-danger">
                                    <strong><?php echo e(__('storeDashboard.seNoStorePaymentGatewayMessage')); ?></strong>
                                </p>
                                <?php endif; ?>
                            </label>

                            <div class="col-lg-8">
                                <select multiple="multiple" class="form-control select" name="store_payment_gateways[]">
                                    <?php $__currentLoopData = $adminPaymentGateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $adminPaymentGateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($adminPaymentGateway->id); ?>" class="text-capitalize"
                                        <?php echo e(in_array($adminPaymentGateway->id, $restaurant->payment_gateways()->pluck('payment_gateway_id')->toArray()) ? 'selected' : ''); ?>>
                                        <?php echo e($adminPaymentGateway->name); ?>

                                    </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Stripe Public Key:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->stripe_public_key); ?>" type="text"
                                    class="form-control form-control-lg stripe_public_key" name="stripe_public_key"
                                    placeholder="Stripe Public Key (Leave blank if not using Stripe)">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Stripe Secret Key:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->stripe_secret_key); ?>" type="text"
                                    class="form-control form-control-lg stripe_secret_key" name="stripe_secret_key"
                                    placeholder="Stripe Secret Key (Leave blank if not using Stripe)">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblPureVeg')); ?></label>
                            <div class="col-lg-9">
                                <div class="checkbox checkbox-switchery mt-2">
                                    <label>
                                        <input value="true" type="checkbox" class="switchery-primary"
                                            <?php if($restaurant->is_pureveg): ?> checked="checked" <?php endif; ?> name="is_pureveg">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblMinOrderPrice')); ?>:</label>
                            <div class="col-lg-9">
                                <input value="<?php echo e($restaurant->min_order_price); ?>" type="text"
                                    class="form-control form-control-lg min_order_price" name="min_order_price"
                                    placeholder="<?php echo e(__('storeDashboard.sePhMinOrderPrice')); ?> <?php echo e(config('setting.currencyFormat')); ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label
                                class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.seLblAutomaticScheduling')); ?></label>
                            <div class="col-lg-9">
                                <div class="checkbox checkbox-switchery mt-2">
                                    <label>
                                        <input value="true" type="checkbox" class="switchery-primary"
                                            <?php if($restaurant->is_schedulable): ?> checked="checked" <?php endif; ?>
                                        name="is_schedulable">
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <?php if(\Nwidart\Modules\Facades\Module::find('OrderSchedule') &&
                            \Nwidart\Modules\Facades\Module::find('OrderSchedule')->isEnabled()): ?>
                            <?php if($restaurant->is_schedulable): ?>
                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label"><?php echo e(__('orderScheduleLang.orderSchedulingOptionLabel')); ?></label>
                                <div class="col-lg-9">
                                    <div class="checkbox checkbox-switchery mt-2">
                                        <label>
                                            <input value="true" type="checkbox" class="switchery-primary"
                                                <?php if($restaurant->accept_scheduled_orders): ?> checked="checked" <?php endif; ?>
                                            name="accept_scheduled_orders">
                                        </label>
                                    </div>
                                    <?php echo e(__('orderScheduleLang.orderSchedulingHelpTest')); ?>

                                </div>
                            </div>
                            <?php else: ?>
                            <mark><?php echo e(__('orderScheduleLang.orderSchedulingInfoHelpText')); ?></mark>
                            <?php endif; ?>

                            <?php if($restaurant->is_schedulable && $restaurant->accept_scheduled_orders): ?>
                            <div class="form-group row">
                                <label
                                    class="col-lg-3 col-form-label"><?php echo e(__('orderScheduleLang.todayOrderScheduleAfterLabel')); ?></label>
                                <div class="col-lg-9">
                                    <input value="<?php echo e($restaurant->schedule_slot_buffer); ?>" type="text"
                                        class="form-control form-control-lg schedule_slot_buffer"
                                        name="schedule_slot_buffer" placeholder="In Minutes">
                                    <mark><?php echo e(__('orderScheduleLang.todayOrderScheduleAfterHelpText')); ?></mark>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php endif; ?>

                        </div>
                        <?php echo csrf_field(); ?>
                        <div class="text-left">
                            <div class="btn-group btn-group-justified" style="width: 150px">
                                <?php if($restaurant->is_active): ?>
                                <a href="<?php echo e(route('restaurant.disableRestaurant', $restaurant->id)); ?>"
                                    class="btn btn-danger btn-labeled btn-labeled-left mr-2" data-popup="tooltip"
                                    title="<?php echo e(__('storeDashboard.closeStoreToolTipMessage')); ?>" data-placement="bottom">
                                    <b><i class="icon-switch2"></i></b>
                                    <?php echo e(__('storeDashboard.seDisable')); ?>

                                </a>
                                <?php else: ?>
                                <a href="<?php echo e(route('restaurant.disableRestaurant', $restaurant->id)); ?>"
                                    class="btn btn-secondary btn-labeled btn-labeled-left mr-2" data-popup="tooltip"
                                    title="<?php echo e(__('storeDashboard.openStoreToolTipMessage')); ?>" data-placement="bottom">
                                    <b><i class="icon-switch2"></i></b>
                                    <?php echo e(__('storeDashboard.seEnable')); ?>

                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
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
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('restaurant.updateStorePayoutDetails')); ?>" method="POST">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <i class="icon-coin-dollar mr-2"></i> <?php echo e(__('storeDashboard.payoutAccountDetailsTitle')); ?>

                        </legend>
                        <input type="hidden" name="restaurant_id" value="<?php echo e($restaurant->id); ?>">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><strong><?php echo e(__('storeDashboard.bankNameLabel')); ?>:
                                </strong></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="bankName"
                                    value="<?php if(!empty($payoutData->bankName)): ?><?php echo e($payoutData->bankName); ?><?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><strong><?php echo e(__('storeDashboard.bankCodeLabel')); ?>:
                                </strong></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="bankCode"
                                    value="<?php if(!empty($payoutData->bankCode)): ?><?php echo e($payoutData->bankCode); ?><?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><strong><?php echo e(__('storeDashboard.recipientNameLabel')); ?>:
                                </strong></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="recipientName"
                                    value="<?php if(!empty($payoutData->recipientName)): ?><?php echo e($payoutData->recipientName); ?><?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><strong><?php echo e(__('storeDashboard.accountNumberLabel')); ?>:
                                </strong></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="accountNumber"
                                    value="<?php if(!empty($payoutData->accountNumber)): ?><?php echo e($payoutData->accountNumber); ?><?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><strong><?php echo e(__('storeDashboard.paypalIdLabel')); ?>:
                                </strong></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="paypalId"
                                    value="<?php if(!empty($payoutData->paypalId)): ?><?php echo e($payoutData->paypalId); ?><?php endif; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label"><strong><?php echo e(__('storeDashboard.upiIDLabel')); ?>:
                                </strong></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="upiID"
                                    value="<?php if(!empty($payoutData->upiID)): ?><?php echo e($payoutData->upiID); ?><?php endif; ?>">
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
        <?php if($restaurant->is_schedulable): ?>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo e(route('restaurant.updateRestaurantScheduleData')); ?>" method="POST"
                        enctype="multipart/form-data">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            <i class="icon-alarm mr-2"></i> <?php echo e(__('storeDashboard.seStoreSchedulingTimes')); ?>

                        </legend>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seMonday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->monday) && count($schedule_data->monday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->monday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="monday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="monday[]" required>
                            </div>
                            <div class="col-lg-2" day="monday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="monday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="monday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seTuesday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->tuesday) && count($schedule_data->tuesday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->tuesday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="tuesday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="tuesday[]" required>
                            </div>
                            <div class="col-lg-2" day="tuesday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="tuesday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="tuesday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seWednesday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->wednesday) && count($schedule_data->wednesday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->wednesday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="wednesday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="wednesday[]" required>
                            </div>
                            <div class="col-lg-2" day="wednesday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="wednesday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="wednesday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seThursday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->thursday) && count($schedule_data->thursday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->thursday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="thursday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="thursday[]" required>
                            </div>
                            <div class="col-lg-2" day="thursday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="thursday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="thursday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seFriday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->friday) && count($schedule_data->friday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->friday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="friday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="friday[]" required>
                            </div>
                            <div class="col-lg-2" day="friday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="friday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="friday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seSaturday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->saturday) && count($schedule_data->saturday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->saturday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="saturday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="saturday[]" required>
                            </div>
                            <div class="col-lg-2" day="saturday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="saturday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="saturday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <div class="form-group row mb-0">
                            <div class="col-lg-4">
                                <h3><?php echo e(__('storeDashboard.seSunday')); ?></h3>
                            </div>
                        </div>
                        <!-- Checks if there is any schedule data -->
                        <?php if(!empty($schedule_data->sunday) && count($schedule_data->sunday) > 0): ?>
                        <!-- If yes Then Loop Each Data as Time SLots -->
                        <?php $__currentLoopData = $schedule_data->sunday; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="form-group row">
                            <div class="col-lg-5">
                                <label class="col-form-label"><?php echo e(__('storeDashboard.seOpeningTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->open); ?>"
                                    name="sunday[]" required>
                            </div>
                            <div class="col-lg-5">
                                <label class="col-form-label"></span><?php echo e(__('storeDashboard.seClosingTime')); ?></label>
                                <input type="time" class="form-control form-control-lg" value="<?php echo e($time->close); ?>"
                                    name="sunday[]" required>
                            </div>
                            <div class="col-lg-2" day="sunday">
                                <label class="col-form-label text-center" style="width: 43px;"></span><i
                                        class="icon-circle-down2"></i></label><br>
                                <button class="remove btn btn-danger" data-popup="tooltip" data-placement="right"
                                    title="Remove Time Slot">
                                    <i class="icon-cross2"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                        <div id="sunday" class="timeSlots">
                        </div>
                        <a href="javascript:void(0)" onclick="add(this)" data-day="sunday"
                            class="btn btn-secondary btn-labeled btn-labeled-left mr-2"> <b><i
                                    class="icon-plus22"></i></b><?php echo e(__('storeDashboard.seAddSlot')); ?></a>
                        <hr>
                        <input type="text" name="restaurant_id" hidden value="<?php echo e($restaurant->id); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" data-popup="tooltip"
                                title="<?php echo e(__('storeDashboard.seScheduleUpdateMsg')); ?>" data-placement="bottom">
                                <?php echo e(__('storeDashboard.update')); ?>

                                <i class="icon-database-insert ml-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
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
     function add(data) {
        var para = document.createElement("div");
        let day = data.getAttribute("data-day")
        para.innerHTML ="<div class='form-group row'> <div class='col-lg-5'><label class='col-form-label'><?php echo e(__('storeDashboard.seOpeningTime')); ?></label><input type='time' class='form-control form-control-lg' name='"+day+"[]' required> </div> <div class='col-lg-5'> <label class='col-form-label'><?php echo e(__('storeDashboard.seClosingTime')); ?></label><input type='time' class='form-control form-control-lg' name='"+day+"[]'  required> </div> <div class='col-lg-2'> <label class='col-form-label text-center' style='width: 43px'></span><i class='icon-circle-down2'></i></label><br><button class='remove btn btn-danger' data-popup='tooltip' data-placement='right' title='<?php echo e(__('storeDashboard.seRemoveTimeSlot')); ?>'><i class='icon-cross2'></i></button></div></div>";
        document.getElementById(day).appendChild(para);
    }
    $(function () {

        $('body').tooltip({
            selector: 'button'
        });

        $(document).on("click", ".remove", function() {
            $(this).tooltip('hide')
            $(this).parent().parent().remove();
        });
        
        $('.select').select2({
            minimumResultsForSearch: Infinity,
        });
    
         if (Array.prototype.forEach) {
               var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-primary'));
               elems.forEach(function(html) {
                   var switchery = new Switchery(html, { color: '#2196F3' });
               });
           }
           else {
               var elems = document.querySelectorAll('.switchery-primary');
               for (var i = 0; i < elems.length; i++) {
                   var switchery = new Switchery(elems[i], { color: '#2196F3' });
               }
           }
    
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/editRestaurant.blade.php ENDPATH**/ ?>