<?php

namespace App\Controllers\Api;

use Amber\Container\Facades\Response;
use Amber\Container\Facades\View;
use App\Controllers\Controller;
use Psr\Http\Message\ServerRequestInterface;
use App\Models\UserProvider;
use Amber\Helpers\Hash;

class UsersController extends Controller
{
    public function list(ServerRequestInterface $request)
    {
        $provider = new UserProvider();

        return Response::json($provider->all());
    }

    public function create(ServerRequestInterface $request)
    {
        $provider = new UserProvider();

        $resource = $provider->new(
            $request->getParsedBody()->all()
        );

        if ($resource->isValid()) {
            $resource->password = Hash::make($resource->password);

            if ($provider->save($resource)) {
                return Response::json($resource);
            }
        }

        return Response::json([
            'errors' => $resource->getErrors(),
        ]);
    }

    public function read(int $id)
    {
        $provider = new UserProvider();

        $resource = $provider->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        return Response::json($resource);
    }

    public function update(ServerRequestInterface $request, int $id)
    {
        $provider = new UserProvider();

        $resource = $provider->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        $resource->fill(
            $request->getParsedBody()
                ->only($resource->getAttributesNames())
                ->toArray()
        );


        if ($resource->isValid() && $provider->save($resource)) {
            return Response::json($resource);
        }

        return Response::json([
            'errors' => $resource->getErrors(),
        ]);
    }

    public function delete(int $id)
    {
        $provider = new UserProvider();

        $resource = $provider->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        if (!$provider->delete($resource)) {
            return Response::internalServerError();
        }

        return Response::json([
            'message' => 'Resource successfully deleted.',
        ]);
    }
}
