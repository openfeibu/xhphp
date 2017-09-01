<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recommend extends Model
{
    protected $table = 'recommend';

    protected $fillable = [
    	'id',
        'url',
        'img',
        'name'
    ];
}
