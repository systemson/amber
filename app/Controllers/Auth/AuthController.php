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

    public function login(Request $request)
    {
        $credentials = $this->getCredentialsFromRequest($request);

        $user = $this->getUser('email', $credentials['email']);

        if (!$this->validateCredentials($credentials, $user)) {
        	throw new \Exception('These credentials are not valid');
        }

        //$this->startSession();

        dump($credentials);die();

        return Response::json(true);
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

    protected function validateCredentials(array $credentials, $user)
    {
      	if (empty($user)) {
      		return false;
      	}

    	foreach ($credentials as $key => $value) {
    		if (!$this->isValid($key, $value, $user[$key])) {
    			return false;
    		}
    	}

    	return true;
    }

    protected function isValid($key, $value, $userValue)
    {
    	$validations = $this->setValidations();

    	return $validations[$key]($value, $userValue);
    }

    protected function getUser(string $key, string $value)
    {
    	return User::where($key, $value)->first();
    }

    protected function setValidations()
    {
    	return [
    		'email' => function ($value, $userValue) {
    			return $value == $userValue;
    		},
    		'password' => function ($value, $userValue) {
    			return password_verify($value, $userValue);
    		},
    	];
    }
}
