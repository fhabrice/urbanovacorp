<?php

namespace App\Controllers;

/**
 * Simple Controller - fonctionne sans Application complète
 */
class SimpleController
{
    protected $config;
    protected $session;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/config.php';
        $this->session = $_SESSION;
    }

    protected function view($view, $data = [])
    {
        $data['config'] = $this->config;
        $data['session'] = $this->session;
        
        extract($data);
        
        $viewPath = APP_PATH . '/Views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            echo "View not found: $viewPath";
            return;
        }
        
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        echo $content;
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }

    protected function getConfig()
    {
        return $this->config;
    }

    protected function getSession()
    {
        return $this->session;
    }
}
