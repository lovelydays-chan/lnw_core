<?php

namespace Lnw\Core\Classes;

abstract class Controller
{
    private $request;
    private $action;

    public function __construct($action, $request)
    {
        $this->action = $action;
        $this->request = $request;
    }

    public function executeAction()
    {
        return $this->{$this->action}();
    }

    public function request($key = 'all')
    {
        $data = null;
        if ($key === 'all') {
            $data = (object) $this->request;
        } else {
            $data = $this->request[$key] ?? '';
        }

        return $data;
    }

    protected function returnView($views, $viewmodel = false)
    {
        if ($viewmodel) {
            extract($viewmodel);
        }
        $view = 'views/'.strtolower(str_replace('.', '/', $views)).'.php';
        require $view;
    }
}
