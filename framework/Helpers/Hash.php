<?php

namespace Amber\Framework\Helpers;

class Hash
{
    public static function make(string $value): string
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public static function verify(string $value, string $hash): bool
    {
        return password_verify($value, $hash);
    }

    public static function token($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }
}
