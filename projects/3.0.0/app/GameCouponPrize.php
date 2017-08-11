<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameCouponPrize extends Model
{
    protected $table = 'game_coupon_prize';

    public $timestamps = false;

    protected $primaryKey = 'prize_id';

    protected $fillable = [
		'price_id',
		'coupon_id',
        'prize_value',
    ];
}
