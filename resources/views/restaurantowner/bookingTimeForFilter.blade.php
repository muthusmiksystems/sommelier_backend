@if(!empty($time_buttons))
    <option value="all">All Time Slot</option>
    @foreach($time_buttons as $booking_time)
        <option value="{{ $booking_time }}" {{ (isset($_POST['time_slot']) && $booking_time == $_POST['time_slot']) ? "selected='selected'" : "" }}>{{ $booking_time }}</option>
    @endforeach
@endif