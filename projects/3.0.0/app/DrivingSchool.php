<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrivingSchool extends Model
{
    protected $table = 'driving_school';

    protected $primaryKey = 'ds_id';

    protected $fillable = [
        'name',
        'logo_url',
        'img_url',
        'desc',
        'content',
        'tell',
    ];
}
