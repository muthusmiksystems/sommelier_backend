
    <form action="" method="post">
        <div class="row">
            <div class="col-lg-3">
                <select name="resturant" class="form-control filter_restaurant_dropdown">
                    <option value="all">Select Restaurant</option>
                        @foreach ($restaurants as $restaurant)
                            @if(isset($restaurant->restaurantSettings->sommelier_reservations) && $restaurant->restaurantSettings->sommelier_reservations == 'yes')
                                <option value="{{ $restaurant->id }}" class="text-capitalize">{{ $restaurant->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </form>