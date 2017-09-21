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
        'dormitory_number',
        'building_id',
        'campus_id',
        'created_at',
        'updated_at'
    ];
}
