<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RealnameAuth extends Model
{
    use SoftDeletes;

    protected $table = 'real_name_auth';

    protected $dates = ['deleted_at'];

    public function user()
    {
    	return $this->belongsTo('App\User', 'uid', 'uid');
    }
}
