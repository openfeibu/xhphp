<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loss extends Model
{
    protected $table = 'loss';

    protected $primaryKey = 'loss_id';

    protected $fillable = [
    	'cat_id',
        'college_id',
        'uid',
    	'content',
    	'mobile',
        'type',
        'img',
        'thumb'
    ];
}
