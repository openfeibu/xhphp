<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $table = 'notifications';

    protected $primaryKey = 'id';

    protected $dates = ['deleted_at'];

    protected $fillable = ['uid','top_id','top_uid','object_id','object_uid','new_id','new_uid','attr','type','read','created_at'];

}
