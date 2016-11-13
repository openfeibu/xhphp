<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';

    protected $primaryKey = 'cart_id';

    protected $fillable = [
    	'cart_id',
    	'uid',
    	'goods_name',
    	'goods_sn',
        'goods_price',
        'goods_number',
        'goods_desc',
        'goods_img',
        'created_at',
        'updated_at',
    ];
}
