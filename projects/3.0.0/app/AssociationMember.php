<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssociationMember extends Model
{
    use SoftDeletes;

    protected $table = 'association_member';

    protected $primaryKey = 'amid';

    protected $dates = ['deleted_at'];
}
