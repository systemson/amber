<?php

namespace App\Controllers\Api;

use Amber\Controller\ResourceCrudTrait;
use App\Controllers\Controller;
use App\Models\UserProvider as Model;

class UsersController extends Controller
{
    use ResourceCrudTrait;

    protected $provider = Model::class;
}
