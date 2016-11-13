<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Version extends Model
{
    protected $table = 'version';

    protected $primaryKey = 'id';

    protected $guarded = [];
    
}
