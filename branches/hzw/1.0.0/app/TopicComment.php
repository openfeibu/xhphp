<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TopicComment extends Model
{
    use SoftDeletes;

    protected $table = 'topic_comment';

    protected $primaryKey = 'tcid';

    protected $dates = ['deleted_at'];

    public function topic()
    {
    	return $this->belongsTo('App\Topic', 'tid', 'tid')->withTrashed();
    }

    public function topicFavourite()
    {
        return $this->hasMany('App\TopicFavourite', 'tcid', 'tcid');
    }
}
