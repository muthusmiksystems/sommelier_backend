<?php


namespace App;

use ChristianKuri\LaravelFavorite\Traits\Favoriteable;
use Event;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
class RestaurantCustomerModel extends Model
{
    protected $table = 'restaurant_customer_model'; // Specify the table name if different from the default

    protected $fillable = [
        'role_id',
        'user_id',
        'restaurant_id',
    ];

    // Define relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    // Add any other relationships as needed, e.g., with Role model

    // You can define any other methods or accessors/mutators as per your application logic
}
