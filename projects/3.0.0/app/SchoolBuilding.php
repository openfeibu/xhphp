<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolBuilding extends Model
{
    protected $table = 'school_building';

    public $timestamps = false;

    protected $primaryKey = 'building_id';

    protected $fillable = [
        'building_id',
        'campus_id',
        'building_no',
    ];
}
