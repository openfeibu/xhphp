<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameUserPrize extends Model
{
    protected $table = 'game_user_prize';

    protected $primaryKey = 'guc_id';

    protected $fillable = [
		'uid',
		'game_id',
        'prize_name',
        'created_at',
        'updated_at',
    ];
}
