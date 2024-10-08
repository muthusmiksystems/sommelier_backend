<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'restaurant_id'
    ];

    /**
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}
