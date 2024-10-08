<?php if(!empty($tables_info->isNotEmpty())): ?>
    <?php $__currentLoopData = $tables_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($table->bookings->isEmpty()): ?>
            <div class="form-check">
                <input class="form-check-input selectable_table_input" type="checkbox" name="selected_table[]" value="<?php echo e($table->id); ?>" data-seats="<?php echo e($table->total_seats); ?>" data-no_of_persons="<?php echo e($selected_booking->no_of_seats); ?>">
                <label class="form-check-label" for="flexRadioDefault1">
                     <?php echo e($table->table_number); ?> (Seats= <?php echo e($table->total_seats); ?>)
                </label>
            </div>
        <?php elseif($table->bookings->isNotEmpty() && strtotime(date('H:i', strtotime($selected_booking->booking_datetime))) != strtotime(date('H:i', strtotime($table->bookings[0]->booking_datetime)))): ?>
            <div class="form-check">
                <input class="form-check-input selectable_table_input" type="checkbox" name="selected_table[]" value="<?php echo e($table->id); ?>" data-seats="<?php echo e($table->total_seats); ?>" data-no_of_persons="<?php echo e($selected_booking->no_of_seats); ?>">
                <label class="form-check-label" for="flexRadioDefault1">
                     <?php echo e($table->table_number); ?> (Seats= <?php echo e($table->total_seats); ?>)
                </label>
            </div>
        <?php endif; ?> 
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php else: ?>
    <span style="color:red;">No tables found!</span>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/getAvailableTables.blade.php ENDPATH**/ ?>