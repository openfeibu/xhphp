<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelecomEnrollment extends Model
{
    protected $table = 'telecom_enrollment';

    protected $primaryKey = 'enroll_id';

    protected $fillable = [
        'uid',
        'name',
        'date',
        'time_id',
        'time_start',
        'time_end',
        'created_at',
        'updated_at'
    ];
}
