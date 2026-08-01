<?php

namespace App\Controllers;

use App\Core\Application;
use App\Core\Response;

abstract class Controller
{
    protected $app;
    protected $response;

    public function __construct()
    {
        global $app;
        $this->app = $app;
        $this->response = $app->getResponse();
    }

    protected function view($view, $data = [])
    {
        $data['app'] = $this->app;
        $data['session'] = $this->app->getSession();
        return $this->response->render($view, $data);
    }

    protected function redirect($url)
    {
        return $this->response->redirect($url);
    }

    protected function json($data)
    {
        return $this->response->json($data);
    }

    protected function getDb()
    {
        return $this->app->getDatabase();
    }

    protected function getSession()
    {
        return $this->app->getSession();
    }

    protected function getRequest()
    {
        return $this->app->getRequest();
    }
}
