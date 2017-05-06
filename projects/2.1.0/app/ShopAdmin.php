<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ShopAdmin extends Authenticatable
{
	protected $table = 'user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'mobile', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}