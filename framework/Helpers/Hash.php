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
        $size = ceil($limit / 2);

        return substr(bin2hex(random_bytes($size)), 0, $limit);
    }
}
