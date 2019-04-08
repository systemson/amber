<?php

namespace App\Controllers\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Response;
use Amber\Framework\View;
use App\Models\User;
use App\Controllers\Controller;
use Amber\Framework\Request\InputParameters;
use Amber\Framework\Request\QueryStringParameters;

class AuthController extends Controller
{
	protected $required = [
		// Name    => DB Column
		'email'    => 'email',
		'password' => 'password',
	];

    public function loginForm(Request $request)
    {
        $template = View::view($this->getView())
        ->setLayout('layouts/app.php');

        return Response::setContent(View::toHtml());
    }

    protected function getLoginNameFor(string $name): string
    {
    	return $this->required[$name] ?? null;
    }

    protected function getCredentialsFromRequest(Request $request): array
    {
    	if (InputParameters::hasMultiple($this->required)) {
    		return InputParameters::getMultiple($this->required);
    	}
    	$required = implode('], [', $this->required);
    	throw new \Exception("These parameters are required: [{$required}].");
    	
    }

    public function login(Request $request)
    {
        $credentials = $this->getCredentialsFromRequest($request);
        dump($credentials);die();
    	$user = User::where($this->getCredentialsFromRequest($request))->get();

        return Response::json(true);
    }
}
