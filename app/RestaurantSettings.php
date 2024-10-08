<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RestaurantSettings extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url', 'secret', 'till_id', 'operator_id', 'offline_payment', 'online_payment', 'delivery_plu', 'discount_plu', 'surcharge_plu', 'tip_plu', 'booking_plu', 'table_group', 'order_table_group', 'account_group', 'recipient_email', 'restaurant_id', 'pos_type', 'sommelier_online', 'sommelier_reservations', 'sommelier_functions', 'somemmlier_loyalty', 'sommelier_time_attendance', 'holidays', 'self_pickup_order_type', 'delivery_order_type', 'enable_deposit', 'deposit_covers', 'deposit_amount_per_cover','booking_custom_date_fieldidx','booking_pax_fieldidx','booking_name_fieldidx','booking_comment_fieldidx','booking_option','booking_number_fieldidx'
    ];

    /**
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];
}
