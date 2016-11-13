<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $table = 'activity';

    protected $primaryKey = 'actid';

    protected $dates = ['deleted_at'];
}
