<?php

namespace App\Controllers;

use Amber\Container\Facades\Response;
use Amber\Container\Facades\View;
use App\Models\Providers\UsersProvider;
use Amber\Model\Storage\Storage;
use Amber\Container\ContainerFacade;
use Amber\Model\Mediator\PgsqlMediator;

class HomeController extends Controller
{
    public function index()
    {
        View::view($this->getView())
        ->setLayout('layouts/app.php')
        ->setVar('name', 'World')
        ->setVar('description', 'This is a sample page.')
        ->setVar('version', 'v0.5-beta');

        $storage = new Storage();

        $storage->setProviders([
        	'users' => UsersProvider::class,
        ]);

        $storage->setMediators([
        	'pgsql' => PgsqlMediator::class,
        ]);

        $provider = $storage->getProvider('users');

        $query = $provider->insert([
        	'name' => 'Lol',
        	'email' => 'lol@lol.com',
        	'password' => '1234',
        ]);

        dd(
            $storage->select($provider->all()),
        	$storage->insert($query),
            $storage->select($provider->all()),
        );

        return View::toHtml();
    }
}
