<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    /**
     * @var array
     */
    protected $casts = [
        'expiry_date' => 'datetime',
    ];

    /**
     * @return mixed
     */
    public function restaurant()
    {
        return $this->belongsTo(\App\Restaurant::class);
    }

    /**
     * @return mixed
     */
    public function restaurants()
    {
        return $this->belongsToMany(\App\Restaurant::class);
    }
}
