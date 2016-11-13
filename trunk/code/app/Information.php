<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Information extends Model
{
    use SoftDeletes;

    protected $table = 'information';

    protected $primaryKey = 'iid';

    protected $dates = ['deleted_at'];
}
