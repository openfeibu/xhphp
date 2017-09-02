<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EducationEnrollment extends Model
{
    protected $table = 'education_enrollment';

    protected $primaryKey = 'enroll_id';

    protected $fillable = [
        'name',
        'mobile',
        'content',
        'product_id',
        'edu_id',
        'status',
        'uid'
    ];
}
