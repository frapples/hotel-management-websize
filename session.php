<?php

class Session {
    /* 这个类是当命名空间使的，利用了autoload特性 */

    static public function init()
    {
        session_start();
    }

    static public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    static public function get($key, $default)
    {
        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }else {
            return $default;
        }
    }

    static public function clear()
    {
        $_SESSION = array();
    }
}