<?php

namespace App\Controllers\Api;

use Amber\Controller\ResourceCrudTrait;
use App\Controllers\Controller;
use App\Models\UsersProvider;

class UsersController extends Controller
{
    use ResourceCrudTrait;

    protected $provider = UsersProvider::class;
}
