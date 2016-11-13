<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TelecomOrder extends Model
{
	use SoftDeletes;
	 //use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    
    protected $table = 'telecom_order';

    protected $primaryKey = 'id';
	
	//protected $fillable = array('name', 'idcard', 'major','dormitory_no','student_id','telecom_outOrderNumber');
	protected $guarded = [];

}
