<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolCampus extends Model
{
    protected $table = 'school_campus';

    public $timestamps = false;

    protected $primaryKey = 'campus_id';

    protected $fillable = [
        'campus_id',
        'campus_name',
    ];
}
