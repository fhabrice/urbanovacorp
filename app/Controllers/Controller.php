<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Response;

abstract class Controller
{
    protected $app;
    protected $response;
    protected $config;

    public function __construct()
    {
        global $app;
        $this->app = $app;
        // Fallback if global not set (direct instantiation)
        if (!$this->app) {
            // Try to create minimal app context for static usage
            if (defined('CONFIG_PATH') && file_exists(CONFIG_PATH . '/config.php')) {
                $this->config = require CONFIG_PATH . '/config.php';
            } else {
                $this->config = require __DIR__ . '/../../config/config.php';
            }
            // Create dummy response for fallback
            $this->response = new Response();
            return;
        }
        $this->response = $app->getResponse();
        $this->config = $app->getConfig();
    }

    protected function view($view, $data = [])
    {
        if ($this->app) {
            $data['app'] = $this->app;
            $data['session'] = $this->app->getSession();
            // Ensure pageTitle is passed
            if (!isset($data['pageTitle'])) $data['pageTitle'] = preg_replace('/.*\//','',$view);
            return $this->response->render($view, $data);
        } else {
            // Fallback render
            $session = new \App\Core\Session([]);
            $data['session'] = $session;
            $data['app'] = null;
            extract($data);
            $viewPath = APP_PATH . '/Views/' . $view . '.php';
            if (!file_exists($viewPath)) throw new \Exception("View not found: $view");
            ob_start();
            require $viewPath;
            echo ob_get_clean();
            return;
        }
    }

    protected function redirect($url)
    {
        if ($this->app) return $this->response->redirect($url);
        header("Location: $url"); exit;
    }

    protected function json($data)
    {
        if ($this->app) return $this->response->json($data);
        header('Content-Type: application/json'); echo json_encode($data); exit;
    }

    protected function getDb()
    {
        if ($this->app) return $this->app->getDatabase();
        // Fallback DB
        $cfg = $this->config['database'] ?? [];
        return new \App\Core\Database($cfg);
    }

    protected function getSession()
    {
        if ($this->app) return $this->app->getSession();
        return new \App\Core\Session($this->config['security'] ?? []);
    }

    protected function getRequest()
    {
        if ($this->app) return $this->app->getRequest();
        return new \App\Core\Request();
    }
}
