
<?php $__env->startSection("title"); ?> Wallet Transactions - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <?php if(empty($query)): ?>
                <span class="font-weight-bold mr-2">Total</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($count); ?></span>
                <?php else: ?>
                <span class="font-weight-bold mr-2">Total</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($count); ?></span>
                <br>
                <span class="font-weight-bold mr-2">Showing results for "<?php echo e($query); ?>"</span>
                <?php endif; ?>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <form action="<?php echo e(route('admin.searchWalletTransactions')); ?>" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input"
                placeholder="Search with transaction id" name="query">
            <div class="form-control-feedback form-control-feedback-lg">
                <i class="icon-search4"></i>
            </div>
        </div>
        <?php echo csrf_field(); ?>
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                Transaction ID
                            </th>
                            <th>
                                Type
                            </th>
                            <th width="20%">
                                Amount
                            </th>
                            <th>
                                Description
                            </th>
                            <th>
                                Date
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php echo e($transaction->uuid); ?>

                            </td>
                            <td>
                                <?php if($transaction->type === "deposit"): ?>
                                <span
                                    class="badge badge-flat border-grey-800 text-success text-capitalize"><?php echo e($transaction->type); ?></span>
                                <?php else: ?>
                                <span
                                    class="badge badge-flat border-grey-800 text-danger text-capitalize"><?php echo e($transaction->type); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo e(config('setting.currencyFormat')); ?>

                                <?php echo e(number_format($transaction->amount / 100, 2,'.', '')); ?>

                            </td>
                            <td>
                                <?php echo e($transaction->meta["description"]); ?>

                            </td>
                            <td>
                                <?php echo e($transaction->created_at->diffForHumans()); ?>

                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($transactions->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/viewAllWalletTransactions.blade.php ENDPATH**/ ?>