<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class ShippingConfig extends Model
{
    protected $table = 'shipping_config';

    protected $primaryKey = 'cid';

    protected $fillable = [
    	'min',
        'max',
        'weight',
        'outweight',
        'shipping_fee',
        'payer'
    ];
}
