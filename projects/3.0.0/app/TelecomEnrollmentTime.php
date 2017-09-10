<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelecomEnrollmentTime extends Model
{
    protected $table = 'telecom_enrollment_time';

    protected $primaryKey = 'time_id';

    protected $fillable = [
        'time_start',
        'time_end',
        'count',
        'created_at',
        'updated_at',
    ];
}
