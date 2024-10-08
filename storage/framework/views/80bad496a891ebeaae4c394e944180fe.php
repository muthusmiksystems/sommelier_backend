<?php $__currentLoopData = $time_buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift => $booking_time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row">
        <div class="col-lg-12">
            <label class="col-form-label shift_label_text" style="text-align:left;"><?php echo e($shift); ?></label>
            <?php $__currentLoopData = $booking_time; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <span class="booking_time_btn_block">
                    <span class="booking_time_text <?php echo e($time['class']); ?>">
                        <input type="radio" name="booking_time" class="booking_time_input" value="<?php echo e($time['time']); ?>" style="display:none;" <?php echo e((isset($current_booking_time) && !empty($current_booking_time) && strtotime($time["time"]) == $current_booking_time) ? 'checked="checked"' : ''); ?>/>
                        <?php echo e($time['time']); ?>

                    </span>
                </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/bookingTimeButtons.blade.php ENDPATH**/ ?>