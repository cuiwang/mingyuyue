<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Srcdata extends Model
{
    //
    protected $fillable = [
        'from','size','click', 'by','time','author', 'name','description','content','done','custom','imageurl'.'free'];

    protected $hidden = ['content','done'];


    public function getCreatedAtAttribute($date)
    {
        // 默认100天前输出完整时间，否则输出人性化的时间
        if (Carbon::now() > Carbon::parse($date)->addDays(100)) {
            return Carbon::parse($date);
        }

        return Carbon::parse($date)->diffForHumans();
    }

    public function getUpdatedAtAttribute($date)
    {
        // 默认100天前输出完整时间，否则输出人性化的时间
        if (Carbon::now() > Carbon::parse($date)->addDays(100)) {
            return Carbon::parse($date);
        }

        return Carbon::parse($date)->diffForHumans();
    }

}
