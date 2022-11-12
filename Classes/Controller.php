<?php

namespace Lnw\Core;

use Lnw\Core\Msg;

abstract class Controller
{

    public function __construct($action, $request)
    {
        return $this->{$action}($request);
    }

    protected function returnView($views, $viewmodel = [])
    {
        $msg = Msg::get();

        if ($msg) {
            extract($msg);
        }

        if ($viewmodel) {
            extract($viewmodel);
        }
        $view = 'views/' . strtolower(str_replace('.', '/', $views)) . '.php';
        require_once $view;
    }
    protected function redirectTo($path, $params = [])
    {
        if (count($params) > 0) {
            foreach ($params as $key => $value) {
                Msg::set($key, $value);
            }
        }
        header('Location:' . $path);
        die;
    }
}
