
<?php $__env->startSection("title"); ?> Promo Slider's Stores - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .assigning-checkboxes label {
    margin-right: 10px;
    background-color: rgba(250, 250, 250, 0.3);
    border-radius: 25px;
    margin-bottom: 1.2rem;
    }
    .assigning-checkboxes label span {
    text-align: center;
    display: block;
    padding: 8px 15px;
    border: 1px solid #eee;
    border-radius: 25px;
    }
    .assigning-checkboxes label input {
    position: absolute;
    top: -20px;
    display: none;
    }
    .assigning-checkboxes input:checked + span {
    background-color: #2ebf91;
    padding: 8px 15px;
    color: #fff;
    border: 1px solid #eee;
    }
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2">Editing</span>
                <span class="badge badge-primary badge-pill animated flipInX">"<?php echo e($user->email); ?>"</span>
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <legend class="font-weight-semibold text-uppercase font-size-sm">
                    <i class="icon-address-book mr-2"></i> <?php echo e($user->name); ?>'s Stores
                </legend>
                <div class="form-group row form-group-feedback form-group-feedback-right">
                    <?php if(count($userRestaurants) === 0): ?>
                    <div class="col-lg-9">
                        <p class="text-warning"><?php echo e($user->name); ?> is not assigned to take deliveries from any stores yet.</p>
                    </div>
                    <?php else: ?>
                    <br>
                    <div class="col-lg-9">
                        <p><strong><?php echo e($user->name); ?></strong> is serving <strong><?php echo e($userRestaurants->count()); ?> </strong> stores.</p>
                        <?php $__currentLoopData = $userRestaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge badge-flat border-grey-800" style="font-size: 0.9rem;"><?php echo e($ur->name); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg" id="manageRestaurants">
                <b><i class="icon-gear ml-1"></i></b>
                MANAGE
                </button>
                <!-- <?php echo e($user); ?> -->
            </div>
        </div>
    </div>
    <div class="col-md-12 hidden" id="manageRestaurantsBlock">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input"
                placeholder="Filter with slider name...">
            <div class="form-control-feedback form-control-feedback-lg">
                <i class="icon-search4"></i>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="col-lg-12">
                    <form action="<?php echo e(route('restaurant.updateStoreOwnerSlider')); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo e($user->id); ?>">
                        <div class="text-right mb-4">
                            <button type="button" class="btn btn-primary btn-labeled btn-labeled-left btn-sm" id="checkAll" data-popup="tooltip" title="Double Click to Check All" data-placement="left">
                            <b><i class="icon-check ml-1"></i></b>
                                Check All
                            </button>
                            <button type="button" class="btn btn-primary btn-labeled btn-labeled-left btn-sm" id="unCheckAll" data-popup="tooltip" title="Double Click to Un-check All" data-placement="top">
                            <b><i class="icon-cross3 ml-1"></i></b>
                                Un-check All
                            </button>
                        </div>
                        <div class="assigning-checkboxes mt-3">
                        <?php $__currentLoopData = $allRestaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <input type="hidden" name="sliderid[]" value="<?php echo e($ar->id); ?>">
    <label>
        <input type="checkbox" 
               data-name="<?php echo e($ar->name); ?>" 
               name="user_restaurants[]" 
               value="<?php echo e($ar->id); ?>" 
               <?php if($ar->slides->contains('is_active', 1)): ?> checked="checked" <?php endif; ?> />
        <span><?php echo e($ar->name); ?></span>
    </label>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg">
                            <b><i class="icon-database-insert ml-1"></i></b>
                            UPDATE
                            </button>
                        </div>
                        <?php echo csrf_field(); ?>
                    </form>
                </div>
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
                <form action="<?php echo e(route('restaurant.createSlider')); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="userRestaurantsIds" value="<?php echo e(implode(',', $userRestaurantsIds)); ?>">
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Name:</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="name"
                                placeholder="Enter Slider Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
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
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Mobile/Web:</label>
                        <div class="col-lg-9">
                            <select id="viewSelect" name="view" class="form-control form-control-lg" required>
                                <option value="mobile">Mobile</option>
                                <option value="web">Web</option>
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
                    <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Slide Image:</label>
                            <div class="col-lg-9">
                                <img class="slider-preview-image hidden"/>
                                <div class="uploader">
                                    <input type="file" class="form-control-uniform" name="image" required accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                                    <small>Image of minimum dimension 384x384</small>
                                </div>
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
    $(function() {
        $('#manageRestaurants').click(function(event) {
            $(this).hide();
            $('#manageRestaurantsBlock').removeClass('hidden');
        });
    
        $('.assigning-checkboxes label').each(function(){
            $(this).attr('data-name', $(this).text().toLowerCase());
        });
    
        $('.search-input').on('keyup', function(){
        var searchTerm = $(this).val().toLowerCase();
            $('.assigning-checkboxes label').each(function(){
                if ($(this).filter('[data-name *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        
        
        $('#checkAll').dblclick(function(event) {
            $("input:checkbox").prop("checked", true);
        });
        $('#unCheckAll').dblclick(function(event) {
            $("input:checkbox").prop("checked", false);
        });
        
    }); 
    function readURL(input) {
        if (input.files && input.files[0]) {
            let reader = new FileReader();
            reader.onload = function (e) {
                $('.slider-preview-image')
                    .removeClass('hidden')
                    .attr('src', e.target.result)
                    .width(120)
                    .height(120);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/restaurentOwnerSliders.blade.php ENDPATH**/ ?>