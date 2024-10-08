<?php if(!empty($restaurants)): ?>
    <option value="">Please select venue</option>
    <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($restaurant->id); ?>"><?php echo e($restaurant->name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/restaurantname.blade.php ENDPATH**/ ?>