<?php

namespace App\Core;

class Router
{
    private $request;
    private $response;
    private $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function addRoute($method, $path, $handler, $middleware = [])
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch()
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        foreach ($this->routes as $route) {
            if ($this->matchRoute($route, $uri, $method)) {
                $params = $this->extractParams($route['path'], $uri);

                // Execute middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    $result = $middleware->handle($this->request);
                    if ($result !== true) {
                        return $result;
                    }
                }

                // Execute handler
                return $this->executeHandler($route['handler'], $params);
            }
        }

        // 404 Not Found
        $this->response->setStatusCode(404);
        require APP_PATH . '/Views/errors/404.php';
    }

    private function matchRoute($route, $uri, $method)
    {
        if ($route['method'] !== $method) {
            return false;
        }

        $pattern = $this->convertPathToRegex($route['path']);
        return preg_match($pattern, $uri);
    }

    private function convertPathToRegex($path)
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function extractParams($routePath, $uri)
    {
        $params = [];
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        foreach ($routeParts as $index => $part) {
            if (preg_match('/\{([a-zA-Z0-9_]+)\}/', $part, $matches)) {
                $paramName = $matches[1];
                $params[$paramName] = $uriParts[$index] ?? null;
            }
        }

        return $params;
    }

    private function executeHandler($handler, $params)
    {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                $controllerClass = "App\\Controllers\\$controller";
                $controllerInstance = new $controllerClass();

                if (method_exists($controllerInstance, $method)) {
                    return call_user_func_array([$controllerInstance, $method], $params);
                }
            }
        }

        throw new \Exception("Invalid handler: $handler");
    }
}
