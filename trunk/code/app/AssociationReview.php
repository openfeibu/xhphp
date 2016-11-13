<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssociationReview extends Model
{
    use SoftDeletes;

    protected $table = 'association_review';

    protected $dates = ['deleted_at'];

    public function user()
    {
    	return $this->belongsTo('App\User', 'uid', 'uid');
    }
}
