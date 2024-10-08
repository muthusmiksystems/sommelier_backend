@extends('admin.layouts.master')
@section("title") {{__('storeDashboard.bpePageTitle')}}
@endsection
@section('content')
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
    .shift_label_text{
        display:block;
        width:100%;
        font-weight:600;
    }
    .booking_cur_time_text{
        display: inline-block;
        padding: 5px;
        border: 1px solid;
        border-radius: 50px;
        margin-right: 10px;
        margin-bottom: 10px;
        color: #fff;
        background-color: #8360c3;
        border-color: #8360c3;
    }
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2">{{__('storeDashboard.bpeEditing')}}</span>
                <span class="badge badge-primary badge-pill animated flipInX">"{{ $booking->first_name." ".$booking->last_name }}"</span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('restaurant.updateBooking') }}" method="POST" enctype="multipart/form-data">
                    <legend class="font-weight-semibold text-uppercase font-size-sm">
                        <i class="icon-address-book mr-2"></i> {{__('storeDashboard.bpeBookingDetails')}}
                    </legend>
                    <input type="hidden" name="id" value="{{ $booking->id }}">

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>{{__('storeDashboard.bpmLabelBookingRestaurant')}}:</label>
                        <div class="col-lg-9">
                            <select class="form-control select-search booking_store_select" id="booking_store_select" name="restaurant_id" required>
                                <option value="">Please select a store</option>
                                @foreach ($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" class="text-capitalize" @if($booking->restaurant_id
                                    == $restaurant->id) selected="selected" @endif>{{ $restaurant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>{{__('storeDashboard.bpmLabelName')}}</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="no_of_seats" placeholder="No of seats" value="{{ $booking->no_of_seats }}"
                                required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>{{__('storeDashboard.bpmTableTiming')}}</label>
                        <div class="col-lg-9">
                        <i class="fa fa-calendar"></i>
                        <input type="date" class="form-control pull-right" id="booking_date_input" formControlName="timing" name="booking_date" value="{{ date('Y-m-d', strtotime($booking->booking_datetime)) }}" required> 
                        </div>
                    </div>

                    <div class="form-group row booking_timing_block" style="display:none;">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Current Booking Time</label>
                        <div class="col-lg-9">
                            <span class="booking_cur_time_btn_block">
                                <span class="booking_cur_time_text">
                                    {{ date('h:i A', strtotime($booking->booking_datetime)) }}
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group row booking_timing_block" style="display:none;">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>{{__('storeDashboard.bpmLableTiming')}}</label>
                        <div class="col-lg-9">
                            <div class="booking_time_btn"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Mobile Number</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="mobile_number" placeholder="Enter mobile number" value="{{ $booking->booking_mobile }}"
                                required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Email Address</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="email_address" placeholder="Enter the email address" value="{{ $booking->user->email }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>First Name</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="first_name" placeholder="Enter the First name" value="{{ $booking->booking_firstname }}" required>
                        </div>
                    </div>
                   
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label"><span class="text-danger">*</span>Last Name</label>
                        <div class="col-lg-9">
                            <input type="text" class="form-control form-control-lg" name="last_name" placeholder="Enter the Last name" value="{{ $booking->booking_lastname }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Date of Birth</label>
                        <div class="col-lg-9">
                        <i class="fa fa-calendar"></i>
                        <input type="date" class="form-control pull-right" name="dob" formControlName="dob" placeholder="DD-MM-YYYY"
                           id="dateOfBirth" value="@if($booking->user->dob){{ date('Y-m-d', strtotime($booking->user->dob)) }}@endif">
                        </div>
                    </div>
                   
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Comment</label>
                        <div class="col-lg-9">
                        <textarea class="form-control" name="comment" placeholder="{{__('storeDashboard.bpmPhComment')}}"
                        style="resize:none;">{{ $booking->comments }}</textarea> 
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Tables</label>
                        <div class="col-lg-9">
                            @if($tables_info->isNotEmpty())
                                @foreach($tables_info as $table)
                                    @if($table->bookings->isEmpty())
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="selected_table[]" value="{{ $table->id }}">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                 {{ $table->table_number }} (Seats= {{$table->total_seats }})
                                            </label>
                                        </div>
                                    @else
                                        @foreach($table->bookings as $booking_v)
                                            @if(strtotime(date('H:i', strtotime($selected_booking->booking_datetime))) != strtotime(date('H:i', strtotime($booking_v->booking_datetime))))
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="selected_table[]" value="{{ $table->id }}">
                                                    <label class="form-check-label" for="flexRadioDefault1">
                                                         {{ $table->table_number }} (Seats= {{$table->total_seats }})
                                                    </label>
                                                </div>
                                            @elseif($selected_booking->id == $booking_v->id)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="selected_table[]" value="{{ $table->id }}" checked="checked">
                                                    <label class="form-check-label" for="flexRadioDefault1">
                                                         {{ $table->table_number }} (Seats= {{$table->total_seats }})
                                                    </label>
                                                </div>
                                            @endif
                                        @endforeach
                                    @endif 
                                @endforeach
                            @endif
                        </div>
                    </div>
                    
                                 
                    
                    @csrf
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                        {{__('storeDashboard.update')}}
                            <i class="icon-database-insert ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    
    $(function () {
    
        $('.select').select2({
            minimumResultsForSearch: Infinity,
        });

        store_id = jQuery('.booking_store_select').val();
        booking_date = jQuery('#booking_date_input').val();
        if(store_id != null && store_id != "" && booking_date != null && booking_date != ""){
        $.ajax({
            type:'post',
            url:'{!! url('store-owner/store/shift-timing') !!}/'+store_id,
            data:{_token: "{{ csrf_token() }}", booking_date:booking_date, time: "{{ strtotime(date('h:i A', strtotime($booking->booking_datetime))) }}" },
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



        $('#booking_date_input').change(function(){
            booking_date = jQuery(this).val();
             store_id = jQuery("#booking_store_select").val();
             if(store_id != null && store_id != "" && booking_date != null && booking_date != ""){
                $.ajax({
                    type:'post',
                    url:'{!! url('store-owner/store/shift-timing') !!}/'+store_id,
                   data:{_token: "{{ csrf_token() }}", booking_date: booking_date},
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

    });
</script>
@endsection