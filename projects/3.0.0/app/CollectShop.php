<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CollectShop extends Model
{
    protected $table = 'collect_shops';

    protected $primaryKey = 'id';

     protected $fillable = [
    	'shop_id',
    	'uid',
        'created_at',
        'updated_at',
    ];
}
