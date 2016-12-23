<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $table = 'goods';

    protected $primaryKey = 'goods_id';

    protected $fillable = [
    	'shop_id',
    	'cat_id',
    	'goods_name',
    	'goods_sn',
        'goods_price',
        'goods_number',
        'goods_desc',
        'goods_img',
        'goods_thumb',
        'created_at',
        'updated_at',
    ];
}
