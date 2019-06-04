<?php

namespace Amber\Framework\Http\Session;

use Amber\Collection\Collection;
use Amber\Collection\ImmutableCollection;

class Session extends Collection
{
    protected $flash = [];
    protected $active;
    protected $name;
    protected $id;

    public function __construct()
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

        $this->start();
        $this->loadParams();
        $this->loadFlash();
    }

    public function start()
    {
        if (!$this->isActive()) {

            if (!session_start()) {
                throw new \RuntimeException('Failed to start the session');
            }
        }

        $this->active = true;

        return true;
    }

    public function isActive(): bool
    {
        return $this->active && session_status() === PHP_SESSION_ACTIVE;
    }

    public function close()
    {
        if ($this->isActive()) {
            session_regenerate_id();
            session_unset();
            session_destroy();

            $this->clear();
    
            $this->active = false;

            return true;
        }

        return false;
    }

    protected function loadParams()
    {
        $session = $_SESSION['_params'] ?? [];
        $this->exchangeArray($session);
    }

    protected function loadFlash()
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        $this->flash = new ImmutableCollection($flash);
    }

    public function clear(): void
    {
        parent::clear();
        $this->flash->clear();
    }

    public function set(string $key, $value = null): void
    {
        $_SESSION['_params'][$key] = $value;
        parent::set($key, $value);
    }

    public function flash(... $args)
    {
        $count = count($args);

        switch ($count) {
            case 1:
                return $this->flash->get($args[0]);
                break;

            case 2:
                $flash = $this->flash;

                $_SESSION['_flash'][$args[0]] = $args[1];
                break;
            
            default:
                return $this->flash;
                break;
        }
        return $this->flash;
    }
}
