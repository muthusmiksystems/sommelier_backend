@if(!empty($areas))
    <option value="">Please select table location</option>
    @foreach($areas as $area)
        <option value="{{ $area->id }}">{{ $area->area_name }}</option>
    @endforeach
@endif