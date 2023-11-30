<?php

namespace router;

readonly class Router
{
    private array $routes;

    /**
     * @param array $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    public function route($path, $data): void {
        $key = 'home';
        if ($path != null && array_key_exists($path, $this->routes)) {
            $key = $path;
        }

        $controllerName = $this->routes[$key]['controller'];
        $controllerAction = $this->routes[$key]['action'];
        $method = $this->routes[$key]['method'];
        require_once "app/controller/{$controllerName}.php";

        $controllerName = "controller\\$controllerName";
        $controller = new $controllerName();

        switch ($method) {
            case 'POST':
                $controller->$controllerAction($data);
                break;
            case '':
            case 'GET':
            default:
                $controller->$controllerAction();
                break;

        }
    }
}