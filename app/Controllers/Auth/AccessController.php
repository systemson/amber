<?php

namespace App\Controllers\Auth;

use Amber\Container\Facades\Response;
use Amber\Container\Facades\View;
use App\Controllers\Controller;
use Amber\Http\Message\POST;
use App\Models\UserProvider;
use Carbon\Carbon;
use Psr\Container\ContainerInterface;
use Amber\Container\Facades\Session;
use Amber\Helpers\Crypto\Hash;
use Psr\Http\Message\ServerRequestInterface;
use Amber\Validator\Validator;
use Amber\Container\Facades\Lang;

class AccessController extends Controller
{
    protected $required = [
        // Name    => DB Column
        'email'    => 'email',
        'password' => 'password',
    ];

    protected $redirectAfterLogin = '/';

    protected $redirectAfterLogout = 'login';

    public function loginForm(ServerRequestInterface $request)
    {
        View::view($this->getView())
        ->setLayout('layouts/app.php');

        return View::toHtml();
    }

    public function login(ServerRequestInterface $request, UserProvider $provider)
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

        $user = $provider->getUserByEmail($credentials['email']);

        if (!$this->validateCredentials($credentials, $user)) {
            return $this->failedLoginResponse(
                $this->getFailLoginMessage()
            );
        }


        /*
         * * Logs in the user and returns a response after the user is successfully logged in.
         */

        return $this->successfulLoginResponse($user, $provider);
    }

    public function logout(UserProvider $provider)
    {
        $token = Session::get('_token');

        if (!is_null($token)) {
            $user = $provider->getUserByToken($token);

            if (!is_null($user)) {
                $user->remember_token = null;
                $provider->update($user);
            }

            // Deletes the session cache
            Session::cache()->delete($token);
        }

        Session::close();

        return $this->redirectToLoginResponse();
    }

    protected function redirectToLoginResponse()
    {
        return Response::redirect($this->redirectAfterLogout);
    }

    protected function successfulLoginResponse($user, $provider)
    {
        $this->setRememberToken($user, $provider);

        return Response::redirect($this->redirectAfterLogin);
    }

    protected function getFailLoginMessage(): array
    {
        return [
            'email' => Lang::translate('validations.fail-login'),
        ];
    }

    protected function failedLoginResponse(iterable $errors = [])
    {
        if (!empty($errors)) {
            Session::flash()->set('errors', $errors);
        }

        return Response::redirectBack();
    }

    protected function setRememberToken($user, $provider): void
    {
        $newToken = $this->newToken($user);

        $user->remember_token = $newToken;

        $provider->update($user);

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

        $input = $request
            ->getParsedBody()
            ->only(['email', 'password'])
            ->toArray()
        ;

        return Validator::assert(
            $validations,
            $input
        );
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
