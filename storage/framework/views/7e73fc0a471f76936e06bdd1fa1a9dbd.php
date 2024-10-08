
<?php $__env->startSection("title"); ?> Edit Slider - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2">Editing: </span>
                <span class="badge badge-primary badge-pill animated flipInX"><?php echo e($slider->name); ?></span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content" style="margin-bottom: 10rem;">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        Slider Properties
                    </legend>
                    <form action="<?php echo e(route('admin.updateSlider')); ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo e($slider->id); ?>">
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Name:</label>
                            <div class="col-lg-8">
                                <input type="text" class="form-control form-control-lg" name="name"
                                    placeholder="Enter Slider Name" required value="<?php echo e($slider->name); ?>">
                            </div>
                        </div>
                        <div class="form-group row"  id="positionSelectRow">
                            <label class="col-lg-4 col-form-label">Position:</label>
                            <div class="col-lg-8">
                                <select name="position_id" class="form-control form-control-lg select">
                                    <option value="0" <?php if($slider->position_id == 0): ?> selected="selected" <?php endif; ?>>Main
                                        Position</option>
                                    <option value="1" <?php if($slider->position_id == 1): ?> selected="selected" <?php endif; ?>>After 1st
                                        Store</option>
                                    <option value="2" <?php if($slider->position_id == 2): ?> selected="selected" <?php endif; ?>>After 2nd
                                        Store</option>
                                    <option value="3" <?php if($slider->position_id == 3): ?> selected="selected" <?php endif; ?>>After 3rd
                                        Store</option>
                                    <option value="4" <?php if($slider->position_id == 4): ?> selected="selected" <?php endif; ?>>After 4th
                                        Store</option>
                                    <option value="5" <?php if($slider->position_id == 5): ?> selected="selected" <?php endif; ?>>After 5th
                                        Store</option>
                                    <option value="6" <?php if($slider->position_id == 6): ?> selected="selected" <?php endif; ?>>After 6th
                                        Store</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Mobile/Web:</label>
                            <div class="col-lg-8">
                                <select id="viewSelect" name="view" class="form-control form-control-lg" required>
                                    <option value="mobile" <?php if($slider->view == 'mobile'): ?> selected="selected" <?php endif; ?>>
                                        Mobile</option>
                                    <option value="web" <?php if($slider->view == 'web'): ?> selected="selected" <?php endif; ?>>Web
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="defaultSizeMessage" style="display:none;">
                            <label class="col-lg-4 col-form-label">Size:</label>
                            <div class="col-lg-8">
                                <select id="sizeSelect" name="size" class="form-control form-control-lg" required>
                                    <option value="3">Medium</option>
                                </select>
                                <p class="form-control-plaintext ">Mobile view has a default size.</p>
                            </div>
                        </div>
                        <div class="form-group row" id="sizeSelectRow">
                            <label class="col-lg-4 col-form-label">Size:</label>
                            <div class="col-lg-8">
                                <select name="size" class="form-control form-control-lg select" required="required">
                                    <option value="1" <?php if($slider->size == 1): ?> selected="selected" <?php endif; ?>>Extra Small
                                    </option>
                                    <option value="2" <?php if($slider->size == 2): ?> selected="selected" <?php endif; ?>>Small</option>
                                    <option value="3" <?php if($slider->size == 3): ?> selected="selected" <?php endif; ?>>Medium</option>
                                    <option value="4" <?php if($slider->size == 4): ?> selected="selected" <?php endif; ?>>Large</option>
                                    <option value="5" <?php if($slider->size == 5): ?> selected="selected" <?php endif; ?>>Extra Large
                                    </option>
                                </select>
                            </div>
                        </div>

                        <?php echo csrf_field(); ?>
                        <div class="d-flex justify-content-end my-4">
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary">
                                    Update
                                    <i class="icon-database-insert ml-1"></i>
                                </button>
                            </div>
                        </div>

                        <hr>

                        <div class="text-left">
                            <div class="btn-group btn-group-justified">
                                <?php if($slider->is_active): ?>
                                    <a href="<?php echo e(route('admin.disableSlider', $slider->id)); ?>"
                                        class="btn btn-warning text-white mr-3" data-popup="tooltip" title="Disable Slider"
                                        data-placement="bottom"> Disable <i class="icon-switch2 ml-1"></i> </a>
                                <?php else: ?>
                                    <a href="<?php echo e(route('admin.disableSlider', $slider->id)); ?>"
                                        class="btn btn-primary text-white mr-3" data-popup="tooltip" title="Enable Slider"
                                        data-placement="bottom"> Enable <i class="icon-switch2 ml-1"></i> </a>
                                <?php endif; ?>
                                <a class="btn btn-danger text-white" data-toggle="modal"
                                    data-target="#deleteSliderConfirmModal" id="deleteSliderButton"
                                    href="javascript:void(0)">
                                    DELETE SLIDER
                                    <i class="icon-trash ml-1"></i>
                                </a>
                            </div>


                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        Slides
                    </legend>
                    <?php if(count($slides) == 0): ?>
                        <div id="noSlidesContainer">
                            <strong>No Slides</strong>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary" id="addSlide">
                                    ADD SLIDE
                                    <i class="icon-plus3 ml-1"></i>
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row" id="sortable">
                            <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-3 mb-2 each-slide" data-id="<?php echo e($slide->id); ?>">
                                    <p class="h6 mb-1"><strong><?php echo e($slide->name); ?></strong></p>
                                    <img src="<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?><?php echo e($slide->image); ?>"
                                        alt="<?php echo e($slide->name); ?>" width="150" height="150" data-popup="tooltip"
                                        title="<?php echo e($slide->link); ?>" data-placement="right">
                                    <div class="btn-group btn-group-justified" style="width: 150px">
                                        <a href="<?php echo e(route('admin.editSlide', $slide->id)); ?>" class="btn btn-dark rounded-0"
                                            data-popup="tooltip" title="Edit Slide" data-placement="bottom"> <i
                                                class="icon-pencil3"></i> </a>
                                        <a href="<?php echo e(route('admin.deleteSlide', $slide->id)); ?>" class="btn btn-danger"
                                            data-popup="tooltip" title="Delete Slide" data-placement="bottom"> <i
                                                class="icon-trash"></i> </a>
                                        <?php if($slide->is_active): ?>
                                            <a href="<?php echo e(route('admin.disableSlide', $slide->id)); ?>"
                                                class="btn btn-secondary rounded-0" data-popup="tooltip" title="Disable Slide"
                                                data-placement="bottom"> <i class="icon-switch2"></i> </a>
                                        <?php else: ?>
                                            <a href="<?php echo e(route('admin.disableSlide', $slide->id)); ?>"
                                                class="btn btn-warning rounded-0" data-popup="tooltip" title="Enable Slide"
                                                data-placement="bottom"> <i class="icon-switch2"></i> </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary" id="addSlide">
                                ADD SLIDE
                                <i class="icon-plus3 ml-1"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo e(route('admin.saveSlide')); ?>" method="POST" id="slideForm" class="mt-3 hidden"
                        enctype="multipart/form-data">
                        <legend class="font-weight-semibold text-uppercase font-size-sm">
                            Add New Slide
                        </legend>
                        <input type="hidden" class="form-control form-control-lg" name="promo_slider_id"
                            value="<?php echo e($slider->id); ?>" required>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Slide Name:</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control form-control-lg" name="name"
                                    placeholder="Slide Name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Slide Image:</label>
                            <div class="col-lg-9">
                                <img class="slider-preview-image hidden" />
                                <div class="uploader">
                                    <input type="file" class="form-control-uniform" name="image" required
                                        accept="image/x-png,image/gif,image/jpeg" onchange="readURL(this);">
                                    <small>Image of minimum dimension 384x384</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="linkSlideTo">
                            <label class="col-lg-3 col-form-label">Link Slide To:</label>
                            <div class="col-lg-9">
                                <select class="form-control form-control-lg linkTo" name="model">
                                    <option></option>
                                    <option value="1">Store</option>
                                    <option value="2">Item</option>
                                    <option value="3">Custom URL</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row hidden" id="storesList">
                            <label class="col-lg-3 col-form-label">Select a Store:</label>
                            <div class="col-lg-9">
                                <select class="form-control form-control-lg storesList" name="restaurant_id">
                                    <option></option>
                                    <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($restaurant->id); ?>"><?php echo e($restaurant->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row hidden" id="itemList">
                            <label class="col-lg-3 col-form-label">Select an Item:</label>
                            <div class="col-lg-9">
                                <select class="form-control form-control-lg itemList" name="item_id">
                                    <option></option>
                                    <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <optgroup label="<?php echo e($store->name); ?>">
                                            <?php $__currentLoopData = $store->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $storeItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($storeItem->id); ?>"><?php echo e($storeItem->name); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </optgroup>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row hidden" id="customURL">
                            <label class="col-lg-3 col-form-label">Custom URL:</label>
                            <div class="col-lg-9">
                                <input type="url" class="form-control form-control-lg" name="customUrl" id="customUrl"
                                    placeholder="Enter your custom URL">
                                <span class="help-text small">Enter full URL with http:// or https://</span>
                            </div>
                        </div>

                        <div id="onCustomUrlActive" class="hidden">
                            <legend class="font-weight-semibold text-uppercase font-size-sm">
                                Display Location
                            </legend>
                            <div class="form-group row">
                                <label class="col-lg-3 col-form-label"><strong>Location restriction<i
                                            class="icon-question3 ml-1" data-popup="tooltip"
                                            title="Enabling this will allow you to set latitude, longitude and display radius for this slide/image and only when user's location is within the radius, the slide/image will appear."
                                            data-placement="top"></i></strong> </label>
                                <div class="col-lg-9">
                                    <div class="checkbox checkbox-switchery mt-2">
                                        <label>
                                            <input value="true" type="checkbox" class="switchery-primary">
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="locationProperties" class="hidden">
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Latitude:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg latitude" name="latitude"
                                            placeholder="Enter Latitude">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Longitude:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg longitude"
                                            name="longitude" placeholder="Enter Longitude">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">Radius:</label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg radius" name="radius"
                                            placeholder="Enter Operation Radius">
                                    </div>
                                </div>

                                <span class="text-muted">To get a valid latitude/longitude, you can use services like:
                                    <a href="https://www.mapcoordinates.net/en"
                                        target="_blank">https://www.mapcoordinates.net/en</a></span> <br> <span
                                    class="text-muted">If you enter an invalid Latitude/Longitude the system might crash
                                    with a white screen.</span>
                            </div>
                        </div>
                        <?php echo csrf_field(); ?>
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                UPDATE
                                <i class="icon-database-insert ml-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="deleteSliderConfirmModal" class="modal fade mt-5" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold">Are you sure?</span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <span class="help-text">Be careful, all the slides associated with this slider will also be permanently
                    deleted. <br> You can use the "<strong>DISABLE</strong>" feature to temporarily disable the
                    Slider.</span>
                <div class="modal-footer mt-4">
                    <a href="<?php echo e(route('admin.deleteSlider', $slider->id)); ?>" class="btn btn-primary">Yes</a>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="token" value="<?php echo e(csrf_token()); ?>">
<script>
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
    $(function () {

        $('#changeLink').click(function (event) {
            $('#linkSlideTo').removeClass('hidden');
            $(this).addClass('hidden');
            $('.linkTo').attr('required', 'required');
        });

        $('.latitude').numeric({ allowThouSep: false });
        $('.longitude').numeric({ allowThouSep: false });
        $('.radius').numeric({ allowThouSep: false, maxDecimalPlaces: 0, allowMinus: false });

        var elem = document.querySelector('.switchery-primary');
        var switchery = new Switchery(elem, { color: '#2196F3' });

        elem.onchange = function () {
            if (elem.checked) {
                $('#locationProperties').removeClass('hidden');
                $('.latitude').attr('required', 'required');
                $('.longitude').attr('required', 'required');
                $('.radius').attr('required', 'required');
            } else {
                $('#locationProperties').addClass('hidden');
                $('.latitude').removeAttr('required');
                $('.longitude').removeAttr('required');
                $('.radius').removeAttr('required');
            }
        };

        $('.linkTo').select2({
            placeholder: "Choose an option",
            minimumResultsForSearch: -1
        });

        $('.storesList').select2({
            placeholder: "Select a store",
        });

        $('.itemList').select2({
            placeholder: "Select an item",
        });

        $("[name='model']").change(function () {
            let selectedLinkOption = $(this).val();

            //on store selected
            if (selectedLinkOption == 1) {
                $('#storesList').removeClass('hidden');
                $('.storesList').attr('required', 'required');

                $('#itemList').addClass('hidden');
                $('#customURL').addClass('hidden');

                $('#customUrl').removeAttr('required');
                $('.itemList').removeAttr('required');

                $('#onCustomUrlActive').addClass('hidden');
            }

            //on items selected
            if (selectedLinkOption == 2) {
                $('#itemList').removeClass('hidden');
                $('.itemList').attr('required', 'required');

                $('#storesList').addClass('hidden');
                $('#customURL').addClass('hidden');

                $('#customUrl').removeAttr('required');
                $('.storesList').removeAttr('required');

                $('#onCustomUrlActive').addClass('hidden');
            }

            //om custom URL selected
            if (selectedLinkOption == 3) {
                $('#customURL').removeClass('hidden');
                $('#customUrl').attr('required', 'required');

                $('#itemList').addClass('hidden');
                $('#storesList').addClass('hidden');

                $('.storesList').removeAttr('required');
                $('.itemList').removeAttr('required');

                $('#onCustomUrlActive').removeClass('hidden');
            }

        });

        $('.select').select2({
            minimumResultsForSearch: -1
        });

        $('.form-control-uniform').uniform();

        $("#addSlide").click(function (event) {
            $("#slideForm").removeClass('hidden');
            $("#noSlidesContainer").remove();
            $(this).remove();
        });

        $("#urlInput").on("change paste keyup", function () {
            $("#urlHelpBlockContainer").removeClass('hidden');
            $("#appendURL").html($(this).val());
        });

        let URL = "<?php echo e(url("/")); ?>";
        URL = URL.substring(0, URL.lastIndexOf("/") + 1);
        $("#baseURL").html(URL);

        /*handle custom url click */
        $('#enterCustomURL').click(function (event) {
            $(this).hide();
            $('#customURL').removeClass('hidden');
            $('#restaurantURL').addClass('hidden');
            $('#urlInput').removeAttr('required');
            $('#customUrl').attr('required', 'required');
        });

        $('#sortable').sortable({
            animation: 350,
            easing: "cubic-bezier(0.42, 0, 0.58, 1.0)",
            ghostClass: "sortable-placeholder",
            onUpdate: function (evt) {
                let newSortOrder = {};
                $('.each-slide').each(function () {
                    newSortOrder[$(this).index()] = $(this).data('id');
                });
                $.ajax({
                    url: '<?php echo e(route('admin.updateSlidePosition')); ?>',
                    type: 'POST',
                    dataType: 'JSON',
                    data: { newOrder: newSortOrder, _token: $('#token').val() },
                })
                    .done(function (res) {
                        $.jGrowl("Slides sorted successfully", {
                            position: 'bottom-center',
                            header: 'Done ✅',
                            theme: 'bg-success',
                            life: '2000',
                        });
                    })
                    .fail(function () {
                        console.log("error");
                        $.jGrowl("Something went wrong! Reload the page and try again later.", {
                            position: 'bottom-center',
                            header: 'Wooopsss ⚠️',
                            theme: 'bg-warning',
                        });
                    })
            },
        });
    });
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/admin/editSlider.blade.php ENDPATH**/ ?>