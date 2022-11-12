<?php

namespace Lnw\Core;

class Msg
{
    public static function set($key, $value)
    {
        $_SESSION['msg'][$key] = serialize($value);
    }

    public static function get($key = '')
    {
        $data = null;
        if ($key == '' && isset($_SESSION['msg']) && count($_SESSION['msg']) > 0) {
            foreach ($_SESSION['msg'] as $k => $v) {
                $data[$k] = unserialize($v);
                unset($_SESSION['msg'][$k]);
            }
        } else {
            if (isset($_SESSION['msg'][$key])) {
                $data = unserialize($_SESSION['msg'][$key]);
                unset($_SESSION['msg'][$key]);
            }
        }
        return $data;
    }
    public static function destroy()
    {
        unset($_SESSION['msg']);
    }
}
