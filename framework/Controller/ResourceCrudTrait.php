<?php

namespace Amber\Controller;

use Amber\Model\Provider\AbstractProvider as ProviderInterface;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Container\Facades\Response;
use Amber\Container\Facades\View;

trait ResourceCrudTrait
{
	public function getProvider(): ProviderInterface
	{
		return new $this->provider;
	}

    public function list(ServerRequestInterface $request)
    {
        $provider = $this->getProvider();

        return Response::json($provider->all());
    }

    public function form(ServerRequestInterface $request, int $id = null)
    {
    	$provider = $this->getProvider();

    	$resource = $provider->find($id);

        View::view($this->getView())
            //->setLayout('layouts/app.php')
            ->setVar('resource', $resource)
        ;

        return View::toHtml();
    }

    public function create(ServerRequestInterface $request)
    {
        $provider = $this->getProvider();

        $resource = $provider->new(
            $request->getParsedBody()->all()
        );

        if ($resource->isValid()) {
            $resource->password = Hash::make($resource->password);

            if ($provider->save($resource)) {
                return Response::created()->json($resource);
            }
        }

        return Response::unprocessableEntity()
            ->json([
                'errors' => $resource->getErrors(),
            ])
        ;
    }

    public function read(ServerRequestInterface $request, int $id)
    {
        $provider = $this->getProvider();

        $resource = $provider->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        return Response::json($resource);
    }

    public function update(ServerRequestInterface $request, int $id)
    {
        $provider = $this->getProvider();

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

        return Response::unprocessableEntity()
            ->json([
                'errors' => $resource->getErrors(),
            ])
        ;
    }

    public function delete(ServerRequestInterface $request, int $id)
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
            'message' => 'Resource successfully deleted.',
        ]);
    }
}