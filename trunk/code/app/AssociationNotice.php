<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssociationNotice extends Model
{
    use SoftDeletes;

    protected $table = 'association_notice';

    protected $primaryKey = 'anid';

    protected $dates = ['deleted_at'];
}
