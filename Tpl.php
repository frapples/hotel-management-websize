<?php

class Tpl {

    static public function load($name, $api_var)
    {
        $path = Path::join(ROOT_DIR, "template", $name . ".php");
        if (!file_exists($path)) {
            throw new TplNotFoundException;
        }

        $api = function($key) use($api_var) {
            if (!is_callable($api_var[$key])) {
                $ret = $api_var[$key];
                return function () use($ret) {
                    return $ret;
                };
            } else {
                return $api_var[$key];
            }
        };

        include($path);
    }
}

class TplNotFoundException extends Exception {
}