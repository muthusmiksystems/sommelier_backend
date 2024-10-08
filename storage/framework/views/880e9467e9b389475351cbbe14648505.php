<?php if(Session::has('success')): ?>
<script>
    $(function () {
        $.jGrowl("<?php echo e(Session::get('success')); ?>", {
            position: 'bottom-center',
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
            header:  '<?php echo e(__('storeDashboard.successNotification')); ?>',
            <?php else: ?>
            header: 'SUCCESS 👌',
            <?php endif; ?>
            theme: 'bg-success',
        });    
    });
</script>
<?php endif; ?>
<?php if(Session::has('message')): ?>
<script>
    $(function () {
        $.jGrowl("<?php echo e(Session::get('message')); ?>", {
            position: 'bottom-center',
            <?php if(\Spatie\Permission\PermissionServiceProvider::bladeMethodWrapper('hasRole', "Store Owner")): ?>
            header:  '<?php echo e(__('storeDashboard.woopssNotification')); ?>',
            <?php else: ?>
            header: 'Wooopsss ⚠️',
            <?php endif; ?>
            theme: 'bg-warning',
        });    
    });
</script>
<?php endif; ?>
<?php if($errors->any()): ?>
<script>
    $(function () {
        $.jGrowl("<?php echo e(implode('', $errors->all(':message'))); ?>", {
            position: 'bottom-center',
            header: 'ERROR ⁉️',
            theme: 'bg-danger',
        });    
    });
</script>
<?php endif; ?>

<?php if(Session::get('razorpay_enter_mid') == "true"): ?>
<div id="razorpayMidPopup" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold">Razorpay Merchant ID Missing</span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('admin.saveSpecificSettings')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label><strong>Razorpay Merchant Id <i class="icon-question3 ml-1 text-muted"
                                        data-popup="tooltip"
                                        title="On the Top-Right corner of the Razorpay Dashboard, click the User Icon and click 'Copy Merchant Id'"
                                        data-placement="top"></i></strong></label>
                            <input type="text" class="form-control form-control-lg" name="razorpayMerchantId"
                                value="<?php echo e(config('setting.razorpayMerchantId')); ?>"
                                placeholder="Enter Razorpay Merchant ID here" minlength="14" maxlength="14">
                            <span class="small text-danger">Please make sure the Merchant ID is correctly set for
                                Razorpay to work properly.</span>
                        </div>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="text-right mt-5">
                        <button type="submit" class="btn btn-primary btn-lg">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        $('#razorpayMidPopup').modal({
            show: true,
        })  
    });
</script>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/includes/notification.blade.php ENDPATH**/ ?>