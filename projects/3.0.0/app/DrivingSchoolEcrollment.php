<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrivingSchoolEcrollment extends Model
{
    protected $table = 'driving_school_enrollment';

    protected $primaryKey = 'enroll_id';

    protected $fillable = [
        'name',
        'mobile',
        'content',
        'product_id',
        'ds_id',
        'status',
        'uid'
    ];
}
