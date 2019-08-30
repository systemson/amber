<?php

namespace App\Models;

use Amber\Model\Provider\AbstractProvider;

class RoleProvider extends AbstractProvider
{
    protected $name = 'roles';

    protected $attributes = [
        'id',
        'name' => 'alpha:áéíóúÁÉÍÓÚñÑ',
        'description' => 'alnum|optional|length:null,256',
        'created_at' => 'date|optional',
        'updated_at' => 'date|optional',
    ];

    protected $relations = [
        //
    ];
}
