<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GateRecord extends Model
{
    //
    protected $casts = [
        'records' => 'array'
    ];
}
