<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class UserRolePivot extends AbstractProvider
{
    protected $name = 'users_roles';

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
        return $this->belongsTo(UserProvider::class);
    }
}
