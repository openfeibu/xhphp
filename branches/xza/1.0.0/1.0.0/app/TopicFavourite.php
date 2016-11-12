<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class TopicFavourite extends Model
{
    protected $table = 'topic_favourite';

    public function topic()
    {
        return $this->belongsTo('App\Topic', 'tid', 'tid');
    }

    public function topicComment()
    {
        return $this->belongsTo('App\TopicComment', 'tcid', 'tcid');
    }
}
