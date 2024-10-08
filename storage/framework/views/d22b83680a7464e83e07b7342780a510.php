
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.ovTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>

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

<div class="content">
    <br />
    <div class="col-md-6">
        <a href="javascript:void(0)" id="printButton">
            <input type='button' class="btn btn-lg btn-danger" id='btn' value='PRINT NOW'>
            <div class="clearfix"></div>
        </a>
    </div><br />

    <div id="printOrderBlock">
        <div class="d-flex justify-content-around">
            <h4><b><?php echo e($order->unique_order_id); ?></b></h4>
        </div>
        <div class="d-flex justify-content-around">
            <small> <?php echo e($order->created_at->format('Y-m-d  - h:i A')); ?> </small>
        </div>
        <div class="d-flex justify-content-around">
            <b><?php echo e($order->restaurant->name); ?></b>
        </div>

        <div class="d-flex justify-content-start mt-2">
            <div>
                <b><?php echo e(__('storeDashboard.ovCustomerDetails')); ?>:</b><br />
                <b><?php echo e(__('storeDashboard.ovName')); ?>: </b> <?php echo e($order->user->name); ?> <br />
                <b><?php echo e(__('storeDashboard.ovEmail')); ?>: </b> <?php echo e($order->user->email); ?> <br />
                <b><?php echo e(__('storeDashboard.ovContactNumber')); ?>: </b> <?php echo e($order->user->phone); ?> <br />
                <b><?php echo e(__('storeDashboard.ovAddress')); ?>: </b>
                <?php echo e($order->address); ?> <br /> <br />
            </div>
        </div>

        <div class="d-flex justify-content-start mt-2">
            <div class="col-md-12 p-2 mb-3" style="background-color: #f7f8fb; float: right; text-align: left;">
                <?php $__currentLoopData = $order->orderitems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div>
                    <div class="d-flex mb-1 align-items-start" style="font-size: 1.2rem;">
                        <span class="badge badge-flat border-grey-800 text-default mr-2"><?php echo e($item->quantity); ?>x</span>
                        <strong class="mr-1" style="width: 100%;"><?php echo e($item->name); ?></strong>
                        <?php
                        $itemTotal = ($item->price +calculateAddonTotal($item->order_item_addons)) * $item->quantity;
                        $subTotal = $subTotal + $itemTotal;
                        ?>
                        <span
                            class="badge badge-flat border-grey-800 text-default"><?php echo e(config('setting.currencyFormat')); ?><?php echo e($itemTotal); ?></span>
                    </div>
                    <?php if(count($item->order_item_addons)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Addon</th>
                                    <th>Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $item->order_item_addons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($addon->addon_category_name); ?></td>
                                    <td><?php echo e($addon->addon_name); ?></td>
                                    <td><?php echo e(config('setting.currencyFormat')); ?><?php echo e($addon->addon_price); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                    <?php if(!$loop->last): ?>
                    <div class="mb-2" style="border-bottom: 2px solid #dcdcdc;"></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-2">
            <table class="table table-bordered bill-calc-table">

                <tr>
                    <td class="text-left td-title">SubTotal</td>
                    <td class="td-data"> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($subTotal); ?></td>
                </tr>

                <?php if($order->coupon_name != NULL): ?>
                <tr>
                    <td class="text-left td-title">Coupon</td>
                    <td class="td-data"> <?php echo e($order->coupon_name); ?> <?php if($order->coupon_amount != NULL): ?>
                        (<?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->coupon_amount); ?>) <?php endif; ?> </td>
                </tr>
                <?php endif; ?>

                <?php if($order->restaurant_charge != NULL): ?>
                <tr>
                    <td class="text-left td-title">Store Charge</td>
                    <td class="td-data"> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->restaurant_charge); ?> </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td class="text-left td-title">Delivery Charge</td>
                    <td class="td-data"> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->delivery_charge); ?> </td>
                </tr>

                <?php if($order->tax != NULL): ?>
                <tr>
                    <td class="text-left td-title">Tax</td>
                    <td class="td-data"><?php echo e($order->tax); ?>% <?php if($order->tax_amount != NULL): ?>
                        (<?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->tax_amount); ?>) <?php endif; ?> </td>
                </tr>
                <?php endif; ?>

                <?php if(!$order->tip_amount == NULL): ?>
                <tr>
                    <td class="text-left td-title">Tip</td>
                    <td class="td-data"><?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->tip_amount); ?></td>
                </tr>
                <?php endif; ?>

                <?php if($order->wallet_amount != NULL): ?>
                <tr>
                    <td class="text-left td-title">Paid With <?php echo e(config('setting.walletName')); ?></td>
                    <td class="td-data"> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->wallet_amount); ?> </td>
                </tr>
                <?php endif; ?>

                <tr>
                    <td class="text-left td-title"><b>Total</b></td>
                    <td class="td-data"> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->total); ?> </td>
                </tr>

                <?php if($order->payable != NULL): ?>
                <tr>
                    <td class="text-left td-title">Payable</td>
                    <td class="td-data"><b> <?php echo e(config('setting.currencyFormat')); ?><?php echo e($order->payable); ?></b></td>
                </tr>
                <?php endif; ?>

            </table>
        </div>

    </div>
</div>

<script>
    $('#printButton').click(function(event) {
        $('#printOrderBlock').printThis({
            removeScripts: true,
            pageTitle: "&nbsp;", 
            importCSS: true,
            loadCSS: "<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/css/orderPrint.css"
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/printOrder.blade.php ENDPATH**/ ?>