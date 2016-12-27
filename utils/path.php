<?php

class Path {
    static public function join()
    {
        $s = "";
        foreach(func_get_args() as $name) {
            if (strlen($s) > 0 && $s[strlen($s) - 1] != '/' &&
                strlen($name) > 0 && $name[0] != '/') {
                $s .= '/';
            }
            $s .= $name;
        }
        return $s;
    }

    static public function url()
    {
        $get = func_get_arg(0);
        $querys = array_slice(func_get_args(), 1);

        $s = "/";
        foreach($querys as $name) {
            if (strlen($s) > 0 && $s[strlen($s) - 1] != '/' &&
                strlen($name) > 0 && $name[0] != '/') {
                $s .= '/';
            }
            $s .= $name;
        }

        if (is_string($get)) {
            $s .= '?' . $get;
        } else if (is_array($get)) {
            $s .= '?' . http_build_query($get);
        }

        $s .= ".html";
        return $s;
    }
}