<?php

namespace App\Controllers\Api;

use Amber\Controller\ResourceCrudTrait;
use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\UserProvider as Model;
use Amber\Helpers\Crypto\Hash;

class UsersController extends Controller
{
    use ResourceCrudTrait;

    protected $provider = Model::class;

    protected function alterResourceBeforeCreate($request, $resource)
    {
        $resource->password = Hash::make($resource->password);

        return $resource;
    }

    protected function alterResourceBeforeUpdate(Request $request, $resource)
    {
        if ($request->post->has('password')) {
            $resource->password = Hash::make($resource->password);
        }

        return $resource;
    }
}
