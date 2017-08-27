<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ZhimaCertify extends Model
{
    protected $table = 'zhima_certify';

    protected $primaryKey = 'id';

    protected $fillable = [
        'uid',
        'cert_name',
        'cert_no',
        'bizNo',
        'status',
    ];
}
