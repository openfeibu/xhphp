<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';

    protected $primaryKey = 'edu_id';

    protected $fillable = [
        'name',
        'logo_url',
        'img_url',
        'desc',
        'content',
        'tell',
    ];
}
