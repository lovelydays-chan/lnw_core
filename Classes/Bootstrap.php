<?php

namespace Lnw\Core\Classes;

class Bootstrap
{
    private $controller;
    private $action;
    private $request;

    public function __construct($routeInfo)
    {
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $this->controller = '';
                $this->action = '';
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $this->controller = $routeInfo[1];
                $this->action = '';
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                list($class, $method) = explode('@', $handler, 2);
                $this->controller = $class ?? 'home';
                $this->action = $method ?? 'index';
                $this->request = $this->cleanData(array_merge($vars, $_GET, $_POST));
                break;
        }
    }

    public function cleanData($val)
    {
        return is_array($val) ? array_map([$this, 'cleanData'], $val) : htmlspecialchars(stripslashes(strip_tags(trim($val))));
    }

    public function createController()
    {
        if (class_exists($this->controller)) {
            $parents = class_parents($this->controller);
            // Check Extend
            if (in_array('Controller', $parents, true)) {
                if (method_exists($this->controller, $this->action)) {
                    return new $this->controller($this->action, $this->request);
                }
                // Method Does Not Exist
                echo '<h1>Method does not exist</h1>';

                return;
            }
            // Base Controller Does Not Exist
            echo '<h1>Base controller not found</h1>';

            return;
        }
        // Controller Class Does Not Exist
        echo '<h1>Controller class does not exist</h1>';
    }
}
