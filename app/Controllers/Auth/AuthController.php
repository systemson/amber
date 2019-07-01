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
use Amber\Framework\Helpers\Localization\Lang;

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
        /*
         * Validates the request
         */

        if (($errors = $this->validateRequest($request)) !== true) {
            return $this->failedLoginResponse($errors);
        }


        /*
         * Validates the credentials
         */

        $credentials = $this->getCredentialsFromRequest($request);

        $user = $container->get(UserProvider::class)
            ->getUserByEmail($credentials['email'])
        ;

        if (!$this->validateCredentials($credentials, $user)) {
            return $this->failedLoginResponse(
                $this->getFailLoginMessage($container)
            );
        }


        /*
         * Returns a response after the user is successfully logged in.
         */

        return $this->successfulLoginResponse($user);
    }

    public function logout(UserProvider $provider)
    {
        $token = Session::get('_token');

        if (!is_null($token)) {
            $user = $provider->getUserByToken($token);

            if (!is_null($user)) {
                $user->remember_token = null;
                $user->save();
            }

            // Deletes the session cache
            Session::cache()->delete($token);
        }

        Session::close();

        return $this->redirectToLoginResponse();
    }

    protected function redirectToLoginResponse()
    {
        return Response::redirect('/login');
    }

    protected function successfulLoginResponse($user)
    {
        $this->setRememberToken($user);

        return Response::redirect('/');
    }

    protected function getFailLoginMessage($container): array
    {
        return [
            'email' => $container->get(Lang::class)->translate('validations.fail-login'),
        ];
    }

    protected function failedLoginResponse(iterable $errors = [])
    {
        if (!empty($errors)) {
            Session::flash()->set('errors', $errors);
        }

        return Response::redirectBack('/');
    }

    protected function setRememberToken($user): void
    {
        $newToken = $this->newToken($user);

        $user->remember_token = $newToken;
        $user->save();

        Session::set('_token', $newToken);
        Session::cache()->set($newToken, $user, 15);
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

    protected function validateRequest($request)
    {
        $validations = $this->getRequestValidations();

        return Validator::assert($validations, (object) $request->getParsedBody()->toArray());
    }

    protected function getRequestValidations(): array
    {
        return [
            'email' => 'email|length:null,50',
            'password' => 'alnum|length:5,16',
        ];
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
