<?php

namespace Amber\Auth;

use Amber\Model\Provider\AbstractProvider;

abstract class UserProvider extends AbstractProvider implements UserProviderContract
{
    public function hasUserBy(string $key, $value): bool
    {
        return $this->getUserBy($key, '=', $value) != null;
    }

    public function getUserBy(string $key, $value)
    {
        $query = $this->query()
            ->select(['*'])
            ->from($this->getName())
            ->where($key, '=', $value)
        ;

        return $this->first($query);
    }

    public function hasUserById(int $value): bool
    {
        return $this->hasUserBy('id', $value)->count() > 0;
    }

    public function getUserById(int $id)
    {
        return $this->getUserBy('id', $id);
    }

    public function hasUserByEmail(string $email): bool
    {
        return $this->hasUserBy('email', $email);
    }

    public function getUserByEmail(string $email)
    {
        return $this->getUserBy('email', $email);
    }

    public function hasUserByToken(string $token): bool
    {
        return $this->hasUserBy('remember_token', $token);
    }

    public function getUserByToken(string $token)
    {
        return $this->getUserBy('remember_token', $token);
    }
}
