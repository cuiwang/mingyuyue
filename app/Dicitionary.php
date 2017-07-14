<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dicitionary extends Model
{
    //
    protected $fillable = [
        'name', 'pinyin', 'bushou','bihua','fanti','wuxin','jieshi'];
    protected $hidden = ['created_at','updated_at','jieshi'];
}
