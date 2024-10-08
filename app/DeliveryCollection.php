<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeliveryCollection extends Model
{
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope(new \App\Scopes\ZoneScope);
    }

    /**
     * @return mixed
     */
    public function delivery_collection_logs()
    {
        return $this->hasMany(\App\DeliveryCollectionLog::class);
    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function zone()
    {
        return $this->belongsTo(\App\Zone::class);
    }
}
