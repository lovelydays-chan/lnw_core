<?php
namespace Lnw\Core\Abstracts;

class Bootstrap
{
    private $controller;
    private $action;
    private $request;

    public function __construct($request)
    {
        $this->request = $request;
        $this->controller = $this->request[0] ?? 'home';
        $this->action = $this->request[1] ?? 'index';
        isset($this->request[2]) ? $_GET['id'] = $this->request[2] : '';
    }

    public function createController()
    {
        // Check Class
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
