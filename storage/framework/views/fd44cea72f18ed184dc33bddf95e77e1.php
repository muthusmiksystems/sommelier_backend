
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.bpTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .booking_time_btn .booking_time_text{
        display: inline-block;
        padding: 5px;
        border: 1px solid;
        border-radius: 50px;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .booking_time_btn .booking_time_text:hover{
        color: #fff;
        background-color: #8360c3;
        border-color:#8360c3;
        cursor: pointer;
    }
    .booking_time_btn .booking_time_text.active{
        color: #fff;
        background-color: #8360c3;
        border-color:#8360c3;
    }
    .booking_time_btn .booking_time_available{
        color: #fff;
        background-color: #008000;
        border-color:#008000;
    }
    .booking_time_btn .booking_time_warning{
        color: #fff;
        background-color: #ffcc00;
        border-color:#ffcc00;
    }
    .booking_time_btn .booking_time_not_available{
        color: #fff;
        background-color: #FF0000;
        border-color:#FF0000;
    }
    .shift_label_text{
        display:block;
        width:100%;
        font-weight:600;
    }
    .booking_complete{
        color:#ffffff;
        background-color:green;
    }
    .booking_cancel{
        color:#ffffff;
        background-color:red;
    }
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <?php if(empty($query)): ?>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <span class="badge badge-primary badge-pill animated flipInX"><?php echo e($count); ?></span>
                <span class="font-weight-bold ml-4"><?php echo e(__('storeDashboard.bpNoOfPax')); ?></span>
                <span class="badge badge-primary badge-pill animated flipInX"><?php echo e($no_of_pax); ?></span>
                <?php else: ?>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <span class="badge badge-primary badge-pill animated flipInX mr-2"><?php echo e($count); ?></span>
                <span class="font-weight-bold mr-2">Results for "<?php echo e($query); ?>"</span>
                <?php endif; ?>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewItem"
                    data-toggle="modal" data-target="#addNewItemModal">
                    <b><i class="icon-plus2"></i></b>
                    <?php echo e(__('storeDashboard.bpAddNewBookingmBtn')); ?>

                </button>
                <!-- <button type="button" class="btn btn-secondary btn-labeled btn-labeled-left" id="addBulkItem"
                    data-toggle="modal" data-target="#addBulkItemModal">
                    <b><i class="icon-database-insert"></i></b>
                    <?php echo e(__('storeDashboard.bpBulkCsvUpload')); ?>

                </button> -->
            </div>
        </div>
    </div>
</div>
<div class="content">
    <form action="<?php echo e(route('restaurant.bookings')); ?>" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input" placeholder="<?php echo e(__('storeDashboard.bpSearchPH')); ?>"
                name="query">
            <div class="form-control-feedback form-control-feedback-lg">
                <i class="icon-search4"></i>
            </div>
        </div>
        <input type="hidden" name="bookingdate" value="<?php echo e((!empty($bookingdate)) ? $bookingdate : date('Y-m-d')); ?>"/>
        <input type="hidden" name="booking_status" value="<?php echo e((!empty($booking_status)) ? $booking_status : 'all'); ?>"/>
        <!-- <?php echo csrf_field(); ?> -->
    </form>
    <form action="<?php echo e(route('restaurant.bookings')); ?>" method="GET">
        <div class="row">
            <div class="col-lg-3">
                <select name="resturant" class="form-control filter_restaurant_dropdown">
                    <option value="all">Select Venue</option>
                    <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(isset($restaurant->restaurantSettings->sommelier_reservations) && $restaurant->restaurantSettings->sommelier_reservations == 'yes'): ?>
                            <option value="<?php echo e($restaurant->id); ?>" class="text-capitalize" <?php echo e(($restaurant->id == $restaurant_id) ? "selected='selected'" : ""); ?>><?php echo e($restaurant->name); ?></option>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-lg-3">
                <select name="meal_type" class="form-control">
                    <option value="all" <?php echo e(($meal_type == "all") ? 'selected="selected"' : ""); ?>>All Meal Type</option>
                    <option value="Breakfast" <?php echo e(($meal_type == "Breakfast") ? 'selected="selected"' : ""); ?>>Breakfast</option>
                    <option value="Lunch" <?php echo e(($meal_type == "Lunch") ? 'selected="selected"' : ""); ?>>Lunch</option>
                    <option value="Dinner" <?php echo e(($meal_type == "Dinner") ? 'selected="selected"' : ""); ?>>Dinner</option>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="form-group form-group-feedback form-group-feedback-right search-box">
                    <select class="form-control" name="booking_status">
                        <option value="all" <?php echo e(($booking_status == 'all' || $booking_status == '') ? "selected='selected'" : ''); ?>>All Booking Status</option>
                        <option value="open" <?php echo e(($booking_status == 'open') ? "selected='selected'" : ''); ?>>Open</option>
                        <option value="reserved" <?php echo e(($booking_status == 'reserved') ? "selected='selected'" : ''); ?>>Reserved</option>
                        <option value="completed" <?php echo e(($booking_status == 'completed') ? "selected='selected'" : ''); ?>>Completed</option>
                        <option value="cancelled" <?php echo e(($booking_status == 'cancelled') ? "selected='selected'" : ''); ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="form-group form-group-feedback form-group-feedback-right search-box">
                    <input type="date" class="form-control" placeholder="<?php echo e(__('storeDashboard.bpBookingDate')); ?>"
                        name="bookingdate" value="<?php echo e((!empty($bookingdate)) ? $bookingdate : date('Y-m-d')); ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <select name="time_slot" class="form-control filter_time_slots_dropdown">
                    <option value="all">All Time Slot</option>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="form-group form-group-feedback form-group-feedback-right search-box">
                    <button type="submit" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" name="action" id="BookingSearch" value="bookingSearch">
                        <b><i class="icon-search4"></i></b>
                        <?php echo e(__('storeDashboard.bpBookingSearch')); ?>

                    </button>
                    <button type="submit" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" formaction="<?php echo e(route('restaurant.bookingsPrint')); ?>" name="action" id="BookingPrint" value="bookingPrint">
                        <b><i class="icon-printer"></i></b>
                        <?php echo e(__('storeDashboard.bpBookingPrint')); ?>

                    </button>
                </div>
            </div>
        </div>

        <!-- <?php echo csrf_field(); ?> -->
    </form>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('storeDashboard.bpTableBookingId')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTableCustomerName')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTableShift')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTableTableNo')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTableTime')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTablePax')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTableComments')); ?></th>
                            <th><?php echo e(__('storeDashboard.bpTableRestaurant')); ?></th>
                            <th>
                                <div class="booking_action" style="display:flex;">
                                    <div class="action_title">
                                        <?php echo e(__('storeDashboard.bpTableAction')); ?>

                                    </div>
                                    <div class="booking_done_all" style="margin-left:15px;">
                                        <?php if($in_complete_booking > 0 && strtotime(date('Y-m-d', strtotime($bookingdate))) < strtotime(date('Y-m-d'))): ?>
                                        
                                            <div class="checkbox checkbox-switchery ml-1">
                                                <label>
                                                <input value="true" type="checkbox" class="action-switch-booking-done-all" data-booking_date="<?php echo e($bookingdate); ?>" data-booking_status="<?php echo e($booking_status); ?>">
                                                </label>
                                            </div>
                                        
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>#<?php echo e($booking->unique_booking_id); ?></td>
                            <td><?php echo e($booking->booking_name); ?></td>
                            <td><?php echo e($booking->booking_shift); ?></td>
                            <td>
                                <?php if(!empty($booking->resTables)): ?>
                                    <?php $__currentLoopData = $booking->resTables; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table_row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <?php echo e($table_row->table_number); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e(date('h:i a', strtotime($booking->booking_datetime))); ?></td>
                            <td><?php echo e($booking->no_of_seats); ?></td>
                            <td><?php echo e($booking->comments); ?></td>
                            <td><?php echo e($booking->restaurant->name); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-justified align-items-center">
                                    <?php if($booking->booking_status == 'completed'): ?>
                                    <span class="badge badge-icon booking_complete" disabled> Completed <i
                                            class="icon-check ml-1"></i></span>
                                    <?php elseif($booking->booking_status == 'cancelled'): ?>
                                    <span class="badge badge-icon booking_cancel" disabled> Cancelled <i
                                            class="icon-cross ml-1"></i></span>
                                    <?php endif; ?>
                                    <?php if($booking->booking_status != 'completed' && $booking->booking_status != 'cancelled'): ?>
                                        <a href="<?php echo e(route('restaurant.get.editBooking', $booking->id)); ?>"
                                        class="badge badge-primary badge-icon"> <?php echo e(__('storeDashboard.edit')); ?> <i
                                            class="icon-database-edit2 ml-1"></i></a>

                                        <a href="<?php echo e(route('restaurant.get.cancelBooking', $booking->id)); ?>"
                                        class="badge badge-primary badge-icon ml-1" onclick="return confirm('Are you sure you want to cancel this booking?')"> <?php echo e(__('storeDashboard.cancel')); ?> <i
                                            class="icon-cross"></i></a>
                                    <?php endif; ?>
                                    <?php if($booking->booking_type == 'recurring' ): ?>
                                        <a href="<?php echo e(route('restaurant.get.cancelAllBooking', $booking->id)); ?>"
                                        class="badge badge-primary badge-icon ml-1" onclick="return confirm('Are you sure you want to cancel all future booking?')"> <?php echo e(__('storeDashboard.cancelall')); ?> <i
                                            class="icon-cross"></i></a>
                                    <?php endif; ?>
                                    <?php if($booking->booking_status == 'open' || $booking->booking_status == 'reserved'): ?>
                                        <div class="checkbox checkbox-switchery ml-1" style="padding-top: 0.8rem;">
                                            <label>
                                            <input value="true" type="checkbox" class="action-switch"
                                            <?php if($booking->booking_status == 'completed'): ?> checked="checked" <?php endif; ?> data-id="<?php echo e($booking->id); ?>">
                                            </label>
                                        </div>
                                    <?php endif; ?>
                                </div>


                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
                <div class="mt-3">
                    <?php echo e($bookings->appends($pagination)->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<div id="addNewItemModal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.bpmTitle')); ?></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.saveNewBooking')); ?>" method="POST" enctype="multipart/form-data" id="strip_payment_booking">
              
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span><?php echo e(__('storeDashboard.bpmLabelBookingRestaurant')); ?>:</label>
                        <div class="col-lg-9">
                            <select class="form-control select-search booking_store_select" id="booking_store_select" name="restaurant_id" required>
                                <option value="">Select Venue</option>
                                <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($restaurant->restaurantSettings->sommelier_reservations == 'yes'): ?>
                                    <option value="<?php echo e($restaurant->id); ?>" class="text-capitalize"><?php echo e($restaurant->name); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span><?php echo e(__('storeDashboard.bpmLabelName')); ?></label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="no_of_seats" id="no_of_seats" placeholder="No of seats"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span><?php echo e(__('storeDashboard.bpmTableTiming')); ?></label>
                        <div class="col-lg-9">
                        <i class="fa fa-calendar"></i>
                        <input type="date" class="form-control form-control-lg" id="booking_date_input" name="booking_date" Placeholder="Booking Date" style="padding:.5625rem 1rem;" required>
                            <!-- <textarea class="summernote-editor" name="desc" placeholder="<?php echo e(__('storeDashboard.bpmPhDescription')); ?>"
                                rows="6"></textarea> -->
                        </div>
                    </div>

                    <div class="form-group row booking_timing_block" style="display:none;">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span><?php echo e(__('storeDashboard.bpmLableTiming')); ?></label>
                        <div class="col-lg-9">
                            <div class="booking_time_btn"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Booking Type</label>
                        <div class="col-lg-9">
                            <select name="booking_type" class="form-control form-control-lg booking_type_dropdown_addnew" required>
                                    <option value="onetime">One Time</option>
                                    <option value="recurring">Recurring</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row booking_frequency_dropdown_addnew_row" style="display:none;">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Booking Frequency</label>
                        <div class="col-lg-9">
                            <select name="booking_frequency" class="form-control form-control-lg booking_frequency_dropdown_addnew">
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Table Location</label>
                        <div class="col-lg-9">
                            <select name="booking_table_location" class="form-control form-control-lg table_location_dropdown_addnew" required id="table_location_dropdown_addnew">
                                    <option value="">Please select table location</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Mobile Number</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="mobile_number" placeholder="Enter mobile number"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Email Address</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="email_address" placeholder="Enter the email address" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>First Name</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="first_name" placeholder="Enter the First name" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Last Name</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="last_name" placeholder="Enter the Last name" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Date of Birth</label>
                        <div class="col-lg-9">
                        <i class="fa fa-calendar"></i>
                        <input type="date" class="form-control pull-right" name="dob" formControlName="dob" placeholder="DD-MM-YYYY"
                           id="dateOfBirth">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Comment</label>
                        <div class="col-lg-9">
                        <textarea class="form-control" name="comment" placeholder="<?php echo e(__('storeDashboard.bpmPhComment')); ?>"
                        style="resize:none;"></textarea>
                        </div>
                    </div>

                    <div class="form-group mx-auto flex w-75 mx-auto mt-5 shadow px-4 py-5 rounded-lg border"  id="paymentSection" style="display:none">
                    <div class="checkbox checkbox-switchery m-3 ">
                        <label>
                            <input id="depositeOverride" value="true" type="checkbox" class="deposite_override">
                            <span>Deposite Override</span>
                        </label>
                    </div>
                        <label for="card-element"  class="col-lg-3 ">Credit or debit card</label>
                        <div id="card-element" class="">
                        </div>
                        <div id="card-errors" role="alert"></div>
                    </div>
                    <?php echo csrf_field(); ?>
                    <div class="text-right"  id="first" >
                        <button type="submit" class="btn btn-primary">
                        <?php echo e(__('storeDashboard.save')); ?>

                            <i class="icon-database-insert ml-1"></i></button>
                    </div>
                    <div class="text-right" id="second" style="display:none">
                        <button type="submit" class="btn btn-primary">
                        <?php echo e(__('storeDashboard.save')); ?>

                            <i class="icon-database-insert ml-1"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    
<div id="addBulkItemModal" class="modal fade mt-5" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="font-weight-bold"><?php echo e(__('storeDashboard.bpmCsvTitle')); ?></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="<?php echo e(route('restaurant.itemBulkUpload')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label"><?php echo e(__('storeDashboard.bpmLabelCsvFile')); ?>: </label>
                        <div class="col-lg-10">
                            <div class="uploader">
                                <input type="file" accept=".csv" name="item_csv"
                                    class="form-control-uniform form-control-lg" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-left">
                        <button type="button" class="btn btn-primary" id="downloadSampleItemCsv">
                        <?php echo e(__('storeDashboard.bpmBtnCsvDownloadSample')); ?>

                            <i class="icon-file-download ml-1"></i>
                        </button>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                        <?php echo e(__('storeDashboard.bpmBtnCsvUpload')); ?>

                            <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://js.stripe.com/v3/"></script>

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

        $('.datepicker').bootstrapMaterialDatePicker({
            format: 'MM/DD/YYYY hh:mm A',

        });
        $('.summernote-editor').summernote({
                   height: 200,
                   popover: {
                       image: [],
                       link: [],
                       air: []
                     }
            });

        $('.select').select2({
        minimumResultsForSearch: Infinity,
        placeholder: 'Select addons categories if applicable',
        });
       $('.select-search').select2({
           minimumResultsForSearch: Infinity,
       });


       $('.form-control-uniform').uniform();

        $('#downloadSampleItemCsv').click(function(event) {
           event.preventDefault();
           window.location.href = "<?php echo e(substr(url("/"), 0, strrpos(url("/"), '/'))); ?>/assets/docs/items-sample-csv.csv";
       });
        $('.price').numeric({allowThouSep:false, maxDecimalPlaces: 2 });

        //Switch Action Function
        if (Array.prototype.forEach) {
               var elems = Array.prototype.slice.call(document.querySelectorAll('.action-switch'));
               elems.forEach(function(html) {
                   var switchery = new Switchery(html, { color: '#8360c3' });
               });
           }
           else {
               var elems = document.querySelectorAll('.action-switch');
               for (var i = 0; i < elems.length; i++) {
                   var switchery = new Switchery(elems[i], { color: '#8360c3' });
               }
           }

         $('.action-switch').click(function(event) {
            choice = confirm('are you sure you want to close this booking?');
            if(choice){
                let id = $(this).attr("data-id")
                let url = "<?php echo e(url('/store-owner/booking/disable/')); ?>/"+id;
                window.location.href = url;
            }else{
                return false;
            }
         });


         if (Array.prototype.forEach) {
               var elems = Array.prototype.slice.call(document.querySelectorAll('.deposite_override'));
               elems.forEach(function(html) {
                   var switchery = new Switchery(html, { color: '#8360c3' });
               });
           }
           else {
               var elems = document.querySelectorAll('.deposite_override');
               for (var i = 0; i < elems.length; i++) {
                   var switchery = new Switchery(elems[i], { color: '#8360c3' });
               }
           }
           if (Array.prototype.forEach) {
               var elems = Array.prototype.slice.call(document.querySelectorAll('.action-switch-booking-done-all'));
               elems.forEach(function(html) {
                   var switchery = new Switchery(html, { color: '#8360c3' });
               });
           }
           else {
               var elems = document.querySelectorAll('.action-switch-booking-done-all');
               for (var i = 0; i < elems.length; i++) {
                   var switchery = new Switchery(elems[i], { color: '#8360c3' });
               }
           }
         $('.action-switch-booking-done-all').click(function(event) {
            choice = confirm('are you sure you want to close all booking?');
            if(choice){
                let booking_date = $(this).attr("data-booking_date");
                let booking_status = $(this).attr("data-booking_status");
                let url = "<?php echo e(url('/store-owner/booking/doneall/')); ?>/?booking_date="+booking_date+"&booking_status="+booking_status;
                window.location.href = url;
            }else{
                return false;
            }
         });

         $('#booking_date_input').change(function(){
             booking_date = jQuery(this).val();
             console.log("booking_date",booking_date)
             store_id = jQuery("#booking_store_select").val();
             
             if(store_id != null && store_id != "" && booking_date != null && booking_date != ""){
                $.ajax({
                    type:'post',
                    url:'<?php echo url('store-owner/store/shift-timing'); ?>/'+store_id,
                   data:{_token: "<?php echo e(csrf_token()); ?>", booking_date: booking_date},
                    success:function(data) {
                        if(data.success){
                            jQuery('.booking_timing_block').show();
                            $(".booking_time_btn").html(data.html);
                        }else{
                            jQuery('.booking_timing_block').hide();
                            $(".booking_time_btn").html('');
                        }
                    }
                    });
             }else{
                jQuery('.booking_timing_block').hide();
                $(".booking_time_btn").html('');
             }
         });

         $(document).on('click', '.booking_time_btn .booking_time_text', function(){
            $('.booking_time_btn .booking_time_text.active').removeClass('active');
            $(this).addClass('active');
            $(this).find('input.booking_time_input').prop("checked", true);
         });

         $('.filter_restaurant_dropdown').change(function(){
            restaurant_id = jQuery(this).val();
             if(restaurant_id != null && restaurant_id != "all"){
                $.ajax({
                    type:'post',
                    url:'<?php echo url('store-owner/store/shift-timing-for-filter'); ?>/'+restaurant_id,
                   data:{_token: "<?php echo e(csrf_token()); ?>", time_slot: "<?php echo e((!empty($time_slot)) ? $time_slot : ''); ?>"},
                    success:function(data) {
                        if(data.success){
                            $('.filter_time_slots_dropdown').html(data.html);
                        }else{
                            $('.filter_time_slots_dropdown').empty().html('<option value="all">All Time Slot</option>');
                        }
                    }
                    });
             }else{
                $('.filter_time_slots_dropdown').empty().html('<option value="all">All Time Slot</option>');
             }
         });


         $('.booking_store_select').change(function(){
            restaurant_id = jQuery(this).val();
             if(restaurant_id != null && restaurant_id != ""){
                $.ajax({
                    type:'post',
                    url:'<?php echo url('store-owner/store/restaurant-table-areas'); ?>/'+restaurant_id,
                   data:{_token: "<?php echo e(csrf_token()); ?>"},
                    success:function(data) {
                        if(data.success){
                            $('.table_location_dropdown_addnew').html(data.html);
                        }else{
                            $('.table_location_dropdown_addnew').empty().html('<option value="">Please select table location</option>');
                        }
                    }
                    });
             }else{
                $('.table_location_dropdown_addnew').empty().html('<option value="">Please select table location</option>');
             }
         });

         //$('.filter_restaurant_dropdown').load(function(){
            restaurant_id = jQuery('.filter_restaurant_dropdown').val();
             if(restaurant_id != null && restaurant_id != "all"){
                $.ajax({
                    type:'post',
                    url:'<?php echo url('store-owner/store/shift-timing-for-filter'); ?>/'+restaurant_id,
                   data:{_token: "<?php echo e(csrf_token()); ?>", time_slot: "<?php echo e((!empty($time_slot)) ? $time_slot : ''); ?>"},
                    success:function(data) {
                        if(data.success){
                            $('.filter_time_slots_dropdown').html(data.html);
                        }else{
                            $('.filter_time_slots_dropdown').empty().html('<option value="all">All Time Slot</option>');
                        }
                    }
                    });
             }else{
                $('.filter_time_slots_dropdown').empty().html('<option value="all">All Time Slot</option>');
             }
        // });
        $('#no_of_seats').on('input', function() {
        var newValue = parseInt($(this).val());
        var depositCovers = parseInt(localStorage.getItem('deposit_covers')) ? parseInt(localStorage.getItem('deposit_covers')) : 0;

        // Check if the new value is less than depositCovers
        if (newValue >= depositCovers) {
            localStorage.setItem('payment_gateway', true);
        } else {
            localStorage.setItem('payment_gateway', false);
        }
        
    });
    $('#table_location_dropdown_addnew').change(function() {
        var restaurantId = $(this).val(); // Get the selected restaurant ID
        console.log("restaurantId",restaurantId)
    })
        $('#booking_store_select').change(function() {
        var restaurantId = $(this).val(); // Get the selected restaurant ID
        var requestData = {
            id: restaurantId // Assuming your route expects 'id', adjust if necessary
        };

        // AJAX request to fetch settings based on the selected restaurant
        $.ajax({
            url: "<?php echo e(route('restaurant.get.getSettingsRestaurantbooking', ['id' => ':id'])); ?>".replace(':id', restaurantId), // Ensure correct route name
            method: 'GET',
            data: requestData, // Send data as query parameters
            success: function(response) {
                if(response.restaurant_settings.enable_deposit != null ){
                var depositCovers = response.restaurant_settings.deposit_covers;
                var deposit_amount_per_cover = response.restaurant_settings.deposit_amount_per_cover;
                // Store deposit_covers in localStorage
                localStorage.setItem('deposit_covers', depositCovers);
                localStorage.setItem('deposit_amount_per_cover',deposit_amount_per_cover)
                }
                else{
                    alert("Please Enable deposit for this Restaurant");
                    $('#addNewItemModal').modal('hide');window.location.reload();

                }
                // Update the no_of_seats input field with the retrieved settings // Adjust based on your response structure
            },
            error: function(xhr, status, error) {
                console.error(error);
                // Handle error scenarios if needed
            }
        });
        $.ajax({
            url: "<?php echo e(route('restaurant.get.getRestaurantbooking', ['id' => ':id'])); ?>".replace(':id', restaurantId), // Ensure correct route name
            method: 'GET',
            data: requestData, // Send data as query parameters
            success: function(response) {
                var stripe_public_key = response.restaurant.stripe_public_key;
                // Store deposit_covers in localStorage
                if(response.restaurant.stripe_public_key != null)
                {
                    localStorage.setItem('stripe_public_key', stripe_public_key);
                }
                else{
                    alert("Please provide Stripe Api key")
                    $('#addNewItemModal').modal('hide');
                }


                
                // Update the no_of_seats input field with the retrieved settings // Adjust based on your response structure
            },
            error: function(xhr, status, error) {
                console.error(error);
                // Handle error scenarios if needed
            }
        });
    });

        $(document).on('change', '.booking_type_dropdown_addnew', function(){

            if($(this).find('option:selected').val() == 'recurring'){
                $('.booking_frequency_dropdown_addnew_row').show();
                $('.booking_frequency_dropdown_addnew').prop('required', true);
            }else{
                $('.booking_frequency_dropdown_addnew_row').hide();
                $('.booking_frequency_dropdown_addnew').prop('required', false);
            }
        });
        
      
    });
    $('#openModalButton').click(function() {
            $('#addNewItemModal').modal('show');
        });

        // Close the modal
        $('#closeModalButton').click(function() {
            $('#addNewItemModal').modal('hide');
        });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
    var stripe = Stripe(localStorage.getItem('stripe_public_key'));
    var elements = stripe.elements();
    var $form = $("#strip_payment_booking");
    var $paymentSection = $('#paymentSection');
    var $firstButton = $('#first');
    var $secondButton = $('#second');

    var style = {
        hidePostalCode: true,
        base: {
            fontSize: '16px',
            color: '#32325d',
        },
    };

    var card = elements.create('card', {hidePostalCode: true, style: style});
    card.mount('#card-element');

    card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    $('#first').click(function(event) {
        event.preventDefault();
        var paymentGateway = localStorage.getItem('payment_gateway') === 'true';
        if (paymentGateway) {
            $paymentSection.show();
            $firstButton.hide();
            $secondButton.show();
        } else {
            $('#paymentSection').hide();
            var form = document.getElementById('strip_payment_booking');
            form.submit();
        }
    });

    $('#second').click(function(event) {
        var depositeOverride = localStorage.getItem('depositeOverride') === 'true';
        if(!depositeOverride){
        event.preventDefault(); // Ensure default form submission is prevented
        console.log('Second button clicked');
        stripe.createToken(card).then(function(result) {
            console.log('Stripe token creation result:', result);
            if (result.error) {
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                console.log('Stripe token:', result.token.id);
                var form = document.getElementById('strip_payment_booking');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', result.token.id);
                form.appendChild(hiddenInput);
                var currencyInput = document.createElement('input');
                    currencyInput.setAttribute('type', 'hidden');
                    currencyInput.setAttribute('name', 'currency');
                    currencyInput.setAttribute('value', 'AUD');
                    form.appendChild(currencyInput);
                form.submit();
            }
        }).catch(function(error) {
            console.error('Error creating Stripe token:', error);
        });
    }
    else{
        var form = document.getElementById('strip_payment_booking');
        form.submit();
    }
    });
});

</script>
<script>
    document.getElementById('depositeOverride').addEventListener('change', function() {
        var paymentSection = document.getElementById('paymentSection');
        var first = document.getElementById('first');
        var second = document.getElementById('second');
        if (this.checked) {
            paymentSection.style.display = 'none';
            second.style.display = 'block';
            localStorage.setItem('depositeOverride', true);
        } else {
            paymentSection.style.display = 'block';
            first.style.display = 'none';

        }
    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/bookings.blade.php ENDPATH**/ ?>