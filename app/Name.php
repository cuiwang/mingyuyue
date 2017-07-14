<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Name extends Model
{
    //
    protected $fillable = [
        'from_name', 'from', 'type', 'name','description','by','loves','views'];

    protected $hidden = ['created_at','updated_at'];


}
