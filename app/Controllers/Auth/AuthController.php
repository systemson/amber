<?php

namespace App\Controllers\Auth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response as ResponseContract;
use Amber\Framework\Container\Facades\Response;
use Amber\Framework\Container\Facades\View;
use App\Models\User;
use App\Controllers\Controller;
use Amber\Framework\Http\Message\InputParameters;
use Amber\Framework\Http\Message\QueryStringParameters;
use Amber\Framework\Container\Facades\Auth;
use Amber\Framework\Application as App;
use Amber\Framework\Auth\UserProvider;
use Carbon\Carbon;
use Amber\Container\Container;
use Amber\Framework\Container\Facades\Cache;
use Amber\Framework\Container\Facades\Session;
use Amber\Framework\Helpers\Hash;

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

    public function login(Request $request, Container $container)
    {
        $credentials = $this->getCredentialsFromRequest($request);

        $provider = $container->get(UserProvider::class);
        $user = $provider->getUserByEmail($credentials['email']);

        if ($this->validateCredentials($credentials, $user)) {
            $newToken = $this->newToken($user);

            $user->remember_token = $newToken;
            $user->save();

            Session::set('_token', $newToken);
            Cache::set($newToken, $user, 15);

            return Response::redirect('/login');
        }

        throw new \Exception('These credentials are not valid');
    }

    public function logout(UserProvider $provider)
    {
        $token = Session::get('_token');

        $user = $provider->getUserByToken($token);
        $user->remember_token = null;
        $user->save();

        // Deletes the session cache
        Cache::delete($token);

        Session::clear();
        Session::invalidate();

        return Response::redirect('/login');
    }

    protected function newToken($user): string
    {
        return Hash::make($user->email . Carbon::now());
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
