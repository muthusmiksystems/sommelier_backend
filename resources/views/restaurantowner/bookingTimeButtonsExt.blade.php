<style>
    #booking_time_btn{
        margin-bottom: 5px;
        width: 100%;
        max-width: 74%;
        float: left;
        margin-left: 50px;
    }
    .booking_time_btn_block .booking_time_text{
        display: inline-block;
        padding: 5px;
        border: 1px solid;
        border-radius: 50px;
        margin-right: 10px;
        margin-bottom: 10px;
    }
    .booking_time_btn_block .booking_time_text:hover{
        color: #fff;
        background-color: #8360c3;
        border-color:#8360c3;
        cursor: pointer;
    }
    .booking_time_btn_block .booking_time_text.active{
        color: #fff;
        background-color: #8360c3;
        border-color:#8360c3;
    }
    .booking_time_btn_block .booking_time_available{
        color: #fff;
        background-color: #008000;
        border-color:#008000;
    }
    .booking_time_btn_block .booking_time_warning{
        color: #fff;
        background-color: #ffcc00;
        border-color:#ffcc00;
    }
    .booking_time_btn_block .booking_time_not_available{
        color: #fff;
        background-color: #FF0000;
        border-color:#FF0000;
        display:none;
    }
    .shift_label_text{
        display:block;
        width:100%;
        font-weight:600;
        padding:10px 0;
    }
</style>
@foreach($time_buttons as $shift => $booking_time)
    <div class="row">
        <div class="col-lg-12">
            <label class="col-form-label shift_label_text">{{ $shift }}</label>
            @foreach($booking_time as $time)
                <span class="booking_time_btn_block">
                    <span class="booking_time_text {{ $time['class'] }} {{ (isset($_GET['time']) && !empty($_GET['time']) && strtotime($time) == $_GET['time']) ? 'active' : '' }}" onclick="add_remove_class(this)">
                        <input type="radio" name="booking_time" class="booking_time_input" value="{{ $time['time'] }}" style="display:none;" {{ (isset($_GET['time']) && !empty($_GET['time']) && strtotime($time) == $_GET['time']) ? 'checked="checked"' : '' }}/>
                        {{ $time['time'] }}
                    </span>
                </span>
            @endforeach
        </div>
    </div>
@endforeach