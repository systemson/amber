<?php

namespace Amber\Http\Session;

use Amber\Collection\Collection;
use Amber\Collection\ImmutableCollection;
use Amber\Http\Session\FlashCollection;
use Amber\Http\Session\MetadataCollection;

class Session extends Collection
{
    private $flash = [];
    private $metadata = [];
    private $cookie_params = [];

    const STORAGE_NAME = '_params';

    public function __construct(array $options = [])
    {
        $this->init();
    }

    public function init()
    {
        self::setCookieParams([
            'lifetime' => $options['lifetime'] ?? 15 * 60,
            'path' => $options['path'] ?? '/',
            'domain' => $options['domain'] ?? null,
            'secure' => $options['secure'] ?? null,
            'httponly' => $options['httponly'] ?? null,
        ]);

        $this->start();
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
