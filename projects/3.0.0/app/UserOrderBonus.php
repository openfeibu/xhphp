<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserOrderBonus extends Model
{
    protected $table = 'user_order_bonus';

    protected $primaryKey = 'id';

    protected $fillable = [
    	'number',
        'bonus',
        'date',
        'status',
        'uid'
    ];
}
