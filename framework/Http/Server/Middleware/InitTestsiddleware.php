<?php

namespace Amber\Http\Server\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Amber\Container\Facades\Filesystem;
use Amber\Helpers\ClassMaker\Maker;
use Amber\Http\Session\Session;
use Amber\Helpers\Assets\Loader;
use Amber\Http\Message\Uri;
use Amber\Container\Facades\Gemstone;
use App\Models\UserProvider;
use Amber\Phraser\Phraser;
use Amber\Helpers\Hash;

/**
 * Participant in processing a server request and response.
 *
 * An HTTP middleware component participates in processing an HTTP message:
 * by acting on the request, generating the response, or forwarding the
 * request to a subsequent middleware and possibly acting on its response.
 */
class InitTestsiddleware extends RequestMiddleware
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(Request $request, Handler $handler): Response
    {
        //$this->testClassMaker();
        //$this->testSession();
        //$this->loader();
        //$this->testUri();
        //$this->testSqlite();
        //$this->testGemstone();

        return $handler->handle($request);
    }

    public function loader()
    {
        $loader = new Loader([
            'Amber' => 'Assets',
        ]);

        $loader->js('Amber\Assets\jQuery');
    }

    protected function testClassMaker()
    {
        $maker = new Maker();

        d($maker->getImplementingClass('App\Controllers\UsersController', Handler::class));
        d($maker->getExtendingClass('App\Http\Middleware\TestMiddleware', RequestMiddleware::class));
        dd($maker->getExtendingClass(
            'App\Http\Middleware\TestMiddleware',
            RequestMiddleware::class,
            Middleware::class
        ));
    }

    protected function testSession()
    {
        $session = new Session();

        dd(
            $session,
            $session->metadata()->all(),
            $session->metadata()->created_at,
            $session->metadata()->updated_at,
            $session->metadata()->clear(),
            $_SESSION
        );
    }

    protected function testUri()
    {
        $uri1 = Uri::fromString('http://username:password@www.example.com:3000/api/users?foo=bar#fragment');
        $uri2 = Uri::fromString('https://www.example.com/api/users?foo=bar#fragment');
        $uri3 = Uri::fromString('example.com');

        dd(
            (string) $uri1,
            (string) $uri2,
            (string) $uri3
        );
    }

    protected function testSqlite()
    {
        $path = APP_DIR . 'database/sqlite.db';

        $pdo = new \PDO('sqlite:dbname:{$path}');

        dd($pdo);
    }

    protected function testGemstone()
    {
        $provider = new UserProvider();

        $new = $provider->new();

        $new->name = Phraser::faker()->name;
        $new->email = Phraser::faker()->email;

        $password = Phraser::faker()->password();

        $new->password = Hash::make($password);
        $new->raw_password = $password;

        if (!$new->isValid()) {
            $errors = $new->getErrors();
        }

        $inserted = $provider->insert($new);

        dd($inserted);
    }
}
