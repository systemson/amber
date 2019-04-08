<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class User extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    	'name',
    	'email',
    	'password',
    	'status',
    	'image',
    	'description',
    	'last_login',
    	'last_password_change',
    	'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    	'password',
    	'last_password_change',
    	'remember_token',
    	'deleted_at',
    ];
}
