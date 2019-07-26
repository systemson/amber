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
use Amber\Container\Facades\Str;

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
        //$this->testUpdateModel();
        //$this->testModelProvider();

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
        $path = realpath(config('database.connections.sqlite.database'));

        $pdo1 = new \Aura\Sql\ExtendedPdo('sqlite:dbname={$path}', null, null, []);
        $pdo2 = new \Aura\Sql\ExtendedPdo('sqlite:dbname={$path}');
        $pdo3 = new \Aura\Sql\ExtendedPdo('sqlite:dbname={$path}');

        dd(
            $pdo1,
            $pdo2,
            $pdo3
        );
    }

    public function testUpdateModel()
    {
        $provider = new UserProvider();

        $user = $provider->find(2);

        dd(
            $user->updatable(),
            $user->name = 'Davidson Jose Peña Gonzalez',
            $user->updatable(),
            $provider->update($user),
            $provider->find(2),
        );
    }

    protected function testModelProvider()
    {
        $provider = new UserProvider();

        $user = $provider->new();

        $user->name = Str::faker()->name;
        $user->email = Str::faker()->email;

        $password = Str::faker()->password();
        $user->password = Hash::make($password);
        $user->raw_password = $password;

        if (!$user->isValid()) {
            $errors = $user->getErrors();
        }

        d('new', $user);

        $inserted = $provider->insert($user);

        d('inserted', $user, $inserted);

        $user->name = 'Deivi Peña';

        d('edited', $user, $inserted);
        
        $updated = $provider->update($user);

        dd(
            'updated',
            $user,
            $updated,
            'found',
            $provider->find($inserted->id)
        );
    }
}
