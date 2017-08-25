<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EducationPro extends Model
{
    protected $table = 'education_product';

    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'desc',
        'price',
        'original_price',
        'edu_id',
    ];
}
