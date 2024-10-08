
<?php $__env->startSection("title"); ?> <?php echo e(__('storeDashboard.aspTitle')); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
    .remaining_seats_block{
        display: flex;
        align-items: center;
        float:right;
    }
    .total_seats_selection_remaining{
        display:block;
        float:right;
        color:#fff;
        background-color:#2ebf91;
        width:35px;
        height:35px;
        line-height:35px;
        border-radius:50%;
        text-align:center;
        font-size:16px;
        font-weight:600;
        margin-left:15px;
    }
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <?php if(empty($query)): ?>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <?php else: ?>
                <span class="font-weight-bold mr-2"><?php echo e(__('storeDashboard.total')); ?></span>
                <span class="font-weight-bold mr-2">Results for "<?php echo e($query); ?>"</span>
                <?php endif; ?>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
        <div class="header-elements d-none py-0 mb-3 mb-md-0">
            <div class="breadcrumb">
                <!--<button type="button" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewItem"
                    data-toggle="modal" data-target="#addNewItemModal">
                    <b><i class="icon-plus2"></i></b>
                    <?php echo e(__('storeDashboard.bpAddNewBookingmBtn')); ?>

                </button> -->
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

   <?php /*  <form action="{{ route('restaurant.post.searchBooking') }}" method="GET">
        <div class="form-group form-group-feedback form-group-feedback-right search-box">
            <input type="text" class="form-control form-control-lg search-input" placeholder="{{__('storeDashboard.bpSearchPH')}}"
                name="query">
            <div class="form-control-feedback form-control-feedback-lg">
                <i class="icon-search4"></i>
            </div>
        </div>
        @csrf
    </form>  */ ?>

    <form action="<?php echo e(route('restaurant.assignTable')); ?>" method="GET">
        <div class="form-group row">
            <div class="col-lg-3">
                <select name="resturant" class="form-control">
                    <?php $__currentLoopData = $restaurants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $restaurant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(isset($restaurant->restaurantSettings->sommelier_reservations) && $restaurant->restaurantSettings->sommelier_reservations == 'yes'): ?>
                            <option value="<?php echo e($restaurant->id); ?>" class="text-capitalize" <?php echo e(($restaurant->id == $restaurant_id) ? "selected='selected'" : ""); ?>><?php echo e($restaurant->name); ?></option>
                    <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-lg-3">
                <input type="date" class="form-control" name="booking_date" value="<?php echo e((!empty($search_date)) ? $search_date : date('Y-m-d')); ?>"/>
            </div>
            <div class="col-lg-3">
                <select name="meal_type" class="form-control">
                    <option value="all" <?php echo e(($meal_type == "all") ? 'selected="selected"' : ""); ?>>All</option>
                    <option value="Breakfast" <?php echo e(($meal_type == "Breakfast") ? 'selected="selected"' : ""); ?>>Breakfast</option>
                    <option value="Lunch" <?php echo e(($meal_type == "Lunch") ? 'selected="selected"' : ""); ?>>Lunch</option>
                    <option value="Dinner" <?php echo e(($meal_type == "Dinner") ? 'selected="selected"' : ""); ?>>Dinner</option>
                </select>
            </div>
            <div class="col-lg-3">
                <button type="submit" class="btn btn-secondary btn-labeled btn-labeled-left mr-2" id="addNewItem"
                    data-toggle="modal" data-target="#addNewItemModal">
                    <b><i class="icon-search4"></i></b>
                    <?php echo e(__('storeDashboard.aspSearchButton')); ?>

                </button>
            </div>
        </div>
        <!-- <?php echo csrf_field(); ?> -->
    </form>

    <form action="<?php echo e(route('restaurant.assignTableToBooking')); ?>" method="POST">
    <?php echo csrf_field(); ?>
        <div class="form-group row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">List of Bookings</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if(!empty($bookings)): ?>
                                <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="form-check">
                                        <input class="form-check-input booking_selection_input" type="radio" name="booking_id" data-restaurant-id="<?php echo e($booking->restaurant_id); ?>" value="<?php echo e($booking->id); ?>">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            <?php echo e("#".$booking->unique_booking_id); ?> (<?php echo e($booking->user->name); ?>, No of Person=<?php echo e($booking->no_of_seats); ?>, <?php echo e(date('Y-m-d h:i A', strtotime($booking->booking_datetime))); ?> )
                                        </label>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">List of Tables <span class="remaining_seats_block"> Remaining Seats: <span class="total_seats_selection_remaining" data-seats="0" data-total_tables="0">0</span></span></div>
                    <div class="card-body">
                        <div class="table-responsive available_tables_output">
                            <span style="color:red;">Please choose booking first.</span>
                           <!-- <?php if(!empty($tables_info)): ?>
                                <?php $__currentLoopData = $tables_info; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $table): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($table->bookings->isEmpty()): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="selected_table[]" value="<?php echo e($table->id); ?>">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                Table <?php echo e($table->table_number); ?> (Seats= <?php echo e($table->total_seats); ?>)
                                            </label>
                                        </div>
                                    <?php endif; ?> 
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-right" >
            <button type="submit" class="btn btn-primary">
            <?php echo e(__('storeDashboard.save')); ?>

                <i class="icon-database-insert ml-1"></i></button>
        </div>
    </form>
</div>
<script>
    $(function () {
        $('.booking_selection_input').click(function(){
             booking_id = jQuery(this).val();
             restaurant_id = jQuery(this).attr('data-restaurant-id');
             if((booking_id != null && booking_id != "") && (restaurant_id != null && restaurant_id != "")){
                $.ajax({
                    type:'post',
                    url:'/public/store-owner/store/get-available-tables',
                   data:{booking_id: booking_id, restaurant_id: restaurant_id, _token: "<?php echo e(csrf_token()); ?>"},
                    success:function(data) {
                        if(data.success){
                            $(".available_tables_output").html(data.html);
                            $(".total_seats_selection_remaining").text(data.no_of_persons).attr('data-seats', data.no_of_persons);
                        }else{
                            $(".available_tables_output").html(data.html);
                            $(".total_seats_selection_remaining").text(0).attr('data-seats', 0);
                        }
                        
                    }
                    });
             }
         });

         $(document).on('click', '.available_tables_output input', function(){
                no_of_persons = $(".total_seats_selection_remaining").attr('data-seats');
                counter = 1;
                total_seats = 0;
                remaining = 0;
                $(".available_tables_output input:checkbox:checked").each(function(){
                    total_seats =parseInt($(this).attr('data-seats'))+total_seats;
                    if(counter == 1)
                    remaining= parseInt(no_of_persons)-total_seats;
                    
                    if(counter >= 2){
                        total_seats=  total_seats-2;
                    remaining = parseInt(no_of_persons)-(total_seats);
                    }

                    counter++;
                    
                });
                //console.log(remaining);
                if(counter > 1)
                $(".total_seats_selection_remaining").text(remaining);
                else
                $(".total_seats_selection_remaining").text(no_of_persons);
           /* no_of_tables =$('.total_seats_selection_remaining').attr('data-total_tables');
            if($(this).prop('checked') == true)
                update_no_of_tables =(parseInt(no_of_tables)+1);
            else    
                update_no_of_tables =(parseInt(no_of_tables)-1);
             $('.total_seats_selection_remaining').attr('data-total_tables', update_no_of_tables)
            
            seats =$(this).attr('data-seats');
            remaining_seats = 0;

            if(update_no_of_tables == 1)
                remaining_seats = parseInt(no_of_persons)- parseInt(seats);
            else if(update_no_of_tables >= 2)
                remaining_seats = (parseInt(no_of_persons)-(parseInt(update_no_of_tables)*2))- parseInt(seats);
            
            $(".total_seats_selection_remaining").text(remaining_seats);*/
         });
    });
</script>


<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/assignTable.blade.php ENDPATH**/ ?>