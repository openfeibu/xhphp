<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
	use SoftDeletes;
	
    protected $table = 'order';

    protected $primaryKey = 'oid';

	protected $guarded = [];
	
    public function user()
    {
    	return $this->belongsTo('App\User', 'uid', 'owner_id');
    }

    public function history()
    {
        return $this->hasMany('App\OrderHistory', 'oid', 'oid');
    }
}
