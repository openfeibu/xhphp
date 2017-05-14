<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $table = 'user_coupon';

    protected $primaryKey = 'user_coupon_id';

    protected $fillables = [
		'uid',
		'coupon_id',
		'overdue',
        'receive',
        'status'
    ];
}
