<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantCategory extends Model
{
    /**
     * @return mixed
     */
    public function restaurants()
    {
        return $this->belongsToMany(\App\Restaurant::class, 'restaurant_category_restaurant');
    }
}
