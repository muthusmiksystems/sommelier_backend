<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    //

    public function resTables()
    {
        return $this->belongsToMany(\App\TableInformation::class)->withPivot('table_information_id');
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    /**
     * @return mixed
     */
    public function restaurant()
    {
        return $this->belongsTo(\App\Restaurant::class);
    }
}
