<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Game extends Model
{
    use SoftDeletes;

    protected $table = 'game';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    protected $fillables = [
		'name',
		'title',
		'status',
		'starttime',
		'endtime',
		'created_at',
		'updated_at'
    ];
}
