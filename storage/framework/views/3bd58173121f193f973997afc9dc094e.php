
<?php $__env->startSection("title"); ?>
OTP Verification
<?php $__env->stopSection(); ?>
<?php $__env->startSection("content"); ?>
<form class="registration-form py-5" action="<?php echo e(route('post.verifyotp')); ?>" method="POST" id="otpForm"
    style="margin: 0 auto 20px auto;">
    <div class="card mb-0">
        <div class="card-body" style="width:372px;">
            <div class="text-center mb-3">
                <i class="icon-key icon-2x text-slate-300 border-slate-300 border-3 rounded-round p-3 mb-3 mt-1"></i>
                <h5 class="mb0">OTP Verification</h5>
                <span class="d-block text-muted">Enter the OTP sent to your email</span>
            </div>
            <div class="form-group form-group-feedback form-group-feedback-left">
                <input type="hidden" name="email" value="<?php echo e($email); ?>">
                <input type="text" class="form-control" placeholder="OTP" name="otp" required>
                <div class="form-control-feedback">
                    <i class="icon-key text-muted"></i>
                </div>
            </div>
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block" style="height: 2.8rem; font-size: 1rem;">Verify OTP
                    <i class="icon-circle-right2 ml-2"></i></button>
            </div>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("admin.layouts.master", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/auth/otp.blade.php ENDPATH**/ ?>