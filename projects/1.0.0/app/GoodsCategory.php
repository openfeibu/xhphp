<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodsCategory extends Model
{
	public $timestamps = false;

    protected $table = 'goods_category';

	protected $fillable = [
		'cat_name',
		'parent_id',
		'shop_id'
	];
}
