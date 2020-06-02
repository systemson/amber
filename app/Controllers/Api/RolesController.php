<?php

namespace App\Controllers\Api;

use Amber\Controller\ResourceCrudTrait;
use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\RoleProvider as Model;
use Amber\Helpers\Crypto\Hash;

class RolesController extends Controller
{
    use ResourceCrudTrait;

    protected $provider = Model::class;
}
