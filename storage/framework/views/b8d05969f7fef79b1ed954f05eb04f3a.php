
<?php $__env->startSection("title"); ?>
<?php echo e(__('storeDashboard.dashboardTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

<?php if(config('setting.oneSignalAppId') != null && config('setting.oneSignalRestApiKey') != null): ?>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js"></script>
<script>
    window.OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
      OneSignal.init({
        appId: "<?php echo e(config('setting.oneSignalAppId')); ?>",
      });
    });
    let user_id = "<?php echo e(Auth::user()->id); ?>";
    
    OneSignal.push(function() {
      OneSignal.on('subscriptionChange', function(isSubscribed) {
        if (isSubscribed) {
          OneSignal.push(function() {
            OneSignal.setExternalUserId(user_id);
          });
        }
      });
    });
</script>
<?php endif; ?>

<div class="content mb-5">
    <div class="row mt-3">
        <div class="col-6 col-xl-3 mt-2">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-left" href="<?php echo e(route('restaurant.restaurants')); ?>">
                    <div class="block-content block-content-full clearfix">
                        <div class="float-right mt-10 d-none d-sm-block">
                            <i class="dashboard-display-icon icon-store2"></i>
                        </div>
                        <div class="dashboard-display-number"><?php echo e($restaurantsCount); ?></div>
                        <div class="font-size-sm text-uppercase text-muted"><?php echo e(__('storeDashboard.dashboardStores')); ?>

                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-6 col-xl-3 mt-2">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-left" href="<?php echo e(route('restaurant.orders')); ?>">
                    <div class="block-content block-content-full clearfix">
                        <div class="float-right mt-10 d-none d-sm-block">
                            <i class="dashboard-display-icon icon-basket"></i>
                        </div>
                        <div class="dashboard-display-number"><?php echo e($ordersCount); ?></div>
                        <div class="font-size-sm text-uppercase text-muted">
                            <?php echo e(__('storeDashboard.dashboardOrdersProcessed')); ?></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-6 col-xl-3 mt-2">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-left" href="javascript:void(0)">
                    <div class="block-content block-content-full clearfix">
                        <div class="float-right mt-10 d-none d-sm-block">
                            <i class="dashboard-display-icon icon-stack-star"></i>
                        </div>
                        <div class="dashboard-display-number"><?php echo e($orderItemsCount); ?></div>
                        <div class="font-size-sm text-uppercase text-muted"><?php echo e(__('storeDashboard.dashboardItemsSold')); ?>

                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-6 col-xl-3 mt-2 store-dashboard-stats--Earnings">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-left" href="<?php echo e(route('restaurant.earnings')); ?>">
                    <div class="block-content block-content-full clearfix">
                        <div class="float-right mt-10 d-none d-sm-block">
                            <i class="dashboard-display-icon icon-coin-dollar"></i>
                        </div>
                        <div class="dashboard-display-number"><?php echo e(config('setting.currencyFormat')); ?>

                            <?php echo e(floatval($totalEarning)); ?>

                        </div>
                        <div class="font-size-sm text-uppercase text-muted"><?php echo e(__('storeDashboard.dashboardEarnings')); ?>

                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row pt-4 p-0">
        <div class="col-xl-12">
            <div class="panel panel-flat dashboard-main-col mt-4">

                <?php if($autoPrinting): ?>
                <button class="btn text-danger btn-lg mr-2 mt-2 float-right printerStatus" data-popup="tooltip"
                    data-placement="right" title="<?php echo e(__('thermalPrinterLang.connectingToPrinterFailedMessage')); ?>"
                    style="border: 0; background-color: #F5F5F5;"><i class="icon-printer4"></i>
                </button>
                <?php endif; ?>

                <div class="panel-heading">
                    <h4 class="panel-title pl-3 pt-3"><strong><?php echo e(__('storeDashboard.dashboardNewOrders')); ?></strong></h4>
                    <hr>
                </div>
                <div id="newOrdersTable" class="table-responsive <?php if(!count($newOrders)): ?> hidden <?php endif; ?>">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th><?php echo e(__('storeDashboard.dashboardOrderID')); ?></th>
                                <th class="text-center"><i class="
                                    icon-circle-down2"></i></th>
                                <th><?php echo e(__('storeDashboard.dashboardStore')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardPrice')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderStatus')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $newOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $nO): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('restaurant.viewOrder', $nO->unique_order_id)); ?>"
                                        class="letter-icon-title">#<?php echo e(substr ($nO->unique_order_id, -9)); ?></a>
                                    <?php if($nO->schedule_slot != null): ?>
                                    <br>
                                    <small>
                                        <mark class="px-0">
                                            <?php echo e(json_decode($nO->schedule_date)->day); ?>,
                                            <?php echo e(json_decode($nO->schedule_date)->date); ?><br>
                                            <?php echo e(json_decode($nO->schedule_slot)->open); ?> -
                                            <?php echo e(json_decode($nO->schedule_slot)->close); ?>

                                        </mark>
                                    </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center new-order-actions">
                                    <?php if($nO->orderstatus_id == 1): ?>
                                    <a href="javascript:void(0)"
                                        class="btn btn-primary btn-labeled btn-labeled-left mr-2 accpetOrderBtnTableList"
                                        data-id=<?php echo e($nO->id); ?>> <b><i class="icon-checkmark3 ml-1"></i> </b>
                                        <?php echo e(__('storeDashboard.dashboardAcceptOrder')); ?> </a>
                                    <?php endif; ?>
                                    <?php if($nO->orderstatus_id == 10): ?>
                                    <a href="<?php echo e(route('restaurant.confirmScheduledOrder', $nO->id)); ?>"
                                        class="btn btn-success btn-labeled btn-labeled-left mr-2 confirmOrderBtnTableList"
                                        data-id=<?php echo e($nO->id); ?>> <b><i class="icon-checkmark3 ml-1"></i> </b>
                                        <?php echo e(__('orderScheduleLang.dashboardConfirmScheduledOrder')); ?> </a>
                                    <?php endif; ?>

                                    <a href="<?php echo e(route('restaurant.cancelOrder', $nO->id)); ?>"
                                        class="btn btn-danger btn-labeled btn-labeled-right mr-2 cancelOrderBtnTableList"
                                        data-popup="tooltip" data-placement="right"
                                        title="<?php echo e(__('storeDashboard.dashboardDoubleClickMsg')); ?>"> <b><i
                                                class="icon-cross ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardCancelOrder')); ?> </a>
                                </td>
                                <td>
                                    <?php echo e($nO->restaurant->name); ?>

                                </td>
                                <?php
                                if(!is_null($nO->tip_amount)) {
                                $nOTotal = $nO->total - $nO->tip_amount;
                                } else {
                                $nOTotal = $nO->total;
                                }
                                ?>
                                <td>
                                    <span class="text-semibold no-margin"><?php echo e(config('setting.currencyFormat')); ?>

                                        <?php echo e($nOTotal); ?></span>
                                </td>
                                <td>
                                    <?php if($nO->orderstatus_id == 1): ?>
                                    <span class="badge badge-flat border-grey-800 text-default text-capitalize">
                                        <?php echo e(__('storeDashboard.dashboardNew')); ?>

                                    </span>
                                    <?php endif; ?>

                                    <?php if($nO->orderstatus_id == 10): ?>
                                    <span class="badge badge-warning text-white text-capitalize">
                                        <?php echo e(__('orderScheduleLang.scheduledOrderStatusText')); ?>

                                    </span>
                                    <?php endif; ?>

                                    <?php if($nO->delivery_type == 2): ?>
                                    <span class="badge badge-flat border-danger-800 text-default text-capitalize">
                                        <?php echo e(__('storeDashboard.dashboardSelfPickup')); ?>

                                    </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <?php if(!count($newOrders)): ?>
                <div class="text-center text-muted pb-2" id="newOrdersNoOrdersMessage">
                    <h4> <?php echo e(__('storeDashboard.dashboardNoOrders')); ?> </h4>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-xl-12">
            <div class="panel panel-flat dashboard-main-col mt-4">
                <div class="panel-heading">
                    <h4 class="panel-title pl-3 pt-3"><strong><?php echo e(__('storeDashboard.dashboardPreparingOrders')); ?></strong>
                    </h4>
                    <hr>
                </div>
                <div class="table-responsive">
                    <?php if(count($preparingOrders)): ?>
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th><?php echo e(__('storeDashboard.dashboardOrderID')); ?></th>
                                <th class="text-center"><i class="
                                    icon-circle-down2"></i></th>
                                <th><?php echo e(__('storeDashboard.dashboardPrice')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderPlacedTime')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderAcceptedTime')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $preparingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pO): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('restaurant.viewOrder', $pO->unique_order_id)); ?>"
                                        class="letter-icon-title">#<?php echo e(substr ($pO->unique_order_id, -9)); ?></a>
                                    <?php if($pO->schedule_slot != null): ?>
                                    <br>
                                    <small>
                                        <mark class="px-0">
                                            <?php echo e(json_decode($pO->schedule_date)->day); ?>,
                                            <?php echo e(json_decode($pO->schedule_date)->date); ?><br>
                                            <?php echo e(json_decode($pO->schedule_slot)->open); ?> -
                                            <?php echo e(json_decode($pO->schedule_slot)->close); ?>

                                        </mark>
                                    </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center accepted-order-actions">
                                    <?php if($pO->delivery_type == 2 && $pO->orderstatus_id == 2): ?>
                                    <a href="<?php echo e(route('restaurant.markOrderReady', $pO->id)); ?>"
                                        class="btn btn-warning btn-labeled btn-labeled-left mr-2 actionAfterAccept">
                                        <b><i class="icon-checkmark3 ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardMarkAsReady')); ?> </a>
                                    <?php endif; ?>
                                    <?php if($pO->delivery_type == 2 && $pO->orderstatus_id == 7): ?>
                                    <a href="<?php echo e(route('restaurant.markSelfPickupOrderAsCompleted', $pO->id)); ?>"
                                        class="btn btn-success btn-labeled btn-labeled-left mr-2 actionAfterAccept">
                                        <b><i class="icon-checkmark3 ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardMarkAsCompleted')); ?> </a>
                                    <?php endif; ?>

                                    <?php if($pO->orderstatus_id == 11): ?>
                                    <span class="badge badge-warning text-white text-capitalize">
                                        <?php echo e(__('orderScheduleLang.scheduledOrderStatusText')); ?>

                                    </span>
                                    <?php elseif($pO->orderstatus_id == "3"): ?>
                                    <span class="badge badge-dark text-white text-capitalize">
                                        <?php echo e(__('storeDashboard.dashboardOrderAcceptedByDelivery')); ?>

                                    </span>
                                    <?php else: ?>
                                    <span>--</span>
                                    <?php endif; ?>

                                </td>
                                <?php
                                if(!is_null($pO->tip_amount)) {
                                $pOTotal = $pO->total - $pO->tip_amount;
                                } else {
                                $pOTotal = $pO->total;
                                }
                                ?>
                                <td>
                                    <span class="text-semibold no-margin"><?php echo e(config('setting.currencyFormat')); ?>

                                        <?php echo e($pOTotal); ?></span>
                                </td>
                                <td>
                                    <?php echo e($pO->created_at->diffForHumans()); ?>

                                </td>
                                <td>
                                    <?php echo e($pO->updated_at->diffForHumans()); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center text-muted pb-2">
                        <h4> <?php echo e(__('storeDashboard.dashboardNoOrders')); ?> </h4>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if(config('setting.enSPU')== "true"): ?>
        <div class="col-xl-12">
            <div class="panel panel-flat dashboard-main-col mt-4">
                <div class="panel-heading">
                    <h4 class="panel-title pl-3 pt-3">
                        <strong><?php echo e(__('storeDashboard.dashboardSelfpickupOrders')); ?></strong></h4>
                    <hr>
                </div>
                <div class="table-responsive">
                    <?php if(count($selfpickupOrders)): ?>
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th><?php echo e(__('storeDashboard.dashboardOrderID')); ?></th>
                                <th class="text-center"><i class="
                                    icon-circle-down2"></i></th>
                                <th><?php echo e(__('storeDashboard.dashboardPrice')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderPlacedTime')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderAcceptedTime')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $selfpickupOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $spO): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('restaurant.viewOrder', $spO->unique_order_id)); ?>"
                                        class="letter-icon-title">#<?php echo e(substr ($spO->unique_order_id, -9)); ?></a>
                                </td>
                                <td class="text-center accepted-order-actions">
                                    <?php if($spO->delivery_type == 2 && $spO->orderstatus_id == 2): ?>
                                    <a href="<?php echo e(route('restaurant.markOrderReady', $spO->id)); ?>"
                                        class="btn btn-warning btn-labeled btn-labeled-left mr-2 actionAfterAccept">
                                        <b><i class="icon-checkmark3 ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardMarkAsReady')); ?> </a>
                                    <?php endif; ?>
                                    <?php if($spO->delivery_type == 2 && $spO->orderstatus_id == 7): ?>
                                    <a href="<?php echo e(route('restaurant.markSelfPickupOrderAsCompleted', $spO->id)); ?>"
                                        class="btn btn-success btn-labeled btn-labeled-left mr-2 actionAfterAccept">
                                        <b><i class="icon-checkmark3 ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardMarkAsCompleted')); ?> </a>
                                    <?php endif; ?>
                                    <?php if($spO->delivery_type == 1): ?>
                                    <span>--</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-semibold no-margin"><?php echo e(config('setting.currencyFormat')); ?>

                                        <?php echo e($spO->total); ?></span>
                                </td>
                                <td>
                                    <?php echo e($spO->created_at->diffForHumans()); ?>

                                </td>
                                <td>
                                    <?php echo e($spO->updated_at->diffForHumans()); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center text-muted pb-2">
                        <h4> <?php echo e(__('storeDashboard.dashboardNoOrders')); ?> </h4>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div class="col-xl-12">
            <div class="panel panel-flat dashboard-main-col mt-4">
                <div class="panel-heading">
                    <h4 class="panel-title pl-3 pt-3">
                        <strong><?php echo e(__('storeDashboard.dashboardOngoingDeliveries')); ?></strong></h4>
                    <hr>
                </div>
                <?php if(count($ongoingOrders)): ?>
                <div class="table-responsive">
                    <table class="table text-nowrap">
                        <thead>
                            <tr>
                                <th><?php echo e(__('storeDashboard.dashboardOrderID')); ?></th>
                                <th class="text-center"><i class="
                                    icon-circle-down2"></i></th>
                                <th><?php echo e(__('storeDashboard.dashboardPrice')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderPlacedTime')); ?></th>
                                <th><?php echo e(__('storeDashboard.dashboardOrderPickedupTime')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $ongoingOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ogO): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <a href="<?php echo e(route('restaurant.viewOrder', $ogO->unique_order_id)); ?>"
                                        class="letter-icon-title">#<?php echo e(substr ($ogO->unique_order_id, -9)); ?></a>
                                </td>
                                <td class="text-center accepted-order-actions">
                                    <?php if($ogO->delivery_type == 2 && $ogO->orderstatus_id == 2): ?>
                                    <a href="<?php echo e(route('restaurant.markOrderReady', $ogO->id)); ?>"
                                        class="btn btn-warning btn-labeled btn-labeled-left mr-2 actionAfterAccept">
                                        <b><i class="icon-checkmark3 ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardMarkAsReady')); ?> </a>
                                    <?php endif; ?>
                                    <?php if($ogO->delivery_type == 2 && $ogO->orderstatus_id == 7): ?>
                                    <a href="<?php echo e(route('restaurant.markSelfPickupOrderAsCompleted', $ogO->id)); ?>"
                                        class="btn btn-success btn-labeled btn-labeled-left mr-2 actionAfterAccept">
                                        <b><i class="icon-checkmark3 ml-1"></i></b>
                                        <?php echo e(__('storeDashboard.dashboardMarkAsCompleted')); ?> </a>
                                    <?php endif; ?>
                                    <?php if($ogO->delivery_type == 1): ?>
                                    <span>--</span>
                                    <?php endif; ?>
                                </td>
                                <?php
                                if(!is_null($ogO->tip_amount)) {
                                $ogOTotal = $ogO->total - $ogO->tip_amount;
                                } else {
                                $ogOTotal = $ogO->total;
                                }
                                ?>
                                <td>
                                    <span class="text-semibold no-margin"><?php echo e(config('setting.currencyFormat')); ?>

                                        <?php echo e($ogOTotal); ?></span>
                                </td>
                                <td>
                                    <?php echo e($ogO->created_at->diffForHumans()); ?>

                                </td>
                                <td>
                                    <?php echo e($ogO->updated_at->diffForHumans()); ?>

                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center text-muted pb-2">
                        <h4> <?php echo e(__('storeDashboard.dashboardNoOrders')); ?> </h4>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo e(csrf_token()); ?>" class="csrfToken">
<div id="newOrderModal" class="modal fade mt-5" tabindex="-1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h5 class="modal-title mt-3"> <i class="icon-bell3 animated-bell"></i> </h5>
            </div>
            <div class="float-right pr-3 pt-3" style="position: absolute; right: 0;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="newOrdersData">
                <div class="d-flex justify-content-center">
                    <h3 class="text-muted"> <?php echo e(__('storeDashboard.dashboardNoOrders')); ?> </h3>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var autoPrinting = '<?php echo e($autoPrinting); ?>';

    if (autoPrinting) {
        var socket = null;
        var socket_host = 'ws://127.0.0.1:6441';

        initializeSocket = function() {
            try {
                if (socket == null) {
                    socket = new WebSocket(socket_host);
                    socket.onopen = function() {};
                    socket.onmessage = function(msg) {
                        let message = msg.data;
                        $.jGrowl("", {
                            position: 'bottom-center',
                            header: message,
                            theme: 'bg-danger',
                            life: '5000'
                        });
                    };
                    socket.onclose = function() {
                        socket = null;
                    };
                }
            } catch (e) {
                console.log("ERROR", e);
            }

            var checkSocketConnecton = setInterval(function() {
                if (socket == null || socket.readyState != 1) {
                    $('.printerStatus').attr('data-original-title', '<?php echo e(__('thermalPrinterLang.connectingToPrinterFailedMessage')); ?>').removeClass('text-success').addClass('text-danger');
                }
                if (socket != null && socket.readyState == 1) {
                     $('.printerStatus').removeClass('text-danger').addClass('text-success').attr('data-original-title', '<?php echo e(__('thermalPrinterLang.connectionSuccessToLocalServer')); ?>');
                }
                clearInterval(checkSocketConnecton);
            }, 500)
        };
    }


    $(function() {

        if (autoPrinting) {
            initializeSocket();
        }

        var touchtime = 0;
        
        let notification = document.createElement('audio');
        let notificationFileRoute = '<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/tones/<?php echo e(config('setting.restaurantNotificationAudioTrack')); ?>.mp3';
           notification.setAttribute('src', notificationFileRoute);
           notification.setAttribute('type', 'audio/mp3');
           // notification.setAttribute('muted', 'muted');
           notification.setAttribute('loop', 'true');
        
        $(".stopSound").click(function(event) {
            notification.pause();
            notification.currentTime = 0;
        });
        
      
        const newOrderJson = <?php echo json_encode($newOrders, 15, 512) ?>;
        console.log(newOrderJson);
    
        setInterval(function() {
            $.ajax({
                url: '<?php echo e(route('restaurant.getNewOrders')); ?>',
                type: 'POST',
                dataType: 'json',
                data: {listed_order_ids: <?php echo json_encode($newOrdersIds, 15, 512) ?>, _token: $('.csrfToken').val()},
            })
    
            .done(function(newArray) {
                console.log("New Array", newArray)
                console.log(newOrderJson.length);
                console.log(newArray.length );
    
                if (newArray.length > 0) {
                    //if new orders, then lenght will be greater, if order cancelled, then it should not go inside this 
                    console.log("NEW ORDER")
                
                    $('#newOrderModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
    
                    //play sound
                    notification.play();
    
                    console.log("New Array", newArray)
                    // const newOrder = newArray[0];
    
                    let newOrderData = "";
                    $.map(newArray, function(order, index) {
                        if(order.delivery_type == 2) {
                            var selfPickup = '<span class="badge badge-flat border-danger-800 text-default text-capitalize ml-1"><?php echo e(__('storeDashboard.dashboardSelfPickup')); ?></span>'
                        } else {
                            selfPickup = "";
                        }
    
                        let viewOrderURL = "<?php echo e(url('/store-owner/order')); ?>/" + order.unique_order_id;
                        
                        console.log(order);

                        if (order.tip_amount != null) {
                            var orderTotal = parseFloat(order.total) - parseFloat(order.tip_amount); 
                        } else {
                             var orderTotal = order.total;
                        }
                        newOrderData +='<div class="popup-order mb-3"><div class="text-center my-3 h5"><strong><span class="text-semibold no-margin"><?php echo e(config('setting.currencyFormat')); ?>'+orderTotal+'</span> <i class="icon-arrow-right5"></i> <a href="'+viewOrderURL+'">'+order.unique_order_id+'</a> <i class="icon-arrow-right5"></i>'+order.restaurant.name+'</strong> '+ selfPickup +'</div>';
    
                        newOrderData += '<div class="d-flex justify-content-center"><button data-id="'+order.id+'" class="btn btn-primary btn-labeled btn-labeled-left mr-2 acceptOrderBtn"><b><i class="icon-checkmark3 ml-1"></i></b> <?php echo e(__('storeDashboard.dashboardAcceptOrder')); ?> </a> <button data-id="'+order.id+'" class="btn btn-danger btn-labeled btn-labeled-right mr-2 cancelOrderBtnPopup" data-popup="tooltip" data-placement="top" title="<?php echo e(__('storeDashboard.dashboardDoubleClickMsg')); ?>"> <b><i class="icon-cross ml-1"></i></b> <?php echo e(__('storeDashboard.dashboardCancelOrder')); ?>  </a></div></div>'
                        
    
                        $('#newOrdersData').html(newOrderData);
                        $('#newOrdersNoOrdersMessage').remove();
                    });
                    
                } else {
                    console.log("NO New Order")
                    //when orde has been accepted or denied, length will be 0, close the model
                    $('#newOrderModal').modal('hide');
                }
            })
            .fail(function() {
                console.log("error");
            })  
        }, <?php echo e(config("setting.restaurantNewOrderRefreshRate")); ?> * 1000); //all API every x seconds (config settings from admin)
        
        //reload page when popup closed
        $('#newOrderModal').on('hidden.bs.modal', function () {
            window.location.reload();
        })
    
    
        //on single click, accpet order and disable block
        $('body').on("click", ".acceptOrderBtn", function(e) {
            
            let elem = $(this);
            let context = $(this).parents('.popup-order');
            context.addClass('popup-order-processing').prepend('<div class="d-flex pt-2 pr-2 float-right"><i class="icon-spinner10 spinner"></i></div>')

            <?php if($autoPrinting): ?>
                console.log("autoPrinting is enabled...")
                let printType = null;
                let order_id = $(this).data("id");
                let token = $('.csrfToken').val();

                $.ajax({
                    url: '<?php echo e(route('thermalprinter.getOrderData')); ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {order_id: order_id, _token: token, print_type: printType },
                })
                .done(function(response) {
                    let content = {};
                    content.type = 'print-receipt';
                    content.data = response;
                    let sendData = JSON.stringify(content);
                        if (socket != null && socket.readyState == 1) {
                            socket.send(sendData);
                            console.log("print command sent...")
                            $.jGrowl("", {
                                position: 'bottom-center',
                                header: '<?php echo e(__('thermalPrinterLang.printCommandSentMessage')); ?>',
                                theme: 'bg-success',
                                life: '3000'
                            });
                            setTimeout(function() {
                                popupAcceptOrderBtn(elem,context);
                            }, 2500);
                        } else {
                            initializeSocket();
                            setTimeout(function() {
                                socket.send(sendData);
                                console.log("print command sent...")
                                $.jGrowl("", {
                                    position: 'bottom-center',
                                    header: '<?php echo e(__('storeDashboard.printCommandSentMessage')); ?>',
                                    theme: 'bg-success',
                                    life: '5000'
                                });
                            }, 700);
                            setTimeout(function() {
                                popupAcceptOrderBtn(elem,context);
                            }, 2500);
                        }
                    })
                    .fail(function() {
                        console.log("Print Error.")
                        popupAcceptOrderBtn(elem,context);
                    })
                <?php else: ?>
                    console.log("autoPrinting is disabled...")
                    popupAcceptOrderBtn(elem,context);
                <?php endif; ?>
            
        });

        popupAcceptOrderBtn = function(elem, context) {
            let id = elem.attr("data-id");
            let acceptOrderUrl = "<?php echo e(url('/store-owner/orders/accept-order')); ?>/" +id;
            $.ajax({
                url: acceptOrderUrl,
                type: 'GET',
                dataType: 'JSON',
            })
            .done(function(data) {
                // $(context).remove();
                //count number of order on popup, if 0 then remove popup
                $('#newOrderModal').modal('hide');
                $.jGrowl("<?php echo e(__('storeDashboard.orderAcceptedNotification')); ?>", {
                    position: 'bottom-center',
                    header: '<?php echo e(__('storeDashboard.successNotification')); ?>',
                    theme: 'bg-success',
                    life: '5000'
                });
            })
            .fail(function() {
                console.log("error")
                $.jGrowl("<?php echo e(__('storeDashboard.orderSomethingWentWrongNotification')); ?>", {
                    position: 'bottom-center',
                    header: '<?php echo e(__('storeDashboard.woopssNotification')); ?>',
                    theme: 'bg-warning',
                    life: '5000'
                });
            })
        }

        $('body').on("click", ".confirmOrderBtnTableList", function(e) {
            $(this).parents('.new-order-actions').addClass('popup-order-processing');
            window.location = this.href;
            return false;
        });

        $('body').on("click", ".accpetOrderBtnTableList", function(e) {
            
            let elem = $(this);
            $(this).parents('.new-order-actions').addClass('popup-order-processing');
            <?php if($autoPrinting): ?>
                console.log("autoPrinting is enabled...")
                let printType = null;
                let order_id = $(this).data("id");
                let token = $('.csrfToken').val();

                $.ajax({
                    url: '<?php echo e(route('thermalprinter.getOrderData')); ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {order_id: order_id, _token: token, print_type: printType },
                })
                .done(function(response) {
                    let content = {};
                    content.type = 'print-receipt';
                    content.data = response;
                    let sendData = JSON.stringify(content);
                        if (socket != null && socket.readyState == 1) {
                            socket.send(sendData);
                            console.log("print command sent...")
                            $.jGrowl("", {
                                position: 'bottom-center',
                                header: '<?php echo e(__('thermalPrinterLang.printCommandSentMessage')); ?>',
                                theme: 'bg-success',
                                life: '3000'
                            });
                            setTimeout(function() {
                                acceptOrderTableList(elem);
                            }, 2500);
                        } else {
                            initializeSocket();
                            setTimeout(function() {
                                socket.send(sendData);
                                console.log("print command sent...")
                                $.jGrowl("", {
                                    position: 'bottom-center',
                                    header: '<?php echo e(__('storeDashboard.printCommandSentMessage')); ?>',
                                    theme: 'bg-success',
                                    life: '5000'
                                });
                            }, 700);
                            setTimeout(function() {
                                acceptOrderTableList(elem);
                            }, 2500);
                        }
                    })
                    .fail(function() {
                        console.log("Print Error.")
                        acceptOrderTableList(elem);
                    })
                <?php else: ?>
                    console.log("autoPrinting is disabled...")
                    acceptOrderTableList(elem);
                <?php endif; ?>
        });

        acceptOrderTableList = function(elem) {
            let id = elem.attr("data-id");
            let acceptOrderUrl = "<?php echo e(url('/store-owner/orders/accept-order')); ?>/" +id;
            $.ajax({
                url: acceptOrderUrl,
                type: 'GET',
                dataType: 'JSON',
            })
            .done(function(data) {
                $.jGrowl("<?php echo e(__('storeDashboard.orderAcceptedNotification')); ?>", {
                    position: 'bottom-center',
                    header: '<?php echo e(__('storeDashboard.successNotification')); ?>',
                    theme: 'bg-success',
                    life: '5000'
                });
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            })
            .fail(function() {
                console.log("error")
                $.jGrowl("<?php echo e(__('storeDashboard.orderSomethingWentWrongNotification')); ?>", {
                    position: 'bottom-center',
                    header: '<?php echo e(__('storeDashboard.woopssNotification')); ?>',
                    theme: 'bg-warning',
                    life: '5000'
                });
            })
        }
    
        //on Single click donot cancel order table list
        $('body').on("click", ".cancelOrderBtnTableList", function(e) {
            return false;
        });
    
        $('body').on("click", ".cancelOrderBtnTableList", function(e) {
            e.preventDefault()
            if (((new Date().getTime()) - touchtime) < 500) {
                $(this).parents('.new-order-actions').addClass('popup-order-processing');
                window.location = this.href;
                return false;
            }
            touchtime = new Date().getTime();
        });
    
        //on Single click donot cancel order popup
        $('body').on("click", ".cancelOrderBtnPopup", function(e) {
            return false;
        });
        
        $('.actionAfterAccept').click(function(event) {
          $(this).parents('.accepted-order-actions').addClass('popup-order-processing');
        });
        
    
        $('body').on("click", ".cancelOrderBtnPopup", function(e) {
            e.preventDefault()
    
            if (((new Date().getTime()) - touchtime) < 500) {
    
                let context = $(this).parents('.popup-order');
                context.addClass('popup-order-processing').prepend('<div class="d-flex pt-2 pr-2 float-right"><i class="icon-spinner10 spinner"></i></div>')
                console.log("HERE", context);
                
                let id = $(this).attr("data-id");
                let cancelOrderURL = "<?php echo e(url('/store-owner/orders/cancel-order')); ?>/" +id;
                $.ajax({
                    url: cancelOrderURL,
                    type: 'GET',
                    dataType: 'JSON',
                })
                .done(function(data) {
                    $(context).remove();
                    //count number of order on popup, if 0 then remove popup
                    if ($('.popup-order').length == 0) {
                        $('#newOrderModal').modal('hide');
                    }
                    $.jGrowl("<?php echo e(__('storeDashboard.orderCanceledNotification')); ?>", {
                        position: 'bottom-center',
                        header: '<?php echo e(__('storeDashboard.successNotification')); ?>',
                        theme: 'bg-success',
                        life: '5000'
                    });
                })
                .fail(function() {
                    console.log("error");
                    $.jGrowl("<?php echo e(__('storeDashboard.orderSomethingWentWrongNotification')); ?>", {
                        position: 'bottom-center',
                        header: '<?php echo e(__('storeDashboard.woopssNotification')); ?>',
                        theme: 'bg-warning',
                        life: '5000'
                    });
                })
            }
            touchtime = new Date().getTime();
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/dashboard.blade.php ENDPATH**/ ?>