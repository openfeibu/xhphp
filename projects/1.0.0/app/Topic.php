<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use SoftDeletes;

    protected $table = 'topic';

    protected $primaryKey = 'tid';

    protected $dates = ['deleted_at'];

    public function comments()
    {
    	return $this->hasMany('App\TopicComment', 'tid', 'tid');
    }

    public function topicFavourite()
    {
        return $this->hasMany('App\TopicFavourite', 'tid', 'tid');
    }
    public function user ()
    {
    	return $this->belongsTo('App\User','uid','uid');
    }
}
