<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelecomEnrollmentCount extends Model
{
    protected $table = 'telecom_enrollment_count';

    protected $primaryKey = 'cid';

    protected $fillable = [
        'date',
        'count',
        'campus_id',
        'created_at',
        'updated_at',
    ];
}
