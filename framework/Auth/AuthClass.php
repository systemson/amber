<?php

namespace Amber\Framework\Auth;

use Psr\Http\Message\ServerRequestInterface as Request;

class AuthClass
{
    private $user;
    private $request;

    public function __construct($user = null)
    {
        if (!is_null($user)) {
            $this->setUser($user);
        }
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function getUser()
    {
        if ($request = $this->getRequest()) {
            return $this->getRequest()->getAttribute('user');
        }
    }

    public function check()
    {
        return !is_null($this->getUser());
    }

    public function __call(string $method, $args)
    {
        if ($this->check()) {
            return $this->user[$method] ?? null;
        }
    }
}
