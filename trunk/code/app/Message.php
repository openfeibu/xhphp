<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    protected $table = 'message';

    protected $primaryKey = 'mid';

    protected $dates = ['deleted_at'];
}
