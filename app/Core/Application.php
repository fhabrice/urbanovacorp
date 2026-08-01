<?php

namespace App\Core;

class Application
{
    private $config;
    private $router;
    private $request;
    private $response;
    private $session;
    private $database;

    public function __construct()
    {
        $this->config = require CONFIG_PATH . '/config.php';
        $this->initializeEnvironment();
        $this->initializeServices();
    }

    private function initializeEnvironment()
    {
        // Set timezone
        date_default_timezone_set($this->config['app']['timezone']);

        // Error reporting
        if ($this->config['app']['debug']) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }

        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->config['security']['session_name']);
            session_start();
        }
    }

    private function initializeServices()
    {
        // Load language helper
        require_once APP_PATH . '/Helpers/language.php';

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session($this->config['security']);
        $this->database = new Database($this->config['database']);
        $this->router = new Router($this->request, $this->response);
    }

    public function run()
    {
        try {
            // Load routes
            $this->loadRoutes();

            // Dispatch request
            $this->router->dispatch();
        } catch (\Exception $e) {
            $this->handleException($e);
        }
    }

    private function loadRoutes()
    {
        $routes = require APP_PATH . '/routes/web.php';
        foreach ($routes as $route) {
            $this->router->addRoute(
                $route['method'],
                $route['path'],
                $route['handler'],
                $route['middleware'] ?? []
            );
        }
    }

    private function handleException(\Exception $e)
    {
        if ($this->config['app']['debug']) {
            $this->response->setStatusCode(500);
            echo "<h1>Error</h1>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            $this->response->setStatusCode(500);
            require APP_PATH . '/Views/errors/500.php';
        }
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getDatabase()
    {
        return $this->database;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
