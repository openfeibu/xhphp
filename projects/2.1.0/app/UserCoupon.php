<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{
    protected $table = 'user_coupon';

    protected $primaryKey = 'user_coupon_id';

    protected $fillable = [
		'uid',
		'overdue',
        'receive',
        'status',
        'min_price',
        'price',
    ];
}
