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
        'status' => 'numeric|default:1',
        //'description' => 'alnum|optional',
        'remember_token' => 'optional|alnum',
        'created_at' => 'date|optional',
        'updated_at' => 'date|optional',
    ];
}
