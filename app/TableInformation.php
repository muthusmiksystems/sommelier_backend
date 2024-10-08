<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableInformation extends Model
{
    protected $table = 'table_informations';

    protected $fillable = [
        'table_number', 'total_seats', 'restaurant_id', 'area_id', 'table_type_id',
    ];

    public function bookings()
    {
        return $this->belongsToMany(\App\Booking::class);
    }
}
