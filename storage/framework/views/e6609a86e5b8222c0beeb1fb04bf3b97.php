
<?php $__env->startSection("title"); ?> Promo Sliders - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4>
                <span class="font-weight-bold mr-2">Total</span>
                <i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2"><?php echo e($count); ?> Sliders</span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="addNewSlider"
                    data-toggle="modal" data-target="#addNewSliderModal">
                    <b><i class="icon-plus2"></i></b>
                    Add New Slider
                </button>
            </div>
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
                            <th>Name</th>
                            <th>Status</th>
                            <th>No. of Slides</th>
                            <th>Position</th>
                            <th>Size</th>
                            <th>Created At</th>
                            <th class="text-center" style="width: 10%;"><i class="
                                icon-circle-down2"></i></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slider): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($slider->name); ?></td>
                                <td><?php if($slider->is_active): ?>
                                    <span class="badge badge-flat border-grey-800 text-default text-capitalize">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-flat border-grey-800 text-default text-capitalize">
                                        Inactive
                                    </span>
                                <?php endif; ?>
                                </td>
                                <td><?php echo e(count($slider->slides)); ?></td>
                                <?php if($slider->position_id == 0): ?>
                                    <td>MAIN</td>
                                <?php endif; ?>
                                <?php if($slider->position_id == 1): ?>
                                    <td>After 1st Store</td>
                                <?php endif; ?>
                                <?php if($slider->position_id == 2): ?>
                                    <td>After 2nd Store</td>
                                <?php endif; ?>
                                <?php if($slider->position_id == 3): ?>
                                    <td>After 3rd Store</td>
                                <?php endif; ?>
                                <?php if($slider->position_id == 4): ?>
                                    <td>After 4th Store</td>
                                <?php endif; ?>
                                <?php if($slider->position_id == 5): ?>
                                    <td>After 5th Store</td>
                                <?php endif; ?>
                                <?php if($slider->position_id == 6): ?>
                                    <td>After 6th Store</td>
                                <?php endif; ?>

                                <?php if($slider->size == 1): ?>
                                    <td>Extra Small</td>
                                <?php endif; ?>
                                <?php if($slider->size == 2): ?>
                                    <td>Small</td>
                                <?php endif; ?>
                                <?php if($slider->size == 3): ?>
                                    <td>Medium</td>
                                <?php endif; ?>
                                <?php if($slider->size == 4): ?>
                                    <td>Large</td>
                                <?php endif; ?>
                                <?php if($slider->size == 5): ?>
                                    <td>Extra Large</td>
                                <?php endif; ?>
                                <td><?php echo e($slider->created_at->diffForHumans()); ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-justified">
                                        <a href="<?php echo e(route('admin.get.editSlider', $slider->id)); ?>"
                                            class="btn btn-sm btn-primary"> Edit </a>
                                        <?php if($slider->is_active): ?>
                                            <a href="<?php echo e(route('admin.disableSlider', $slider->id)); ?>"
                                                class="btn btn-sm btn-primary ml-1" data-popup="tooltip" title="Disable Slider"
                                                data-placement="bottom"> <i class="icon-switch2"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('admin.disableSlider', $slider->id)); ?>"
                                                class="btn btn-sm btn-danger ml-1 " data-popup="tooltip" title="Enable Slider"
                                                data-placement="bottom"> <i class="icon-switch2"></i> </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="addNewSliderModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold">Add New Slider</span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('admin.createSlider')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Name:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="Enter Slider Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Mobile/Web:</label>
                        <div class="col-lg-9">
                            <select id="viewSelect" name="view" class="form-control form-control-lg" required>
                                <option value="mobile">Mobile</option>
                                <option value="web">Web</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="positionSelectRow">
                        <label class="col-lg-3 col-form-label">Position:</label>
                        <div class="col-lg-9">
                            <select name="position_id" class="form-control form-control-lg">
                                <option value="0">Main Position</option>
                                <option value="1">After 1st Store</option>
                                <option value="2">After 2nd Store</option>
                                <option value="3">After 3rd Store</option>
                                <option value="4">After 4th Store</option>
                                <option value="5">After 5th Store</option>
                                <option value="6">After 6th Store</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="sizeSelectRow">
                        <label class="col-lg-3 col-form-label">Size:</label>
                        <div class="col-lg-9">
                            <select id="sizeSelect" name="size" class="form-control form-control-lg" required>
                                <option value="1">Extra Small</option>
                                <option value="2">Small</option>
                                <option value="3">Medium</option>
                                <option value="4">Large</option>
                                <option value="5">Extra Large</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="defaultSizeMessage" style="display:none;">
                        <label class="col-lg-3 col-form-label">Size:</label>
                        <div class="col-lg-9 mt-3">
                            <select id="sizeSelect" name="size" class="form-control form-control-lg" required>
                                <option value="3">Medium</option>
                            </select>
                            <p class="form-control-plaintext ">Mobile view has a default size.</p>
                        </div>
                    </div>
                    <hr>
                    <?php echo csrf_field(); ?>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            SAVE
                            <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                    <span class="help-text text-muted">The new slider will be <b class="text-danger">inactive</b> by
                        default.</span>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select').select2({
        minimumResultsForSearch: Infinity,
    });

    $('.slider-size').numeric({ allowThouSep: false, maxDecimalPlaces: 0, allowMinus: false, min: 1, max: 5 });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewSelect = document.getElementById('viewSelect');
        const sizeSelectRow = document.getElementById('sizeSelectRow');
        const defaultSizeMessage = document.getElementById('defaultSizeMessage');

        function updateView() {
            if (viewSelect.value === 'mobile') {
                sizeSelectRow.style.display = 'none';
                defaultSizeMessage.style.display = 'flex';
            } else {
                sizeSelectRow.style.display = 'flex';
                defaultSizeMessage.style.display = 'none';
            }
        }

        viewSelect.addEventListener('change', updateView);

        // Initial check on page load
        updateView();
    });
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/sliders.blade.php ENDPATH**/ ?>