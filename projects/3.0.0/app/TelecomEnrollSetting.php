<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TelecomEnrollSetting extends Model
{
    protected $table = 'telecom_enroll_setting';

    public $timestamps = false;

    protected $primaryKey = 'setting_id';

    protected $fillable = [
        'setting_id',
        'campus_id',
        'count',
    ];
}
