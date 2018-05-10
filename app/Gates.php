<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gates extends Model
{
    //
    public function records()
    {
    	return $this->hasOne('App\GateRecord', 'gate_id');
    }
}
