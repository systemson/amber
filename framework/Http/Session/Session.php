<?php

namespace Amber\Framework\Http\Session;

use Amber\Collection\Collection;
use Amber\Collection\ImmutableCollection;
use Amber\Framework\Http\Session\FlashCollection;
use Amber\Framework\Http\Session\MetadataCollection;

class Session extends Collection
{
    protected $flash = [];
    protected $metadata = [];
    protected $cookie_params = [];

    const STORAGE_NAME = '_params';
    const FLASH_NAME = '_flash';

    public function __construct(array $options = [])
    {
        /*var_dump('Session start');
        //var_dump(session_start());
        var_dump('Session name');
        var_dump(session_name());
        var_dump('Session id');
        var_dump(session_id());
        var_dump('Session active');
        var_dump(PHP_SESSION_ACTIVE);
        var_dump('Session status');
        var_dump(session_status());
        var_dump('Session param');
        //var_dump($_SESSION);
        var_dump('Cookie param');
        var_dump($_COOKIE);
        //var_dump(session_regenerate_id());
        var_dump(session_unset());
        //var_dump(session_destroy());
        dd('die');*/

        $this->init();
    }

    public function init()
    {
        self::setCookieParams([
            'lifetime' => $options['lifetime'] ?? 60,
            'path' => $options['path'] ?? '/',
            'domain' => $options['domain'] ?? null,
            'secure' => $options['secure'] ?? null,
            'httponly' => $options['httponly'] ?? null,
        ]);

        $this->start();

        $this->load();
    }

    public function start()
    {
        if (!$this->isActive()) {
            if (!session_start()) {
                throw new \RuntimeException('Failed to start the session');
            }

            $this->load();
        }

        return true;
    }

    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    public function close()
    {
        if ($this->isActive()) {
            session_regenerate_id();
            session_unset();
            session_destroy();

            $this->clear();
            $this->init();

            return true;
        }

        return false;
    }

    protected function load()
    {
        $this->cookie_params = session_get_cookie_params();
        $this->loadParams();
        $this->loadFlash();
        $this->loadMetadata();
    }

    protected function loadParams()
    {
        $session = $_SESSION[static::STORAGE_NAME] ?? [];
        $this->exchangeArray($session);
    }

    protected function loadFlash()
    {
        $this->flash = new FlashCollection();
    }

    protected function loadMetadata()
    {
        $this->metadata = new MetadataCollection();
    }

    public function clear(): void
    {
        parent::clear();
        $this->flash->clear();
        $this->metadata->clear();
        $this->close();
    }

    public function offsetSet($offset, $value = null)
    {
        $_SESSION['_params'][$offset] = $value;
        $this->metadata()->touch();
        parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        unset($_SESSION['_params'][$offset]);
        $this->metadata()->touch();
        parent::offsetUnset($offset);
    }

    public function flash()
    {
        return $this->flash;
    }

    public function metadata()
    {
        return $this->metadata;
    }

    public static function setCookieParams(array $options = [])
    {
        session_set_cookie_params(
            $options['lifetime'] ?? null,
            $options['path'] ?? null,
            $options['domain'] ?? null,
            $options['secure'] ?? null,
            $options['httponly'] ?? null
        );
    }
}
