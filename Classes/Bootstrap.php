<?php

namespace Lnw\Core;


use Dotenv\Dotenv;
use Lnw\Core\Database;
use FastRoute;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class Bootstrap
{
    private $controller;
    private $action;
    private $request;
    protected $structure = ['vendor', 'classs', 'models', 'controllers', 'helper', 'validate'];
    public function __construct()
    {
        array_map([$this, 'includeFiles'], $this->structure);
        $dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $route) {
            require_once './route/Route.php';
        });
        $dotenv = Dotenv::createImmutable("./");
        $dotenv->load();
        new Database();

        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = str_replace('//', '/', str_replace(basename(__DIR__), '', $_SERVER['REQUEST_URI']));

        // Strip query string (?foo=bar) and decode URIs
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
        $route = $this->controlRoute($routeInfo);
        return $route;
    }
    protected function controlRoute($routeInfo)
    {
        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                $this->controller = '';
                $this->action = '';
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $this->controller = $routeInfo[1];
                $this->action = '';
                break;
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                list($class, $method) = explode('@', $handler, 2);
                $this->controller = !empty($class) ? $class : 'home';
                $this->action = !empty($method) ? $method : 'index';
                $this->request = $this->cleanData(array_merge($vars, $_REQUEST));
                break;
        }
    }
    public function cleanData($val)
    {
        return is_array($val) ? array_map([$this, 'cleanData'], $val) : htmlspecialchars(stripslashes(strip_tags(trim($val))));
    }

    private function includeFiles($folder)
    {
        foreach (glob("{$folder}/*.php") as $filename) {
            require_once $filename;
        }
    }
    public function createController()
    {
        if (class_exists($this->controller)) {
            $parents = class_parents($this->controller);
            // Check Extend
            if (in_array('Lnw\Core\Controller', $parents, true)) {
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
