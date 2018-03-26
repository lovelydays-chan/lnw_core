<?php

namespace Lnw\Core\Abstracts;

abstract class Controller
{
    protected $request;
    protected $action;

    public function __construct($action, $request)
    {
        $this->action = $action;
        $this->request = $request;
    }

    public function executeAction()
    {
        return $this->{$this->action}();
    }

    protected function returnView($viewmodel, $fullview)
    {
        $loader = new Twig_Loader_Filesystem('views');
        $render = new Twig_Environment($loader, [
          'cache' => 'cache',
        ]);
    }
}
