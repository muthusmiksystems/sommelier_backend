@if(!empty($tables_info->isNotEmpty()))
    @foreach($tables_info as $table)
        @if($table->bookings->isEmpty())
            <div class="form-check">
                <input class="form-check-input selectable_table_input" type="checkbox" name="selected_table[]" value="{{ $table->id }}" data-seats="{{$table->total_seats }}" data-no_of_persons="{{ $selected_booking->no_of_seats }}">
                <label class="form-check-label" for="flexRadioDefault1">
                     {{ $table->table_number }} (Seats= {{$table->total_seats }})
                </label>
            </div>
        @elseif($table->bookings->isNotEmpty() && strtotime(date('H:i', strtotime($selected_booking->booking_datetime))) != strtotime(date('H:i', strtotime($table->bookings[0]->booking_datetime))))
            <div class="form-check">
                <input class="form-check-input selectable_table_input" type="checkbox" name="selected_table[]" value="{{ $table->id }}" data-seats="{{$table->total_seats }}" data-no_of_persons="{{ $selected_booking->no_of_seats }}">
                <label class="form-check-label" for="flexRadioDefault1">
                     {{ $table->table_number }} (Seats= {{$table->total_seats }})
                </label>
            </div>
        @endif 
    @endforeach
@else
    <span style="color:red;">No tables found!</span>
@endif