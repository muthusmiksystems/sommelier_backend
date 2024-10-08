@extends('admin.layouts.master')
@section("title") {{__('storeDashboard.tableShiftTitle')}}
@endsection
@section('content')
<style>
.removeTable{
    margin-top:38px;
}
</style>
<div class="page-header">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-circle-right2 mr-2"></i>
                <span class="font-weight-bold mr-2">{{__('storeDashboard.tableShiftHeading')}}</span>
            </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>
</div>
<div class="content">
    <div class="col-md-12">
        <div class="card" style="min-height: 100vh;">
            <div class="card-body">
                <form action="{{ route('admin.saveRestaurantTableShift') }}" method="POST" enctype="multipart/form-data">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary btn-labeled btn-labeled-left btn-lg" name="action" value="save">
                        <b><i class="icon-database-insert ml-1"></i></b>
                        {{__('storeDashboard.tableShiftSaveSettings')}}
                        </button>
                    </div>
                    <div class="d-lg-flex justify-content-lg-left">
                        <ul class="nav nav-pills flex-column mr-lg-3 wmin-lg-250 mb-lg-0">
                            <li class="nav-item">
                                <a href="#shiftinformation" class="nav-link active" data-toggle="tab">
                                <i class="icon-history mr-2"></i>
                                {{__('storeDashboard.tableShiftInfo')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tablesetups" class="nav-link" data-toggle="tab">
                                <i class="icon-tree5 mr-2"></i>
                                {{__('storeDashboard.tableSetupTitle')}}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#additionalinformation" class="nav-link" data-toggle="tab">
                                <i class="icon-plus-circle2 mr-2"></i>
                                {{__('storeDashboard.tableAdditionalInfo')}}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="width: 100%; padding: 0 25px;">
                            <div class="tab-pane fade show active" id="shiftinformation">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{__('storeDashboard.tableShiftInfo')}}
                                </legend>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-header">
                                                Breakfast<span class="text-danger">*</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Start Time:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input id="input_starttime" type="text" class="form-control form-control-lg input_starttime" name="breakfast_startTime"
                                                                value="{{ (!empty($shif_settings->breakfastStartTime)) ? $shif_settings->breakfastStartTime : '' }}" placeholder="Start Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>End Time:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg input_starttime" name="breakfast_endTime"
                                                                value="{{ (!empty($shif_settings->breakfastEndTime)) ? $shif_settings->breakfastEndTime : '' }}" placeholder="End Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Standard Duration:</strong></label>
                                                        <div class="col-lg-12">
                                                            <select name="breakfast_duration" class="form-control form-control-lg select">
                                                                <option value="15" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "15" )
                                                            selected @endif>15 Minutes</option>
                                                                <option value="30" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "30" )
                                                                selected @endif>30 Minutes</option>
                                                                <option value="45" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "45" )
                                                                selected @endif>45 Minutes</option>
                                                                <option value="60" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "60" )
                                                                selected @endif>60 Minutes</option>
                                                                <option value="75" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "75" )
                                                                selected @endif>75 Minutes</option>
                                                                <option value="90" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "90" )
                                                                selected @endif>90 Minutes</option>
                                                                <option value="105" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "105" )
                                                                selected @endif>105 Minutes</option>
                                                                <option value="120" @if( isset($shif_settings->breakfastDuration) && $shif_settings->breakfastDuration == "120" )
                                                                selected @endif>120 Minutes</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Max Cover:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg" name="max_cover_breakfast"
                                                                value="{{ (!empty($shif_settings->max_cover_breakfast)) ? $shif_settings->max_cover_breakfast : '' }}" placeholder="Max Cover">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                Lunch<span class="text-danger">*</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Start Time:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg input_starttime" name="lunch_start_time"
                                                                value="{{ (!empty($shif_settings->lunchStartTime)) ? $shif_settings->lunchStartTime : '' }}" placeholder="Start Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>End Time:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg input_starttime" name="lunch_end_time"
                                                                value="{{ (!empty($shif_settings->lunchEndTime)) ? $shif_settings->lunchEndTime : '' }}" placeholder="End Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Standard Duration:</strong></label>
                                                        <div class="col-lg-12">
                                                            <select name="lunch_duration" class="form-control form-control-lg select">
                                                                <option value="15" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "15" )
                                                            selected @endif>15 Minutes</option>
                                                                <option value="30" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "30" )
                                                                selected @endif>30 Minutes</option>
                                                                <option value="45" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "45" )
                                                                selected @endif>45 Minutes</option>
                                                                <option value="60" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "60" )
                                                                selected @endif>60 Minutes</option>
                                                                <option value="75" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "75" )
                                                                selected @endif>75 Minutes</option>
                                                                <option value="90" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "90" )
                                                                selected @endif>90 Minutes</option>
                                                                <option value="105" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "105" )
                                                                selected @endif>105 Minutes</option>
                                                                <option value="120" @if( isset($shif_settings->lunchDuration) && $shif_settings->lunchDuration == "120" )
                                                                selected @endif>120 Minutes</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Max Cover:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg" name="max_cover_lunch"
                                                                value="{{ (!empty($shif_settings->max_cover_lunch)) ? $shif_settings->max_cover_lunch : '' }}" placeholder="Max Cover">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header">
                                                Dinner<span class="text-danger">*</span>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Start Time:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg input_starttime" name="dinner_start_time"
                                                                value="{{ (!empty($shif_settings->dinnerStartTime)) ? $shif_settings->dinnerStartTime : '' }}" placeholder="Start Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>End Time:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg input_starttime" name="dinner_end_time"
                                                                value="{{ (!empty($shif_settings->dinnerEndTime)) ? $shif_settings->dinnerEndTime : '' }}" placeholder="End Time">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Standard Duration:</strong></label>
                                                        <div class="col-lg-12">
                                                            <select name="dinner_duration" class="form-control form-control-lg select">
                                                                <option value="15" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "15" )
                                                            selected @endif>15 Minutes</option>
                                                                <option value="30" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "30" )
                                                                selected @endif>30 Minutes</option>
                                                                <option value="45" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "45" )
                                                                selected @endif>45 Minutes</option>
                                                                <option value="60" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "60" )
                                                                selected @endif>60 Minutes</option>
                                                                <option value="75" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "75" )
                                                                selected @endif>75 Minutes</option>
                                                                <option value="90" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "90" )
                                                                selected @endif>90 Minutes</option>
                                                                <option value="105" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "105" )
                                                                selected @endif>105 Minutes</option>
                                                                <option value="120" @if( isset($shif_settings->dinnerDuration) && $shif_settings->dinnerDuration == "120" )
                                                                selected @endif>120 Minutes</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3">
                                                        <label class="col-lg-12 col-form-label"><strong>Max Cover:</strong></label>
                                                        <div class="col-lg-12">
                                                            <input type="text" class="form-control form-control-lg" name="max_cover_dinner"
                                                                value="{{ (!empty($shif_settings->max_cover_dinner)) ? $shif_settings->max_cover_dinner : '' }}" placeholder="Max Cover">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                                        
                            </div>
                            <div class="tab-pane fade" id="tablesetups">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{__('storeDashboard.tableSetupTitle')}}
                                </legend>
                                <div class="form-group row">
                                    <div class="col-lg-12">
                                        <div class="card">
                                            <div class="card-header">
                                                Add New Table
                                            </div>
                                            <div class="card-body table_info_form_inputs">
                                                @if(count($table_info) > 0)
                                                @php 
                                                    $counter = 0;
                                                @endphp
                                                    @foreach($table_info as $ind => $table)
                                                        <div class="form-group row table_info_row">
                                                            <div class="col-lg-5">
                                                                <label class="col-lg-12 col-form-label"><strong>Table Number<span class="text-danger">*</span></strong></label>
                                                                <div class="col-lg-12">
                                                                    <input type="text" class="form-control form-control-lg" name="table_info[{{ $counter }}][table_number]"
                                                                        value="{{ $table->table_number }}" placeholder="Enter table no">
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-5">
                                                                <label class="col-lg-12 col-form-label"><strong>No. Of Seats<span class="text-danger">*</span></strong></label>
                                                                <div class="col-lg-12">
                                                                    <input type="text" class="form-control form-control-lg" name="table_info[{{ $counter }}][no_of_seats]"
                                                                        value="{{ $table->total_seats }}" placeholder="Enter no of seats">
                                                                    <input type='hidden' class="table_id_hidden" name="table_info[{{ $counter }}][table_info_id]" value="{{ $table->id }}"/>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2 remove_table_row_btn">
                                                                @if($ind != 0)
                                                                    <button type="button" class="btn btn-secondary removeTable"><b><i class="icon-minus2"></i></b></button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        @php 
                                                            $counter++ 
                                                        @endphp
                                                    @endforeach
                                                @else
                                                    <div class="form-group row table_info_row">
                                                        <div class="col-lg-5">
                                                            <label class="col-lg-12 col-form-label"><strong>Table Number<span class="text-danger">*</span></strong></label>
                                                            <div class="col-lg-12">
                                                                <input type="text" class="form-control form-control-lg" name="table_info[0][table_number]"
                                                                    value="" placeholder="Enter table no">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <label class="col-lg-12 col-form-label"><strong>No. Of Seats<span class="text-danger">*</span></strong></label>
                                                            <div class="col-lg-12">
                                                                <input type="text" class="form-control form-control-lg" name="table_info[0][no_of_seats]"
                                                                    value="" placeholder="Enter no of seats">
                                                                <input type='hidden' class="table_id_hidden" name="table_info[0][table_info_id]" value=""/>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 remove_table_row_btn">
                                                            
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="card-footer">
                                                <div class="">
                                                    <button type="button" class="btn btn-secondary" id="addNewTable"><b><i class="icon-plus2"></i></b></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- tablesetups -->
                            <div class="tab-pane fade" id="additionalinformation">
                                <legend class="font-weight-semibold text-uppercase font-size-sm">
                                    {{__('storeDashboard.tableAdditionalInfo')}}
                                </legend>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong><span class="text-danger">*</span>Max No of Pax:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control colorpicker-show-input"
                                            name="max_no_of_cover" data-preferred-format="rgb"
                                            value="{{ (!empty($shif_settings->maxNoOfCover)) ? $shif_settings->maxNoOfCover : '' }}" placeholder="Enter max no of cover">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong><span class="text-danger">*</span>Email From:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control colorpicker-show-input"
                                            name="email_from" data-preferred-format="rgb"
                                            value="{{ (!empty($shif_settings->emailFrom)) ? $shif_settings->emailFrom : '' }}" placeholder="Enter email from">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong><span class="text-danger">*</span>Team Name:</strong></label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control colorpicker-show-input"
                                            name="team_name" data-preferred-format="rgb"
                                            value="{{ (!empty($shif_settings->teamName)) ? $shif_settings->teamName : '' }}" placeholder="Enter team name">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label"><strong><span class="text-danger">*</span>Email Options:</strong></label>
                                    <div class="col-lg-9">
                                        <select name="email_options" class="form-control form-control-lg select">
                                            <option value="">Select</option>
                                            <option value="1" @if( isset($shif_settings->email_options) && $shif_settings->email_options == "1" )
                                        selected @endif>Web bookings</option>
                                            <option value="2" @if( isset($shif_settings->email_options) && $shif_settings->email_options == "2" )
                                            selected @endif>App bookings</option>
                                            <option value="3" @if( isset($shif_settings->email_options) && $shif_settings->email_options == "3" )
                                            selected @endif>Both Web & App bookings</option>
                                            <option value="0" @if( isset($shif_settings->email_options) && $shif_settings->email_options == "0" )
                                            selected @endif>No Mail</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div><!-- tab-content -->
                        
                    </div>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf">
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant_id }}"/>
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
        //jQuery('#input_starttime').pickatime({});
        jQuery('.input_starttime').bootstrapMaterialDatePicker({
            date: false,
            format: 'HH:mm'
        }); 
        jQuery(document).on('click', '#addNewTable', function(){
            jQuery('.table_info_form_inputs .table_info_row')
            .last()
            .clone()
            .appendTo(jQuery('.table_info_form_inputs'))
            .find("input").attr("name",function(i,oldVal) {
                return oldVal.replace(/\[(\d+)\]/,function(_,m){
                    return "[" + (+m + 1) + "]";
                });
            });  
            jQuery('.table_info_form_inputs .table_info_row').last().find('.table_id_hidden').val('');
            jQuery('.table_info_form_inputs .table_info_row').last().find('.remove_table_row_btn').html('<button type="button" class="btn btn-secondary removeTable"><b><i class="icon-minus2"></i></b></button>');
        });

        jQuery(document).on('click', '.removeTable', function(){
            jQuery(this).parents('.table_info_form_inputs .table_info_row').remove();
        });
    });
</script>
@endsection