<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DataVersion extends Model
{
    protected $table = 'data_version';

	public $timestamp = false;
}