
<?php $__env->startSection("title"); ?> Store Settings - Dashboard
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<style>
.removeTable{
    margin-top:38px;
}
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2">Store Settings</span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="col-md-12">
        <div class="card" style="min-height: 100vh;">
            <div class="card-body">
                <form action="<?php echo e(route('restaurant.saveRestaurantSettings')); ?>" method="POST" enctype="multipart/form-data">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg" name="action" value="save">
                        <b><i class="icon-database-insert ml-1"></i></b>
                        Save Settings
                        </button>
                    </div>
                    <div class="d-lg-flex justify-content-lg-left">
                        <ul class="nav nav-pills flex-column mr-lg-3 wmin-lg-250 mb-lg-0">
                            <li class="nav-item">
                                <a href="#bepoz_settings" class="nav-link active" data-toggle="tab">
                                <i class="icon-gear mr-2"></i>
                                POS Settings
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#confirm_email_rec" class="nav-link" data-toggle="tab">
                                <i class="icon-envelop3 mr-2"></i>
                                Confirmaton email
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#holidays" class="nav-link" data-toggle="tab">
                                <i class="icon-close2 mr-2"></i>
                                Holidays
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#deposits" class="nav-link" data-toggle="tab">
                                <i class="icon-coin-dollar mr-2"></i>
                                Deposits
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="width: 100%; padding: 0 25px;">
                            <div class="tab-pane fade show active" id="bepoz_settings">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                   POS Settings
                                </legend>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>POS:</strong></label>
                                    <div class="col-lg-9">
                                        <select name="pos_type" class="form-control form-control-lg select">
                                        <option value="None" <?php if(isset($restaurant_settings->pos_type) && $restaurant_settings->pos_type == "None"): ?> selected <?php endif; ?>>-- select --</option>
                                        <option value="Bepoz" <?php if(isset($restaurant_settings->pos_type) && $restaurant_settings->pos_type == "Bepoz"): ?> selected <?php endif; ?>>Bepoz</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>URL:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="bepoz_url"
                                            value="<?php echo e((isset($restaurant_settings->url)) ? $restaurant_settings->url : ''); ?>" placeholder="Enter Bepoz URL">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Secret:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="bepoz_secret"
                                            value="<?php echo e((isset($restaurant_settings->secret)) ? $restaurant_settings->secret : ''); ?>"
                                            placeholder="Enter Secret">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Till ID:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="bepoz_till_id"
                                            value="<?php echo e((isset($restaurant_settings->till_id)) ? $restaurant_settings->till_id : ''); ?>"
                                            placeholder="Enter Till ID">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Operator ID:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="bepoz_operator_id"
                                            value="<?php echo e((isset($restaurant_settings->operator_id)) ? $restaurant_settings->operator_id : ''); ?>"
                                            placeholder="Enter Operator ID">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Offline Payment:</strong></label>
                                    <div class="col-lg-9">
                                        <select name="bepoz_offiline_pay" class="form-control form-control-lg select">
                                            <option value="cash" <?php if( isset($restaurant_settings->online_payment) && $restaurant_settings->online_payment == "cash" ): ?>
                                        selected <?php endif; ?>>Cash</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Online Payment-:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg" name="bepoz_online_payment"
                                            value="<?php echo e((isset($restaurant_settings->online_payment)) ? $restaurant_settings->online_payment : ''); ?>"
                                            placeholder="Online Payment">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Table/Account:</strong></label>
                                    <div class="col-lg-9">
                                    <select name="bepoz_booking_option" class="form-control form-control-lg select">
                                            <option value="Table" <?php if( isset($restaurant_settings->booking_option) && $restaurant_settings->booking_option == "Table" ): ?>
                                        selected <?php endif; ?>>Booking Table</option>
                                        <option value="Account" <?php if( isset($restaurant_settings->booking_option) && $restaurant_settings->booking_option == "Account" ): ?>
                                        selected <?php endif; ?>>Booking Account</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking date Index:</strong></label>
                                    <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="bepoz_booking_custom_date_fieldidx"
                                           value="<?php echo e((isset($restaurant_settings->booking_custom_date_fieldidx)) ? $restaurant_settings->booking_custom_date_fieldidx : ''); ?>"
                                            placeholder="Custom booking date">
                                    </div>
                                </div>
    
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Pax Index:</strong></label>
                                    <div class="col-lg-9">
                                    <input type="number" class="form-control form-control-lg" name="bepoz_booking_pax_fieldidx"
                                            value="<?php echo e((isset($restaurant_settings->booking_pax_fieldidx)) ? $restaurant_settings->booking_pax_fieldidx : ''); ?>"
                                            placeholder="Custom Booking Pax field index">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Name Index:</strong></label>
                                    <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="bepoz_booking_name_fieldidx"
                                            value="<?php echo e((isset($restaurant_settings->booking_name_fieldidx)) ? $restaurant_settings->booking_name_fieldidx : ''); ?>"
                                            placeholder="Custom Booking Name index">
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Comment Index:</strong></label>
                                    <div class="col-lg-9">
                                    <input type="text" class="form-control form-control-lg" name="bepoz_booking_comment_fieldidx"
                                            value="<?php echo e((isset($restaurant_settings->booking_comment_fieldidx)) ? $restaurant_settings->booking_comment_fieldidx : ''); ?>"
                                            placeholder="Custom Booking Comment index">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Number Index:</strong></label>
                                    <div class="col-lg-9">
                                    <input type="number" class="form-control form-control-lg" name="bepoz_booking_number_fieldidx"
                                            value="<?php echo e((isset($restaurant_settings->booking_number_fieldidx)) ? $restaurant_settings->booking_number_fieldidx : ''); ?>"
                                            placeholder="Custom booking number">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Delivery PLU: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_delivery_plu"
                                            value="<?php echo e((isset($restaurant_settings->delivery_plu)) ? $restaurant_settings->delivery_plu : ''); ?>"
                                            placeholder="Delivery PLU">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Discount PLU: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_discount_plu"
                                            value="<?php echo e((isset($restaurant_settings->discount_plu)) ? $restaurant_settings->discount_plu : ''); ?>"
                                            placeholder="Discount PLU">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Surcharge PLU: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_surcharge_plu"
                                            value="<?php echo e((isset($restaurant_settings->surcharge_plu)) ? $restaurant_settings->surcharge_plu : ''); ?>"
                                            placeholder="Surcharge PLU">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Tip PLU: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_tip_plu"
                                            value="<?php echo e((isset($restaurant_settings->tip_plu)) ? $restaurant_settings->tip_plu : ''); ?>"
                                            placeholder="Tip PLU">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking PLU: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_booking_plu"
                                            value="<?php echo e((isset($restaurant_settings->booking_plu)) ? $restaurant_settings->booking_plu : ''); ?>"
                                            placeholder="Booking PLU">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Table Group: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control form-control-lg min-payout" name="bepoz_table_group"
                                            value="<?php echo e((isset($restaurant_settings->table_group)) ? $restaurant_settings->table_group : ''); ?>"
                                            placeholder="Booking Table Group">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Order Table Group: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control form-control-lg min-payout" name="bepoz_order_table_group"
                                            value="<?php echo e((isset($restaurant_settings->order_table_group)) ? $restaurant_settings->order_table_group : ''); ?>"
                                            placeholder="Order Table Group">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Self Pickup Order Type: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control form-control-lg min-payout" name="bepoz_self_pickup_order_type"
                                            value="<?php echo e((isset($restaurant_settings->self_pickup_order_type)) ? $restaurant_settings->self_pickup_order_type : ''); ?>"
                                            placeholder="Self Pickup Order Type">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Delivery Order Type: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="number" class="form-control form-control-lg min-payout" name="bepoz_delivery_order_type"
                                            value="<?php echo e((isset($restaurant_settings->delivery_order_type)) ? $restaurant_settings->delivery_order_type : ''); ?>"
                                            placeholder="Delivery Order Type">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Order Account Group: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_account_group"
                                            value="<?php echo e((isset($restaurant_settings->account_group)) ? $restaurant_settings->account_group : ''); ?>"
                                            placeholder="Order Account Group">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Loyalty Account Group: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_loyalty_account_group"
                                            value="<?php echo e((isset($restaurant_settings->account_group)) ? $restaurant_settings->account_group : ''); ?>"
                                            placeholder="Loyalty Account Group">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Booking Account Group: </strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control form-control-lg min-payout" name="bepoz_booking_account_group"
                                            value="<?php echo e((isset($restaurant_settings->account_group)) ? $restaurant_settings->account_group : ''); ?>"
                                            placeholder="Booking Account Group">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-lg-9">
                                        <button type="submit" class="btn btn-primary btn-labeled btn-lg" formaction="<?php echo e(route('restaurant.checkBepozConnection')); ?>" name="action" value="check_connection">CHECK CONNECTION</button>
                                    </div>
                                </div>
                                
                                                               
                            </div>
                            <div class="tab-pane fade" id="confirm_email_rec">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    Confirmation email recipients
                                </legend>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Recipient Email:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control colorpicker-show-input"
                                            name="bepoz_recipient_email" data-preferred-format="rgb"
                                            value="<?php echo e((isset($restaurant_settings->recipient_email)) ? $restaurant_settings->recipient_email : ''); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="holidays">
                                <div class="holidays_block">
                                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                                        Holidays
                                    </legend>
                                    <?php if(!empty($holidays)): ?>
                                        <?php
                                            $counter = 0;
                                        ?>
                                        <?php $__currentLoopData = $holidays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="form-group row holiday_row">
                                                <div class="col-lg-2">
                                                    <label class="col-lg-12 col-form-label"><strong>Occasion</strong></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control colorpicker-show-input sdfasdfdfa" name="holidays[<?php echo e($counter); ?>][occasion]" data-preferred-format="rgb" value="<?php echo e($holiday['occasion']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label class="col-lg-12 col-form-label"><strong>Date</strong></label>
                                                    <div class="col-lg-12">
                                                        <input type="date" class="form-control colorpicker-show-input" name="holidays[<?php echo e($counter); ?>][date]" data-preferred-format="rgb" value="<?php echo e($holiday['date']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label class="col-lg-12 col-form-label"><strong>Deposit Covers</strong></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control colorpicker-show-input" name="holidays[<?php echo e($counter); ?>][holiday_deposit_covers]" data-preferred-format="rgb" value="<?php echo e($holiday['holiday_deposit_covers']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label class="col-lg-12 col-form-label"><strong>Deposit Amount</strong></label>
                                                    <div class="col-lg-12">
                                                        <input type="text" class="form-control colorpicker-show-input" name="holidays[<?php echo e($counter); ?>][holiday_deposit_amount]" data-preferred-format="rgb" value="<?php echo e($holiday['holiday_deposit_amount']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <label class="col-lg-12 col-form-label"><strong>Open:</strong></label>
                                                    <div class="col-lg-12">
                                                        <div class="checkbox checkbox-switchery mt-2">
                                                            <label>
                                                                <input value="true" type="checkbox" class="switchery-primary"
                                                                <?php if($holiday['enable_holiday_deposit']): ?> checked="checked" <?php endif; ?>  name="holidays[<?php echo e($counter); ?>][enable_holiday_deposit]">
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 remove_table_row_btn">
                                                    <?php if($index != 0): ?>
                                                        <button type="button" class="btn btn-secondary removeTable"><b><i class="icon-minus2"></i></b></button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php 
                                                $counter++ 
                                            ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        <div class="form-group row holiday_row">
                                            <div class="col-lg-2">
                                                <label class="col-lg-12 col-form-label"><strong>Occasion</strong></label>
                                                <div class="col-lg-12">
                                                    <input type="text" class="form-control colorpicker-show-input" name="holidays[0][occasion]" data-preferred-format="rgb" value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="col-lg-12 col-form-label"><strong>Date</strong></label>
                                                <div class="col-lg-12">
                                                    <input type="date" class="form-control colorpicker-show-input" name="holidays[0][date]" data-preferred-format="rgb" value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="col-lg-12 col-form-label"><strong>Deposit Covers</strong></label>
                                                <div class="col-lg-12">
                                                    <input type="text" class="form-control colorpicker-show-input" name="holidays[0][holiday_deposit_covers]" data-preferred-format="rgb" value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="col-lg-12 col-form-label"><strong>Deposit Amount</strong></label>
                                                <div class="col-lg-12">
                                                    <input type="text" class="form-control colorpicker-show-input" name="holidays[0][holiday_deposit_amount]" data-preferred-format="rgb" value="">
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="col-lg-12 col-form-label"><strong>Open:</strong></label>
                                                <div class="col-lg-12">
                                                    <div class="checkbox checkbox-switchery mt-2">
                                                        <label>
                                                            <input value="true" type="checkbox" class="switchery-primary"  name="holidays[0][enable_holiday_deposit]">
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-2 remove_table_row_btn"> </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <hr/>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <button type="button" class="btn btn-secondary" id="addNewTable"><b><i class="icon-plus2"></i></b></button>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="deposits">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    Deposit Settings
                                </legend>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Enable Deposit:</strong></label>
                                    <div class="col-lg-9">
                                        <div class="checkbox checkbox-switchery mt-2">
                                            <label>
                                                <input value="true" type="checkbox" class="switchery-primary"
                                                <?php if($restaurant_settings->enable_deposit): ?> checked="checked" <?php endif; ?>  name="enable_deposit">
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Deposit Covers:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control colorpicker-show-input"
                                            name="deposit_covers" data-preferred-format="rgb"
                                            value="<?php echo e((isset($restaurant_settings->deposit_covers)) ? $restaurant_settings->deposit_covers : ''); ?>">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong>Deposit amount per cover:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control colorpicker-show-input"
                                            name="deposit_amount_per_cover" data-preferred-format="rgb"
                                            value="<?php echo e((isset($restaurant_settings->deposit_amount_per_cover)) ? $restaurant_settings->deposit_amount_per_cover : ''); ?>">
                                    </div>
                                </div>

                            </div>
                        </div><!-- tab-content -->
                        
                    </div>
                    <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>" id="csrf">
                    <input type="hidden" name="restaurant_id" value="<?php echo e($restaurant_id); ?>"/>
                    <div class="text-right mt-5">
                    <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg" name="action" value="save">
                    <b><i class="icon-database-insert ml-1"></i></b>
                    Save Settings
                    </button>
                    </div>
                    <input type="hidden" name="window_redirect_hash" value="">
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function(){
        jQuery(document).on('click', '#addNewTable', function(){
            /*clone = jQuery('#holidays .holiday_row')
            .last()
            .clone()
            .appendTo(jQuery('#holidays .holidays_block'))
            .find("input").val('').attr("name",function(i,oldVal) {
                return oldVal.replace(/\[(\d+)\]/,function(_,m){
                    return "[" + (+m + 1) + "]";
                });
            });  */

            ele = jQuery('#holidays .holiday_row').last();
            clone = ele.clone();
            clone.appendTo(jQuery('#holidays .holidays_block'))
            .find("input").val('').attr("name",function(i,oldVal) {
                return oldVal.replace(/\[(\d+)\]/,function(_,m){
                    return "[" + (+m + 1) + "]";
                });
            });

            
            html = clone.find('input.switchery-primary');
            html.val(true);
            html.next().remove();
            var switchery = new Switchery(html[0], { color: '#2196F3' });
            
            //jQuery('#holidays .holiday_row').last().find('.table_id_hidden').val('');
            jQuery('#holidays .holiday_row').last().find('.remove_table_row_btn').html('<button type="button" class="btn btn-secondary removeTable"><b><i class="icon-minus2"></i></b></button>');
        });

        jQuery(document).on('click', '.removeTable', function(){
            jQuery(this).parents('#holidays .holiday_row').remove();
        });
    });

    if (Array.prototype.forEach) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery-primary'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html, { color: '#2196F3' });
        });
    }
    else {
        var elems = document.querySelectorAll('.switchery-primary');
        for (var i = 0; i < elems.length; i++) {
            var switchery = new Switchery(elems[i], { color: '#2196F3' });
        }
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\Sommelier\resources\views/restaurantowner/settingsRestaurant.blade.php ENDPATH**/ ?>