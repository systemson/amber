<?php

namespace Amber\Helpers\Crypto;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

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

    public static function key(): string
    {
        return Key::createNewRandomKey()->saveToAsciiSafeString();
    }

    public static function encrypt(string $string): string
    {
        $key = key::loadFromAsciiSafeString(env('APP_KEY'));

        return Crypto::encrypt($string, $key);
    }

    public static function decrypt(string $string): string
    {
        $key = key::loadFromAsciiSafeString(env('APP_KEY'));

        return Crypto::decrypt($string, $key);
    }

    public static function token($limit): string
    {
        $size = ceil($limit / 2);

        return substr(bin2hex(random_bytes($size)), 0, $limit);
    }
}
