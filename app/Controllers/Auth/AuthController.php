<?php

namespace App\Controllers\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Response;
use Amber\Framework\View;
use App\Models\User;
use App\Controllers\Controller;

class AuthController extends Controller
{
	protected $credentials = [
		'username' => 'username',
		'password' => 'password',
	];

    public function loginForm(Request $request)
    {
    	dump($this->login($request));
        $template = View::view($this->getView())
        ->setLayout('layouts/app.php')
        ->setVar('title', 'Login form')
        ->setVar('description', 'Comming soon.')
        ->setVar('version', 'v0.5-beta');

        return Response::setContent(View::toHtml());
    }

    protected function getLoginNameFor(string $name): string
    {
    	return $this->credentials[$name] ?? null;
    }

    public function login(Request $request)
    {
    	$username = $request->get($this->getLoginNameFor('username'));
    	$password = $request->get($this->getLoginNameFor('password'));

    	$user = User::where([
	   		$this->getLoginNameFor('username') => $username,
       		$this->getLoginNameFor('password') => $password,
       	])->get();

    	dump($user);die();
        return Response::json(User::all());
    }
}
