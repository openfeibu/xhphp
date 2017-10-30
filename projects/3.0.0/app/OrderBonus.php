<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderBonus extends Model
{
    protected $table = 'order_bonus';

    protected $primaryKey = 'id';

    protected $timestamps = false;

    protected $fillable = [
    	'number',
        'bonus',
        'date',
        'uid'
    ];
}
