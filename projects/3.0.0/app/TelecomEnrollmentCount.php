<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelecomEnrollmentCount extends Model
{
    protected $table = 'telecom_enrollment_count';

    protected $primaryKey = 'cid';

    protected $fillable = [
        'time_id',
        'time_start',
        'date',
        'count',
        'created_at',
        'updated_at',
    ];
}
