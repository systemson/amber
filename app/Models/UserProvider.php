<?php

namespace App\Models;

use Amber\Auth\UserProvider as ParentProvider;

class UserProvider extends ParentProvider
{
    protected $name = 'users';

    protected $attributes = [
        'id',
        'name' => 'string-type|alpha|length:2,64',
        'email' => 'email|length:null,64',
        'password' => 'length:null,64',
        'status' => 'numeric|default:1',
        'description' => 'alnum|optional|length:null,256',
        'remember_token' => 'optional|alnum',
    ];

    /**
     * @var array Eager loaded relations
     */
    public $relations = [
        'roles'
    ];

    public function roles()
    {
        return $this->hasAndBelongsToMany(RoleProvider::class, UserRolePivot::class, 'user_id', 'role_id');
    }
}
