<html>
<body>
    <div style="display:flex;overflow:hidden;width:100%;margin-bottom:15px;align-items: center;height:100px;">
        <div style="display:block;width:80%;text-align:center;">
            <div>{{ $restaurant->name }}</div>
            <div>RESERVATIONS REPORT</div>
        </div>
        <div style="display:block;width:20%;float:right;">
            <img src="{{substr(url("/"), 0, strrpos(url("/"), '/'))}}{{ $restaurant->image }}" alt="{{ $restaurant->name }}" height="80" width="80" style="border-radius: 0.275rem;">
        </div>
    </div>
    <p style="display:block;overflow:hidden;width:100%;margin:0;">Date: {{ date('d M, Y', strtotime($bookingdate)) }}</p>
    @if(!empty($booking_staus_search) && $booking_staus_search != 'all')
        <p style="display:block;overflow:hidden;width:100%;margin:0;">Booking Status: {{ ucfirst($booking_staus_search) }}</p>
    @endif
    @if(!empty($shift) && $shift != 'all')
        <p style="display:block;overflow:hidden;width:100%;margin:0;">Shift: {{ $shift }}</p>
    @endif
    @if(!empty($time_slot) && $time_slot != 'all')
        <p style="display:block;overflow:hidden;width:100%;margin:0px 0px 15px 0px;">Time Slot: {{ $time_slot }}</p>
    @endif
    <table cellpadding="0" cellspacing="0" style="width:100%;display:table;margin-bottom:30px;">
        <tr style="width:100%;">
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:80px;">BookingId</th>
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:120px;">CustName</th>
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:100px;">Mobile No</th>
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:80px;">Time</th>
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:50px;">Pax</th>
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:120px;">Table No</th>
            <th style="text-align:center;border:1px solid grey;border-collapse: collapse;width:150px;">Comments</th>
        </tr>
        
            @if($bookings->isNotEmpty())
                @foreach($bookings as $booking)
                    <tr style="display:table-row;width:100%;">
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:10%;">{{ "#".$booking->unique_booking_id }}</td>
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:20%;">{{ $booking->booking_name }}</td>
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:10%;">{{ $booking->booking_mobile }}</td>
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:20%;">{{ date('h:i a', strtotime($booking->booking_datetime)) }}</td>
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:10%;">{{ $booking->no_of_seats }}</td>
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:20%;">
                            @if(!empty($booking->resTables))
                                @foreach($booking->resTables as $table_row)
                                 {{ $table_row->table_number }}
                                @endforeach
                            @endif
                        </td>
                        <td style="text-align:center;border:1px solid grey;border-collapse: collapse;width:10%;">{{ $booking->comments }}</td>
                    </tr>
                @endforeach
            @endif
        
    </table>
    <div style="display:block;overflow:hidden;width:100%;position:fixed;bottom:-70px;height:100px;text-align:center;opacity:0.5;">
        <p style="width:100%;margin:0;display:block;">Powered by Sommelier Restaurant Reservation System</p>
        <p style="width:100%;margin:0;display:block;"><a style="text-decoration:none;" href="www.cloudappstechnology.com">www.cloudappstechnology.com</a></p>
        <p style="width:100%;margin:0;display:block;">1300 722 777</p>

    </div>
</body>
</html>