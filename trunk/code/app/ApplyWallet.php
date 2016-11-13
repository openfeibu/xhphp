<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplyWallet extends Model
{
    protected $table = 'apply_wallet';

    protected $primaryKey = 'apply_id';
	
	protected $guarded = [];
}
