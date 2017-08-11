<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApiVersion extends Model
{
    protected $table = 'api_version';

	public $timestamp = false;
}