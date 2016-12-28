<?php

class Tpl {
    static private $api_var = array();

    static public function load($name, $api_var)
    {
        $path = Path::join(ROOT_DIR, "template", $name . ".html");
        if (!file_exists($path)) {
            throw new TplNotFoundException;
        }

        self::$api_var = $api_var;
        include($path);
        self::$api_var = array();
    }

    static public function api() {
        $key = func_get_arg(0);

        if (!is_callable(self::$api_var[$key])) {
            return self::$api_var[$key];
        } else {
            return call_user_func_array(self::$api_var[$key], array_slice(func_get_args(), 1));
        }
    }

    static public function asset_url($relative_path)
    {
        $s = '/asset';
        if (!(strlen($relative_path) > 0 && $relative_path[0] == '/')) {
            $s .= '/';
        }
        $s .= $relative_path;
        return $s;
    }

    static public function asset($relative_path)
    {
        return Tpl::asset_url($relative_path);
    }
}

class TplNotFoundException extends Exception {
}