@if(!empty($restaurants))
    <option value="">Please select venue</option>
    @foreach($restaurants as $restaurant)
        <option value="{{ $restaurant->id }}">{{ $restaurant->name }}</option>
    @endforeach
@endif