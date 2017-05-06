<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LossCategory extends Model
{
    protected $table = 'loss_category';

    protected $primaryKey = 'cat_id';
    
    protected $fillable = [
    	'cat_id',
    	'sort',
    	'cat_name'
    ];
}
