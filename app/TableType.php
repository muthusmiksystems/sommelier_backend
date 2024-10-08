<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TableType extends Model
{
    protected $fillable = [
        'table_type_name', 'restaurant_id', 'is_enabled',
    ];
}
