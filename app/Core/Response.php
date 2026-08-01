<?php

namespace App\Core;

class Response
{
    private $statusCode = 200;
    private $headers = [];
    private $content = '';

    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        http_response_code($code);
        return $this;
    }

    public function setHeader($name, $value)
    {
        $this->headers[$name] = $value;
        header("$name: $value");
        return $this;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function json($data)
    {
        $this->setHeader('Content-Type', 'application/json');
        $this->setContent(json_encode($data));
        echo $this->content;
        exit;
    }

    public function redirect($url, $statusCode = 302)
    {
        $this->setStatusCode($statusCode);
        header("Location: $url");
        exit;
    }

    public function render($view, $data = [])
    {
        extract($data);
        $viewPath = APP_PATH . '/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: $view");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        echo $content;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
