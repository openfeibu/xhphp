<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderBonusSetting extends Model
{
    protected $table = 'order_bonus_setting';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
    	'number',
        'bonus',
    ];
}
