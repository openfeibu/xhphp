<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    protected $table = 'order_status_history';

    public function order()
    {
    	return $this->belongsTo('App\Order', 'oid', 'oid');
    }

    public function user()
    {
    	return $this->belongsTo('App\User', 'uid', 'uid');
    }
}
