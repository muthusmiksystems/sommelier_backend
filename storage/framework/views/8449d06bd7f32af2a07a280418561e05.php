
<?php $__env->startSection("title"); ?> Store Payouts - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2">Total</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($count); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
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
                            <th>
                                Store
                            </th>
                            <th>
                                Amount
                            </th>
                            <th>
                                Status
                            </th>
                            <th>
                                Transaction Mode
                            </th>
                            <th>
                                Transaction ID
                            </th>
                            <th>
                                Message
                            </th>
                            <th>
                                Created At
                            </th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $restaurantPayouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurantPayout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($restaurantPayout->restaurant->name); ?></td>
                            <td><?php echo e($restaurantPayout->amount); ?></td>
                            <td><span
                                    class="badge badge-flat border-grey-800 text-default text-capitalize"><?php echo e($restaurantPayout->status); ?></span>
                            </td>
                            <td>
                                <?php if($restaurantPayout->transaction_mode != NULL): ?>
                                <?php echo e($restaurantPayout->transaction_mode); ?>

                                <?php else: ?>
                                ----
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($restaurantPayout->transaction_id != NULL): ?>
                                <?php echo e($restaurantPayout->transaction_id); ?>

                                <?php else: ?>
                                ----
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($restaurantPayout->message != NULL): ?>
                                <?php echo e($restaurantPayout->message); ?>

                                <?php else: ?>
                                ----
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($restaurantPayout->created_at->format('Y-m-d  - h:i A')); ?></td>
                            <td class="text-center">
                                <a href="<?php echo e(route('admin.viewRestaurantPayout', $restaurantPayout->id)); ?>"
                                    class="btn btn-sm btn-primary"> View</a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($restaurantPayouts->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/restaurantPayouts.blade.php ENDPATH**/ ?>