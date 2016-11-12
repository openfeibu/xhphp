<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelecomPackage extends Model
{
    protected $table = 'telecom_package';

    protected $primaryKey = 'package_id';
	
	protected $guarded = [];
}
