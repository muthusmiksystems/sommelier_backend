
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.epTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content">
    <?php if(!empty($restaurants)): ?>
    <div class="row">
        <div class="form-group row mt-5">
            <label class="col-lg-12 col-form-label"><span
                    class="text-danger">*</span><?php echo e(__('storeDashboard.epSelectStore')); ?>:</label>
            <div class="col-lg-12">
                <select class="form-control select-search select" name="restaurant_id" required id="dynamic_select"
                    style="height: 2.5rem;">
                    <option value=""><?php echo e(__('storeDashboard.epSelect')); ?> </option>
                    <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e(route('restaurant.earnings')); ?>/<?php echo e($restaurant->id); ?>" class="text-capitalize">
                        <?php echo e($restaurant->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
    </div>
    <script>
        $(function(){
            $('.select').select2(); 
          // bind change event to select
          $('#dynamic_select').on('change', function () {
              var url = $(this).val(); // get selected value
              if (url) { // require a URL
                  window.location = url; // redirect
              }
              return false;
          });
        });
    </script>
    <?php endif; ?>
    <?php if(!Request::is('store-owner/earnings')): ?>
    <div class="row mt-4">
        <div class="col-12 col-xl-4 mb-2">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-right" href="javascript:void(0)">
                    <div class="block-content block-content-full clearfix">
                        <div class="text-center" style="color: #717171; font-weight: 500;">
                            <?php echo e(__('storeDashboard.epNetEarningsBeforeCommission')); ?></div>
                        <div class="dashboard-display-number text-center">
                            <?php echo e(config('setting.currencyFormat')); ?><?php echo e($balanceBeforeCommission); ?></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-2">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-right" href="javascript:void(0)">
                    <div class="block-content block-content-full clearfix">
                        <div class="text-center" style="color: #717171; font-weight: 500;">
                            <?php echo e(__('storeDashboard.epYourBalanceAfterCommission')); ?>

                            <strong><?php echo e($restaurant->commission_rate); ?>%)</strong></div>
                        <div class="dashboard-display-number text-center">
                            <?php echo e(config('setting.currencyFormat')); ?><?php echo e($balanceAfterCommission); ?></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12 col-xl-4 mb-2">
            <div class="col-xl-12 dashboard-display p-3">
                <a class="block block-link-shadow text-right" href="javascript:void(0)">
                    <div class="block-content block-content-full clearfix">
                        <div class="text-center" style="color: #717171; font-weight: 500;">
                            <?php echo e(__('storeDashboard.epTotalValueBeforeCommission')); ?></div>
                        <div class="dashboard-display-number text-center">
                            <?php echo e(config('setting.currencyFormat')); ?><?php echo e($totalEarning); ?></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row mt-4 d-none d-md-block">
        <div class="col-xl-12">
            <div class="panel panel-flat">
                <div class="panel-body">
                    <div class="chart-container hidden">
                        <div class="chart has-fixed-height has-minimum-width" id="basic_area"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-5">
            <div class="col-xl-12 p-3"
                style="border-radius: 4px; background-color: #fff; box-shadow: 0 1px 6px 1px rgba(0, 0, 0, 0.05);">
                <h4>
                    <strong><?php echo e(__('storeDashboard.epRequestPayout')); ?></strong>
                </h4>

                <?php if(!((double)$balanceAfterCommission > (double)config('setting.minPayout'))): ?>
                <p>
                    <?php echo e(__('storeDashboard.epYourCurrentBalance')); ?>

                    <strong><?php echo e(config('setting.currencyFormat')); ?><?php echo e($balanceAfterCommission); ?></strong>.
                    <?php echo e(__('storeDashboard.epReqPayoutMessage')); ?><strong>
                        <?php echo e(config('setting.currencyFormat')); ?><?php echo e(config('setting.minPayout')); ?></strong>.
                </p>
                <i class="icon-exclamation"
                    style="position: absolute; font-size: 5rem; color: #FF5722; right: 15px; top: 15px; opacity: 0.1;"></i>
                <?php else: ?>
                <button class="btn btn-primary btn-lg" data-toggle="modal"
                    data-target="#sendPayoutRequest"><?php echo e(__('storeDashboard.epRequestPayout')); ?></button>
                <i class="icon-piggy-bank"
                    style="position: absolute; font-size: 5rem; color: #FF5722; right: 15px; top: 15px; opacity: 0.1;"></i>

                <div id="sendPayoutRequest" class="modal fade mt-5" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title"><span
                                        class="font-weight-bold"><?php echo e(__('storeDashboard.epRequestPayout')); ?></span></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <span class="help-text">
                                    <?php echo e(__('storeDashboard.epYourRequest')); ?>

                                    <strong><?php echo e(config('setting.currencyFormat')); ?><?php echo e($balanceAfterCommission); ?></strong>
                                </span>
                                <div class="modal-footer mt-4">
                                    <form method="POST" action="<?php echo e(route('restaurant.sendPayoutRequest')); ?>">
                                        <input type="hidden" name="restaurant_id" value=<?php echo e($restaurant->id); ?>>
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="btn btn-primary"><?php echo e(__('storeDashboard.epSendRequest')); ?></button>
                                        <button type="button" class="btn btn-danger"
                                            data-dismiss="modal"><?php echo e(__('storeDashboard.epCancelReq')); ?></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if(!empty($payoutRequests)): ?>
    <div class="container">
        <div class="row mt-5 mb-5"
            style="border-radius: 4px; background-color: #fff; box-shadow: 0 1px 6px 1px rgba(0, 0, 0, 0.05);">
            <div class="col-xl-12">
                <h4 class="p-3">
                    <strong>
                        <?php echo e(__('storeDashboard.epRequestPayouts')); ?>

                    </strong>
                </h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableAmount')); ?>

                                </th>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableStatus')); ?>

                                </th>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableTransactionMode')); ?> </th>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableTransactionID')); ?>

                                </th>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableMessage')); ?>

                                </th>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableCreated')); ?>

                                </th>
                                <th>
                                    <?php echo e(__('storeDashboard.epTableUpdated')); ?>

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $payoutRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payoutRequest): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($payoutRequest->amount); ?></td>
                                <td><span
                                        class="badge badge-flat border-grey-800 text-default text-capitalize"><?php echo e($payoutRequest->status); ?></span>
                                </td>
                                <td>
                                    <?php if($payoutRequest->transaction_mode != NULL): ?>
                                    <?php echo e($payoutRequest->transaction_mode); ?>

                                    <?php else: ?>
                                    ----
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($payoutRequest->transaction_id != NULL): ?>
                                    <?php echo e($payoutRequest->transaction_id); ?>

                                    <?php else: ?>
                                    ----
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($payoutRequest->message != NULL): ?>
                                    <?php echo e($payoutRequest->message); ?>

                                    <?php else: ?>
                                    ----
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($payoutRequest->created_at->format('Y-m-d  - h:i A')); ?></td>
                                <td>
                                    <?php if($payoutRequest->updated_at != NULL): ?>
                                    <?php echo e($payoutRequest->updated_at->format('Y-m-d  - h:i A')); ?>

                                    <?php else: ?>
                                    ----
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        $(function () {
        
            require.config({
                paths: {
                    echarts: '<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/backend/global_assets/js/plugins/visualization/echarts'
                }
            });
        
            require(
                [
                    'echarts',
                    'echarts/theme/limitless',
                    'echarts/chart/bar',
                    'echarts/chart/line'
                ],
        
                function (ec, limitless) {
        
                    var basic_area = ec.init(document.getElementById('basic_area'), limitless);
                  
                    basic_area_options = {
                        
                        // Setup grid
                        grid: {
                            x: 40,
                            x2: 20,
                            y: 35,
                            y2: 25
                        },
        
                        // Add tooltip
                        tooltip: {
                            trigger: 'axis'
                        },
        
                        
                        calculable: false,
        
        
                            // Horizontal axis
                            xAxis: [{
                                type: 'category',
                                boundaryGap: false,
                                data: <?php echo $monthlyDate; ?>,
                            }],
        
                            // Vertical axis
                            yAxis: [{
                                name: "<?php echo e(__('storeDashboard.epChartEarningsIn')); ?> <?php echo e(config('setting.currencyFormat')); ?>",
                                nameLocation: "end",
                                type: 'value'
                            }],
        
                            // Add series
                            series: [
                                {
                                    name: "<?php echo e(__('storeDashboard.epChartSalesIn')); ?> <?php echo e(config('setting.currencyFormat')); ?>",
                                    type: 'line',
                                    smooth: true,
                                    itemStyle: {normal: {areaStyle: {type: 'default'}}},
                                    data: <?php echo $monthlyEarning; ?>,
                                    itemStyle: {
                                        normal: {
                                            label: {
                                                show: true,
                                                textStyle: {
                                                    fontWeight: 500,
                                                }
                                            }
                                        }
                                    },
                                },
                            ]
                        };
                    basic_area.setOption(basic_area_options);
        
                    window.onresize = function () {
                        setTimeout(function (){
                            basic_area.resize();
                        }, 200);
                    }
                }
            );
        });
    </script>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/earnings.blade.php ENDPATH**/ ?>