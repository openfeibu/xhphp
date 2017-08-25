<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DrivingSchoolPro extends Model
{
    protected $table = 'driving_school_product';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'desc',
        'price',
        'original_price',
        'ds_id',
    ];
}
