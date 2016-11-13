<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TradeAccount extends Model
{
    protected $table = 'trade_account';

    protected $primaryKey = 'id';
	
	protected $guarded = [];
}
