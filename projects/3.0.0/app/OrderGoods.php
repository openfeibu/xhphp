<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table = 'order_goods';

    protected $primaryKey = 'id';

	protected $guarded = [];

	public $timestamp = false;
}
