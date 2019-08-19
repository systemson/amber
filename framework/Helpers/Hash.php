<?php

namespace Amber\Helpers;

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

    public static function token($limit): string
    {
        $size = ceil($limit / 2);

        return substr(bin2hex(random_bytes($size)), 0, $limit);
    }
}
