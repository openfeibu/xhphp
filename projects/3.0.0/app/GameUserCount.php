<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameUserCount extends Model
{
    protected $table = 'game_user_count';

    protected $primaryKey = 'guc_id';

    protected $fillable = [
		'uid',
		'game_id',
        'num',
        'count',
        'lasttime',
        'share_num',
    ];
}
