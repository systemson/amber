<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class UserRolePivot extends AbstractProvider
{
    protected $name = 'user_role';

    protected $attributes = [
        'id',
        'user_id',
        'role_id',
    ];

    public function user()
    {
        return $this->belongsTo(UserProvider::class);
    }

    public function role()
    {
        return $this->belongsTo(RoleProvider::class);
    }
}
