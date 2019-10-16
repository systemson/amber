<?php

namespace Amber\Controller;

use Amber\Model\Provider\AbstractProvider as ProviderInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Amber\Container\Facades\Response;
use Amber\Container\Facades\View;

trait ResourceCrudTrait
{
    protected function getProvider(): ProviderInterface
    {
        return new $this->provider;
    }

    protected function find($id)
    {
        $provider = $this->getProvider();

        return $provider->find($id)
        ;
    }

    public function index(Request $request)
    {
        $provider = $this->getProvider();

        return Response::json([
            'data' => $provider->all(),
        ]);
    }

    /*public function form(Request $request, int $id = null)
    {
        $resource = $this->find($id);

        View::view($this->getView())
            ->setLayout('layouts/app.php')
            ->setVar('resource', $resource)
        ;

        return View::toHtml();
    }*/

    public function create(Request $request)
    {
        $provider = $this->getProvider();

        $resource = $provider->new(
            $request->getParsedBody()->only($provider ->getAttributesNames())->toArray()
        );

        if ($resource->isValid()) {
            $resource = $this->alterResourceBeforeCreate($request, $resource);

            if ($provider->save($resource)) {
                return Response::created()->json([
                    //'status' => 'created',
                    'data' => $resource,
                ]);
            }
        }

        return Response::unprocessableEntity()
            ->json([
                'errors' => $resource->getErrors(),
            ])
        ;
    }

    public function read(Request $request, int $id)
    {
        $resource = $this->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        return Response::json([
            'data' => $resource
        ]);
    }

    public function update(Request $request, int $id)
    {
        $provider = $this->getProvider();

        $resource = $this->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        $values = $request->getParsedBody()->only($provider ->getAttributesNames())->toArray();

        if (!empty($values)) {
            foreach ($values as $attr => $value) {
                $resource->{$attr} = $value;
            }

            if ($resource->isValid()) {
                $resource = $this->alterResourceBeforeUpdate($request, $resource);

                if ($provider->save($resource)) {
                    return Response::created()->json([
                        'data' => $resource
                    ]);
                }
            }
        }

        return Response::unprocessableEntity()
            ->json([
                'errors' => $resource->getErrors(),
            ])
        ;
    }

    public function delete(Request $request, int $id)
    {
        $provider = $this->getProvider();

        $resource = $provider->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        if (!$provider->delete($resource)) {
            return Response::internalServerError();
        }

        return Response::json([
            'status' => 'success',
            'message' => 'Resource successfully deleted.',
        ]);
    }

    protected function preProcessRequest(Request $request): Request
    {
        return $request;
    }

    protected function alterResourceBeforeCreate(Request $request, $resource)
    {
        return $resource;
    }

    protected function alterResourceBeforeUpdate(Request $request, $resource)
    {
        return $resource;
    }
}
