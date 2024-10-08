
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.ovTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .content-wrapper {
        overflow: hidden;
    }

    .bill-calc-table tr td {
        padding: 6px 80px;
    }

    @media (min-width: 320px) and (max-width: 767px) {
        .bill-calc-table tr td {
            padding: 6px 50px;
        }
    }

    .td-title {
        padding-left: 15px !important;
    }

    .td-data {
        padding-right: 15px !important;
    }
</style>
<div class="content">
    <div class="row">
        <div class="col-lg-8 mt-4">
            <?php if(\Nwidart\Modules\Facades\Module::find('ThermalPrinter') &&
            \Nwidart\Modules\Facades\Module::find('ThermalPrinter')->isEnabled()): ?>
            <button type="button" class="btn btn-sm btn-secondary my-2 ml-2 thermalPrintButton" disabled="disabled"
                title="<?php echo e(__('thermalPrinterLang.connectingToPrinterMessage')); ?>"
                style="color: #fff; float: right; border-radius: 8px" data-type="kot"><i
                    class="icon-printer4 mr-1 thermalPrinterIcon"></i>
                <?php echo e(__('thermalPrinterLang.printKotWithThermalPrinter')); ?></button>
            <button type="button" class="btn btn-sm btn-secondary my-2 ml-2 thermalPrintButton" disabled="disabled"
                title="<?php echo e(__('thermalPrinterLang.connectingToPrinterMessage')); ?>"
                style="color: #fff; float: right; border-radius: 8px" data-type="invoice"><i
                    class="icon-printer4 mr-1 thermalPrinterIcon"></i>
                <?php echo e(__('thermalPrinterLang.printInvoiceWithThermalPrinter')); ?></button>
            <input type="hidden" id="thermalPrinterCsrf" value="<?php echo e(csrf_token()); ?>">
            <script>
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
                            $('.thermalPrintButton').attr({
                                disabled: 'disabled',
                                title: '<?php echo e(__('thermalPrinterLang.connectingToPrinterFailedMessage')); ?>'
                            });
                        }
                        if (socket != null && socket.readyState == 1) {
                             $('.thermalPrintButton').removeAttr('disabled').removeAttr('title');
                        }
                        clearInterval(checkSocketConnecton);
                    }, 500)
                };
                
                initializeSocket();
                
                $('.thermalPrintButton').click(function(event) {
                    $('.thermalPrinterIcon').removeClass('icon-printer').addClass('icon-spinner10 spinner');
                    let printButton = $('.thermalPrintButton');
                    printButton.attr('disabled', 'disabled');
                    let printType = $(this).data("type");
                
                    let order_id = '<?php echo e($order->id); ?>';
                    let token = $('#thermalPrinterCsrf').val();
                
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
                                $.jGrowl("", {
                                    position: 'bottom-center',
                                    header: '<?php echo e(__('thermalPrinterLang.printCommandSentMessage')); ?>',
                                    theme: 'bg-success',
                                    life: '3000'
                                });
                                setTimeout(function() {
                                    $('.thermalPrinterIcon').removeClass('icon-spinner10 spinner').addClass('icon-printer');
                                    printButton.removeAttr('disabled');
                                }, 1000);
                            } else {
                                initializeSocket();
                                setTimeout(function() {
                                    socket.send(sendData);
                                    $.jGrowl("", {
                                        position: 'bottom-center',
                                        header: '<?php echo e(__('storeDashboard.printCommandSentMessage')); ?>',
                                        theme: 'bg-success',
                                        life: '5000'
                                    });
                                }, 700);
                            }
                    })
                    .fail(function() {
                        alert("ERROR")
                    })
                });
            </script>
            <?php endif; ?>
            <a href="javascript:void(0)" id="printButton" class="btn btn-sm btn-primary mt-2"
                style="color: #fff; border: 1px solid #ccc; float: right; border-radius: 8px"><i
                    class="icon-printer mr-1"></i><?php echo e(__('storeDashboard.ovPrint')); ?></a>
            <div class="clearfix"></div>
            <div class="sidebar-category mt-1"
                style="box-shadow: 0 1px 6px 1px rgba(0, 0, 0, 0.05);background-color: #fff;">
                <div class="category-content" id="printThis">
                    <div href="#" class="btn btn-block content-group"
                        style="text-align: left; background-color: #8360c3; color: #fff; border-radius: 8px 8px 0 0;">
                        <strong style="font-size: 1.2rem;"><?php echo e($order->unique_order_id); ?></strong>
                    </div>
                    <div class="p-3">
                        <div class="d-flex justify-content-between">
                            <div class="form-group mb-0">
                                <h3><strong><?php echo e($order->restaurant->name); ?></strong></h3>
                            </div>
                            <div class="form-group mb-0">
                                <label
                                    class="control-label no-margin text-semibold mr-1"><strong><?php echo e(__('storeDashboard.ovOrderPlaced')); ?>:</strong></label>
                                <?php echo e($order->created_at->format('Y-m-d  - h:i A')); ?>

                            </div>
                        </div>
                        <hr>
                        <div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group">
                                        <label class="control-label no-margin text-semibold mr-1">
                                            <strong>
                                                <h5 class="font-weight-bold"><?php echo e(__('storeDashboard.ovCustomerDetails')); ?>

                                                </h5>
                                            </strong>
                                        </label>
                                        <br>
                                        <p><b><?php echo e(__('storeDashboard.ovName')); ?>: </b> <?php echo e($order->user->name); ?></p>
                                        <?php if(config('setting.hideCustomerDetailsFromStoreOwner') == "false"): ?>
                                        <p><b><?php echo e(__('storeDashboard.ovEmail')); ?>: </b> <?php echo e($order->user->email); ?></p>
                                        <p><b><?php echo e(__('storeDashboard.ovContactNumber')); ?>: </b> <?php echo e($order->user->phone); ?>

                                        </p>
                                        <?php if($order->delivery_type == 1): ?>
                                        <p><b><?php echo e(__('storeDashboard.ovAddress')); ?>: </b> <?php echo e($order->address); ?></p>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php if($order->user->tax_number != NULL): ?>
                                        <p><b><?php echo e(__('storeDashboard.ovTaxNumber')); ?>: </b>
                                            <?php echo e(strtoupper($order->user->tax_number)); ?></p>
                                        <?php endif; ?>
                                        <?php if($order->order_comment != NULL): ?>
                                        <p class="mb-0"><b><?php echo e(__('storeDashboard.ovCommentSuggestion')); ?>:</b></p>
                                        <h4 class="text-danger"><b><?php echo e($order->order_comment); ?></b></h4>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <div class="form-group mb-1">
                                        <div class="d-flex justify-content-center align-items-center flex-column mb-1"
                                            style="border: 1px solid #ddd;">
                                            <div class="py-1" style="font-weight: 900;">
                                                <?php echo e(__('storeDashboard.ovStatusText')); ?></div>
                                            <hr style="width: 100%;" class="m-0">
                                            <div class="py-1 text-success <?php if($order->orderstatus_id == 6): ?> text-danger <?php endif; ?>"
                                                style="font-weight: 500;">
                                                <?php if($order->orderstatus_id == 1): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus1')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 2): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus2')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 3): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus3')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 4): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus4')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 5): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus5')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 6): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus6')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 7): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus7')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 10): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus10')); ?> <?php endif; ?>
                                                <?php if($order->orderstatus_id == 11): ?>
                                                <?php echo e(__('storeDashboard.opOrderStatus11')); ?> <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-1 mt-2">
                                        <div class="d-flex">
                                            <div class="col p-0 mr-2">
                                                <div class="d-flex justify-content-center align-items-center flex-column mb-1"
                                                    style="border: 1px solid #ddd;">
                                                    <div class="py-1" style="font-weight: 900;">
                                                        <?php echo e(__('storeDashboard.ovOrderType')); ?></div>
                                                    <hr style="width: 100%;" class="m-0">
                                                    <div class="py-1 text-warning" style="font-weight: 500;">
                                                        <?php if($order->delivery_type == 1): ?>
                                                        <?php echo e(__('storeDashboard.ovOrderTypeDelivery')); ?>

                                                        <?php else: ?>
                                                        <?php echo e(__('storeDashboard.ovOrderTypeSelfPickup')); ?>

                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col p-0">
                                                <div class="d-flex justify-content-center align-items-center flex-column mb-1"
                                                    style="border: 1px solid #ddd;">
                                                    <div class="py-1" style="font-weight: 900;">
                                                        <?php echo e(__('storeDashboard.ovPaymentMode')); ?></div>
                                                    <hr style="width: 100%;" class="m-0">
                                                    <div class="py-1 text-warning" style="font-weight: 500;">
                                                        <?php echo e($order->payment_mode); ?>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        $subTotal = 0;
                        function calculateAddonTotal($addons) {
                        $total = 0;
                        foreach ($addons as $addon) {
                        $total += $addon->addon_price;
                        }
                        return $total;
                        }
                        ?>
                        <div class="text-right">
                            <div class="form-group">
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md-12 p-2 mb-3"
                                        style="background-color: #f7f8fb; float: right; text-align: left;">
                                        <?php $__currentLoopData = $order->orderitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div>
                                            <div class="d-flex mb-1 align-items-start" style="font-size: 1.2rem;">
                                                <span
                                                    class="badge badge-flat border-grey-800 text-default mr-2">x<?php echo e($item->quantity); ?></span>
                                                <strong class="mr-2" style="width: 100%;"><?php echo e($item->name); ?></strong>
                                                <?php
                                                $itemTotal = ($item->price
                                                +calculateAddonTotal($item->order_item_addons)) * $item->quantity;
                                                $subTotal = $subTotal + $itemTotal;
                                                ?>
                                                <span
                                                    class="badge badge-flat border-grey-800 text-default"><?php echo e(config('setting.currencyFormat')); ?><?php echo e($itemTotal); ?></span>
                                            </div>
                                            <?php if(count($item->order_item_addons)): ?>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th><?php echo e(__('storeDashboard.ovTableCategory')); ?></th>
                                                            <th><?php echo e(__('storeDashboard.ovTableAddon')); ?></th>
                                                            <th><?php echo e(__('storeDashboard.ovTablePrice')); ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $__currentLoopData = $item->order_item_addons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <tr>
                                                            <td><?php echo e($addon->addon_category_name); ?></td>
                                                            <td><?php echo e($addon->addon_name); ?></td>
                                                            <td><?php echo e(config('setting.currencyFormat')); ?><?php echo e($addon->addon_price); ?>

                                                            </td>
                                                        </tr>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php endif; ?>
                                            <?php if(!$loop->last): ?>
                                            <div class="mb-2" style="border-bottom: 2px solid #c9c9c9;"></div>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="float-right">
                            <table class="table table-bordered table-striped bill-calc-table">
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovSubTotal')); ?></td>
                                    <td class="text-right td-data">
                                        <?php echo e(config('setting.currencyFormat')); ?><?php echo e($subTotal); ?></td>
                                </tr>
                                <?php if($order->coupon_name != NULL): ?>
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovCoupon')); ?></td>
                                    <td class="text-right td-data"> <?php echo e($order->coupon_name); ?> <?php if($order->coupon_amount
                                        != NULL): ?> (<?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->coupon_amount); ?>)
                                        <?php endif; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($order->restaurant_charge != NULL): ?>
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovStoreCharge')); ?></td>
                                    <td class="text-right td-data">
                                        <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->restaurant_charge); ?> </td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovDeliveryCharge')); ?></td>
                                    <td class="text-right td-data">
                                        <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->delivery_charge); ?> </td>
                                </tr>
                                <?php if($order->tax != NULL): ?>
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovTax')); ?></td>
                                    <td class="text-right td-data"><?php echo e($order->tax); ?>% <?php if($order->tax_amount != NULL): ?>
                                        (<?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->tax_amount); ?>) <?php endif; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($order->wallet_amount != NULL): ?>
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovPaidWithWallet')); ?>

                                        <?php echo e(config('setting.walletName')); ?></td>
                                    <td class="text-right td-data">
                                        <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->wallet_amount); ?> </td>
                                </tr>
                                <?php endif; ?>
                                <?php
                                if(!is_null($order->tip_amount)) {
                                $total = $order->total - $order->tip_amount;
                                } else {
                                $total = $order->total;
                                }
                                ?>
                                <tr>
                                    <td class="text-left td-title"><b><?php echo e(__('storeDashboard.ovTotal')); ?></b></td>
                                    <td class="text-right td-data"> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($total); ?>

                                    </td>
                                </tr>
                                <?php
                                if(!is_null($order->tip_amount)) {
                                $payable = $order->payable - $order->tip_amount;
                                } else {
                                $payable = $order->payable;
                                }
                                ?>
                                <?php if($order->payable != NULL): ?>
                                <tr>
                                    <td class="text-left td-title"><?php echo e(__('storeDashboard.ovPayable')); ?></td>
                                    <td class="text-right td-data"><b>
                                            <?php echo e(config('setting.currencyFormat')); ?><?php echo e($payable); ?></b></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-5">
            <div style="margin-top: 5.2rem"></div>
            <?php if($order->schedule_slot != null): ?>
            <div class="card">
                <div class="card-body">
                    <p class="text-center mb-0">
                        <b>
                            <?php echo e(__('orderScheduleLang.orderScheduleTitleText')); ?>

                        </b>
                        <br>
                        <b><?php echo e(__('orderScheduleLang.orderScheduleDateText')); ?></b>
                        <?php echo e(json_decode($order->schedule_date)->day); ?>, <?php echo e(json_decode($order->schedule_date)->date); ?>

                        <br>
                        <b><?php echo e(__('orderScheduleLang.orderScheduleSlotText')); ?>:</b>
                        <?php echo e(json_decode($order->schedule_slot)->open); ?> - <?php echo e(json_decode($order->schedule_slot)->close); ?>

                    </p>
                </div>
            </div>
            <?php endif; ?>
            <?php if($order->orderstatus_id == 3 || $order->orderstatus_id == 4 ||$order->orderstatus_id == 5 ||
            $order->orderstatus_id == 6): ?>
            <?php if($order->accept_delivery && $order->accept_delivery->user && $order->accept_delivery->user->name): ?>
            <div class="card">
                <div class="card-body">
                    <p class="text-center mb-0"> <strong><?php echo e(__('storeDashboard.opDeliveryBy')); ?>:
                            <?php echo e($order->accept_delivery->user->name); ?></strong></p>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
            <?php if($order->rating): ?>
            <div class="card">
                <div class="card-body">
                    <p class="text-center mb-3"><b><?php echo e(__('storeDashboard.ratingsAndReviewsText')); ?></b></p>
                    <div>
                        <p> <b>Store Rating </b> <span
                                class="ml-1 badge badge-flat text-white <?php echo e(ratingColorClass($order->rating->rating_store)); ?>"><?php echo e($order->rating->rating_store); ?>

                                <i class="icon-star-full2 text-white" style="font-size: 0.6rem;"></i></span> </p>
                        <p><?php echo e($order->rating->review_store); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php if($order->orderstatus_id == 10): ?>
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center mb-3"> <strong> <?php echo e(__('storeDashboard.viewOrderOrderActions')); ?> </strong>
                    </h4>
                    <div class="d-flex justify-content-center">
                        <a href="<?php echo e(route('restaurant.confirmScheduledOrder', $order->id)); ?>"
                            class="mr-2 btn btn-lg confirmOrderBtn btn-success">
                            <?php echo e(__('orderScheduleLang.dashboardConfirmScheduledOrder')); ?> <i
                                class="icon-checkmark3 ml-1"></i></a>
                        <a href="<?php echo e(route('restaurant.cancelOrder', $order->id)); ?>"
                            class="btn btn-lg cancelOrderBtn btn-danger" data-popup="tooltip" data-placement="top"
                            title="<?php echo e(__('storeDashboard.ovTooltipCancel')); ?>"> <?php echo e(__('storeDashboard.ovCancel')); ?> <i
                                class="icon-cross ml-1"></i></a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if(($order->orderstatus_id == 1) || ($order->delivery_type == 2 && $order->orderstatus_id == 2) ||
            ($order->delivery_type == 2 && $order->orderstatus_id == 7)): ?>
            <div class="card">
                <div class="card-body">
                    <h4 class="text-center mb-3"> <strong> <?php echo e(__('storeDashboard.viewOrderOrderActions')); ?> </strong>
                    </h4>
                    <div class="d-flex justify-content-center">
                        <?php if($order->orderstatus_id == 1): ?>
                        <a href="<?php echo e(route('restaurant.acceptOrder', $order->id)); ?>"
                            class="mr-2 btn btn-lg acceptOrderBtn btn-primary"> <?php echo e(__('storeDashboard.ovAccept')); ?> <i
                                class="icon-checkmark3 ml-1"></i></a>
                        <a href="<?php echo e(route('restaurant.cancelOrder', $order->id)); ?>"
                            class="btn btn-lg cancelOrderBtn btn-danger" data-popup="tooltip" data-placement="top"
                            title="<?php echo e(__('storeDashboard.ovTooltipCancel')); ?>"> <?php echo e(__('storeDashboard.ovCancel')); ?> <i
                                class="icon-cross ml-1"></i></a>
                        <?php endif; ?>
                        <?php if($order->delivery_type == 2 && $order->orderstatus_id == 2): ?>
                        <a href="<?php echo e(route('restaurant.markOrderReady', $order->id)); ?>" class="btn btn-lg btn-warning">
                            <?php echo e(__('storeDashboard.ovMarkAsReady')); ?> <i class="icon-checkmark3 ml-1"></i></a>
                        <?php endif; ?>
                        <?php if($order->delivery_type == 2 && $order->orderstatus_id == 7): ?>
                        <a href="<?php echo e(route('restaurant.markSelfPickupOrderAsCompleted', $order->id)); ?>"
                            class="btn btn-lg btn-success"> <?php echo e(__('storeDashboard.ovMarkAsCompleted')); ?> <i
                                class="icon-checkmark3 ml-1"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    $('#printButton').on('click',function(){
        $('#printThis').printThis();
    })
    //on single click, accpet order and disable button
    $('body').on("click", ".acceptOrderBtn", function(e) {
        $(this).addClass('pointer-none');
    });

    $('body').on("click", ".confirmOrderBtn", function(e) {
        $(this).addClass('pointer-none');
    });
    
    //on Single click donot cancel order
    $('body').on("click", ".cancelOrderBtn", function(e) {
        return false;
    });
    
    //cancel order on double click
    $('body').on("dblclick", ".cancelOrderBtn", function(e) {
        $(this).addClass('pointer-none');
        window.location = this.href;
        return false;
     });
    
    if (window.location !== window.parent.location ) 
    { 
        //hide menu, nav, header and apply custom css colors when on iFrame from dashboard v2
        $('.navbar-dark').hide();
        $('.navbar-sticky').hide();
        $("body").overlayScrollbars({
            scrollbars : {
                visibility       : "auto",
                autoHide         : "leave",
                autoHideDelay    : 500
            }
        });
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/viewOrder.blade.php ENDPATH**/ ?>