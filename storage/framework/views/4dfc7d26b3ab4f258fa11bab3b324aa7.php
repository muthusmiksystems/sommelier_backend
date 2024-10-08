<?php if(!empty($time_buttons)): ?>
    <option value="all">All Time Slot</option>
    <?php $__currentLoopData = $time_buttons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking_time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($booking_time); ?>" <?php echo e((isset($_POST['time_slot']) && $booking_time == $_POST['time_slot']) ? "selected='selected'" : ""); ?>><?php echo e($booking_time); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/bookingTimeForFilter.blade.php ENDPATH**/ ?>