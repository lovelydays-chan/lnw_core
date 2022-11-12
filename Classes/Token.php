<?php

namespace Lnw\Core;

class Token
{
    public static function generate()
    {
        return $_SESSION['_token'] = md5(uniqid());
    }

    public static function check($token)
    {
        $check = (isset($token) && $token === $_SESSION['_token']);
        unset($_SESSION['_token']);
        return $check;
    }
    public static function tag()
    {
        $token = isset($_SESSION['_token']) ? $_SESSION['_token'] : self::generate();
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}
