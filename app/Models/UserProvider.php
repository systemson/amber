<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class UserProvider extends AbstractProvider
{
    protected $name = 'users';

    protected $attributes = [
        'id',
        'name' => 'string-type|alpha|length:10,64',
        'email' => 'email|length:null,64',
        'password' => 'alnum|length:null,64',
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
