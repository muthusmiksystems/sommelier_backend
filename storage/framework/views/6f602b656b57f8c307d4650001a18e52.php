
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.ratingsPageTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="content">
<style>
    .text-truncate {
        width:250px;white-space:nowrap; overflow:hidden; text-overflow: ellipsis; 
    }
</style>
<?php if(!empty($restaurants)): ?>
<div class="row">
    <div class="form-group row mt-5">
        <label class="col-lg-12 col-form-label"><span class="text-danger">*</span><?php echo e(__('storeDashboard.epSelectStore')); ?>:</label>
        <div class="col-lg-12">
            <select class="form-control select-search select" name="restaurant_id" required id="dynamic_select" style="height: 2.5rem;">
                <option value=""><?php echo e(__('storeDashboard.epSelect')); ?> </option>
                <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e(route('restaurant.ratings')); ?>/<?php echo e($restaurant->id); ?>" class="text-capitalize"><?php echo e($restaurant->name); ?></option>
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
<?php if(!Request::is('store-owner/ratings')): ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.ratingsPageReviewsOfTitle')); ?> <a class="ml-1" href="<?php echo e(route('restaurant.get.editRestaurant', $restaurant->id)); ?>"><?php echo e($restaurant->name); ?></a></span> <span class="ml-1 badge badge-flat text-white <?php echo e(ratingColorClass($averageRating)); ?>"><?php echo e($averageRating); ?> <i class="icon-star-full2 text-white" style="font-size: 0.6rem;"></i></span>
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
                            <th><?php echo e(__('storeDashboard.ratingsPageOrder')); ?></th>
                            <th><?php echo e(__('storeDashboard.ratingsPageCustomer')); ?></th>
                            <th><?php echo e(__('storeDashboard.ratingsPageReview')); ?></th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><a href="<?php echo e(route('restaurant.viewOrder', $review->order->unique_order_id)); ?>"><?php echo e($review->order->unique_order_id); ?></a></td>
                            <td><?php echo e($review->user->name); ?></td>
                            <td>
                                <div class="d-flex">
                                    <div class="mr-1">
                                        <span class="ml-1 badge badge-flat text-white <?php echo e(ratingColorClass($review->rating_store)); ?>" data-popup="tooltip" title="Store Review" data-placement="top"><?php echo e($review->rating_store); ?> <i class="icon-star-full2 text-white" style="font-size: 0.6rem;"></i></span>
                                    </div>
                                    <div>
                                        <p class="text-truncate"><?php echo e($review->review_store); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary reviewViewButton" data-toggle="modal" data-target="#viewReviewModal" data-reviewId="<?php echo e($review->id); ?>" data-ratingDelivery="<?php echo e($review->rating_delivery); ?>" data-ratingStore="<?php echo e($review->rating_store); ?>" data-reviewDelivery="<?php echo e($review->review_delivery); ?>" data-reviewStore="<?php echo e($review->review_store); ?>"> <?php echo e(__('storeDashboard.ratingsPageViewBtn')); ?> </button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="viewReviewModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.ratingsPageDetailedViewTitle')); ?> </span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ratingsPageStoreRating')); ?>:</label>
                    <div class="col-lg-9">
                        <select class="form-control select-search" name="rating_store" id="storeRating" disabled>
                            <option value="1">1 Star</option>
                            <option value="2">2 Two</option>
                            <option value="3">3 Star</option>
                            <option value="4">4 Star</option>
                            <option value="5">5 Stars</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-3 col-form-label"><?php echo e(__('storeDashboard.ratingsPageStoreReview')); ?>:</label>
                    <div class="col-lg-9">
                        <textarea class="form-control" name="review_store" placeholder="Store Review"
                            rows="6" id="storeReviewText" readonly></textarea>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
      $('.reviewViewButton').click(function(event) {
        $('#storeRating').val($(this).attr("data-ratingStore"));
        $('#storeReviewText').val($(this).attr("data-reviewStore"))
      });
    });
</script>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/ratings.blade.php ENDPATH**/ ?>