<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    use SoftDeletes;

    protected $table = 'advertisement';

    protected $primaryKey = 'adid';

    protected $dates = ['deleted_at'];
}
