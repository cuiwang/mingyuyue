<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    //
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    public function getUpdatedAtAttribute($date)
    {
        // 默认100天前输出完整时间，否则输出人性化的时间
        if (Carbon::now() > Carbon::parse($date)->addDays(100)) {
            return Carbon::parse($date);
        }

        return Carbon::parse($date)->diffForHumans();
    }
}
