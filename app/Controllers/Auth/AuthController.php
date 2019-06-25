<?php

namespace App\Controllers\Auth;

use Amber\Framework\Container\Facades\Response;
use Amber\Framework\Container\Facades\View;
use App\Controllers\Controller;
use Amber\Framework\Http\Message\POST;
use Amber\Framework\Auth\UserProvider;
use Carbon\Carbon;
use Psr\Container\ContainerInterface;
use Amber\Framework\Container\Facades\Session;
use Amber\Framework\Helpers\Hash;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Validator\Validator;

class AuthController extends Controller
{
    protected $required = [
        // Name    => DB Column
        'email'    => 'email',
        'password' => 'password',
    ];

    public function loginForm(ServerRequestInterface $request)
    {
        View::view($this->getView())
        ->setLayout('layouts/app.php');

        return View::toHtml();
    }

    public function login(ServerRequestInterface $request, ContainerInterface $container)
    {
        $credentials = $this->getCredentialsFromRequest($request);

        $validations = $this->prevalidate($credentials);
        if ($validations !== true) {

            Session::flash()->set('errors'. $validations);
            return Response::redirect('/login');
        }

        $provider = $container->get(UserProvider::class);
        $user = $provider->getUserByEmail($credentials['email']);

        if ($this->validateCredentials($credentials, $user)) {
            $newToken = $this->newToken($user);

            $user->remember_token = $newToken;
            $user->save();

            Session::set('_token', $newToken);
            Session::cache()->set($newToken, $user, 15);

            return Response::redirect('/');
        }

        throw new \Exception('These credentials are not valid');
    }

    public function logout(UserProvider $provider)
    {
        $token = Session::get('_token');

        if (!is_null($token)) {
            $user = $provider->getUserByToken($token);
            $user->remember_token = null;
            $user->save();

            // Deletes the session cache
            Session::cache()->delete($token);
        }

        Session::close();

        return Response::redirect('/login');
    }

    protected function newToken($user): string
    {
        return Hash::token(32);
    }

    protected function getCredentialsFromRequest(ServerRequestInterface $request): array
    {
        if (POST::hasMultiple($this->required)) {
            return POST::getMultiple($this->required);
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

    protected function prevalidate($values)
    {
        $validations = array_combine($values, [
            'email|length:null,50',
            'alnum|length:5,16',
        ]);

        return Validator::validateAll($validations);
    }

    protected function isValid($key, $value, $userValue)
    {
        $validations = $this->validations();

        return $validations[$key]($value, $userValue);
    }

    protected function getUser(string $key, string $value)
    {
        return User::where($key, $value)->first();
    }

    protected function validations()
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
