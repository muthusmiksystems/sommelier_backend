<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShiftInformation extends Model
{
    protected $table = 'shift_informations';

    protected $fillable = [
        'breakfastStartTime', 'breakfastEndTime', 'breakfastDuration', 'teafirstStartTime', 'teafirstEndTime', 'teafirstDuration', 'lunchStartTime', 'lunchEndTime', 'lunchDuration', 'teasecondStartTime', 'teasecondEndTime', 'teasecondDuration', 'dinnerStartTime', 'dinnerEndTime', 'dinnerDuration', 'maxNoOfCover', 'emailFrom', 'teamName', 'email_options', 'restaurant_id', 'max_cover_breakfast', 'max_cover_lunch', 'max_cover_dinner', 'breakfast_warning_covers', 'lunch_warning_covers', 'dinner_warning_covers',
    ];
}
