<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    //
    protected $fillable = [
        'name_id', 'user_id', 'name','description','star','deleted'];
}
