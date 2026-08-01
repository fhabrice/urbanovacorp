<?php

namespace App\Core;

class Session
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function flash($key, $value = null)
    {
        if ($value === null) {
            $value = $this->get($key);
            $this->remove($key);
            return $value;
        }
        $this->set($key, $value);
    }

    public function regenerateId()
    {
        session_regenerate_id(true);
    }

    public function destroy()
    {
        session_destroy();
        $_SESSION = [];
    }

    public function setFlashMessage($type, $message)
    {
        $this->set('flash_' . $type, $message);
    }

    public function getFlashMessage($type)
    {
        return $this->flash('flash_' . $type);
    }

    public function hasFlashMessage($type)
    {
        return $this->has('flash_' . $type);
    }
}
