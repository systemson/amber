<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class UserProvider extends AbstractProvider
{
    protected $name = 'users';

    protected $attributes = [
        'name' => 'alnum',
        'email' => 'email|length:null,64',
        'password' => 'alnum|length:null,64',
        'status' => 'numeric',
        'description' => 'alnum',
        'last_login' => 'date',
        'last_password_change' => 'date',
        'remember_token' => 'alnum',
    ];
}
