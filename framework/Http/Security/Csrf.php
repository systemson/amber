<?php

namespace Amber\Framework\Http\Security;

use Amber\Framework\Container\Facades\Session;
use Amber\Framework\Http\Message\InputParameters;
use Amber\Framework\Helpers\Hash;
use Psr\Http\Message\ServerRequestInterface;
use Carbon\Carbon;

class Csrf
{
    private $token;

    public function __construct(ServerRequestInterface $request)
    {
        if (Session::has('_csrf')) {
            $token = Session::get('_csrf');
        } else {
            $token = Hash::make($request->getServerParams()->get('REMOTE_ADDR') . Carbon::now());
            Session::set('_csrf', $token, 15);
        }

        $this->token = $token;
    }

    public function token(): string
    {
        return $this->token;
    }

    public function validate(ServerRequestInterface $request): bool
    {
        $sessionToken = Session::get('_csrf');
        Session::remove('_csrf');

        $postToken = $request->getParsedBody()->get('_csrf');

        if (is_null($sessionToken) || $sessionToken !== $postToken) {
            return false;
        }

        return true;
    }
}
