<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class UserProvider extends AbstractProvider
{
    protected $name = 'users';

    protected $attributes = [
        'id',
        'name' => 'alpha',
        'email' => 'email|length:null,64',
        'password' => 'alnum|length:null,64',
        'status' => 'numeric|default:1',
        'description' => 'alnum|optional|length:null,256',
        'remember_token' => 'optional|alnum',
        'created_at' => 'date|optional',
        'updated_at' => 'date|optional',
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
