@foreach($time_buttons as $shift => $booking_time)
    <div class="row">
        <div class="col-lg-12">
            <label class="col-form-label shift_label_text" style="text-align:left;">{{ $shift }}</label>
            @foreach($booking_time as $time)
                <span class="booking_time_btn_block">
                    <span class="booking_time_text {{ $time['class'] }}">
                        <input type="radio" name="booking_time" class="booking_time_input" value="{{ $time['time'] }}" style="display:none;" {{ (isset($current_booking_time) && !empty($current_booking_time) && strtotime($time["time"]) == $current_booking_time) ? 'checked="checked"' : '' }}/>
                        {{ $time['time'] }}
                    </span>
                </span>
            @endforeach
        </div>
    </div>
@endforeach