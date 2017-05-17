<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupon';

    public $timestamps = false;

    protected $primaryKey = 'coupon_id';

    protected $fillable = [
		'coupon_code',
		'price',
		'min_price',
    ];
}
