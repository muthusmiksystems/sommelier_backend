<?php if(!empty($areas)): ?>
    <option value="">Please select table location</option>
    <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($area->id); ?>"><?php echo e($area->area_name); ?></option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/tableAreasLocations.blade.php ENDPATH**/ ?>