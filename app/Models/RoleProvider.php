<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class RoleProvider extends AbstractProvider
{
    protected $name = 'roles';

    protected $attributes = [
        'id' => 'numeric',
        'name' => 'alpha',
        'description' => 'alnum|optional|length:null,256',
        'created_at' => 'date|optional',
        'updated_at' => 'date|optional',
        'status' => 'numeric|default:1',
    ];

    protected $relations = [
        'users'
    ];

    public function users()
    {
        return $this->hasAndBelongsToMany(UserProvider::class, UserRolePivot::class, 'user_id', 'role_id');
    }
}
