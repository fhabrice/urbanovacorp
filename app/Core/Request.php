<?php

namespace App\Core;

class Request
{
    private $method;
    private $uri;
    private $params;
    private $body;
    private $files;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $this->parseUri();
        $this->params = $_GET;
        $this->body = $this->parseBody();
        $this->files = $_FILES;
    }

    private function parseUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?');
        return $uri;
    }

    private function parseBody()
    {
        if ($this->method === 'POST') {
            return $_POST;
        }
        return [];
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function getParam($key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getBodyParam($key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getFile($key)
    {
        return $this->files[$key] ?? null;
    }

    public function isPost()
    {
        return $this->method === 'POST';
    }

    public function isGet()
    {
        return $this->method === 'GET';
    }

    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
