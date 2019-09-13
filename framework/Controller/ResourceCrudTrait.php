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

        return $provider
            ->with($provider->relations)
            ->find($id)
        ;
    }

    public function index(Request $request)
    {
        $provider = $this->getProvider();

        $resources = $provider
            ->with($provider->relations)
            ->all()
        ;

        return Response::json($resources);
    }

    public function form(Request $request, int $id = null)
    {
        $resource = $this->find($id);

        View::view($this->getView())
            ->setLayout('layouts/app.php')
            ->setVar('resource', $resource)
        ;

        return View::toHtml();
    }

    public function create(Request $request)
    {
        $provider = $this->getProvider();

        $resource = $provider->new(
            $request->getParsedBody()->all()
        );

        if ($resource->isValid()) {
            $resource = $this->alterResourceBeforeCreate($resource);

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

    public function read(Request $request, int $id)
    {
        $resource = $this->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        return Response::json($resource);
    }

    public function update(Request $request, int $id)
    {
        $provider = $this->getProvider();

        $resource = $this->find($id);

        if (is_null($resource)) {
            return Response::notFound();
        }

        $raw_data = file_get_contents('php://input');
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        $parts = array_slice(explode($boundary, $raw_data), 1);

        foreach ($parts as $part) {
            if ($part == "--\r\n") {
                break;
            }

            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            $raw_headers = explode(";", $raw_headers);
            $raw_headers = end($raw_headers);
            list($name, $value) = explode('=', $raw_headers);

            $values[$value] = $body;
        }

        dd(
            $raw_data,
            $values,
        );

        dd(
            $parts,
            file_get_contents('php://input'),
            json_decode(file_get_contents('php://input'), true),
            parse_str(file_get_contents('php://input'), $_PATCH),
            $_PATCH,
        );

        d($resource->password);
        d(parse_str(file_get_contents('php://input')));
        d($resource->getAttributesNames());
        d($request->getParsedBody());
        d($request->getParsedBody()
                ->only($resource->getAttributesNames())
                ->toArray());

        $resource->fill(
            $request->getParsedBody()
                ->only($resource->getAttributesNames())
                ->toArray()
        );

        dd($resource->password);


        if ($resource->isValid()) {
            $resource = $this->alterResourceBeforeUpdate($request, $resource);

            if ($provider->save($resource)) {
                return Response::json($resource);
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
            'message' => 'Resource successfully deleted.',
        ]);
    }

    protected function preProcessRequest(Request $request): Request
    {
        return $request;
    }

    protected function alterResourceBeforeCreation($resource)
    {
        return $resource;
    }

    protected function alterResourceBeforeUpdate(Request $request, $resource)
    {
        return $resource;
    }
}
