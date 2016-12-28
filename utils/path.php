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

        $s .= ".html";

        assert(!is_string($get));

        $query_str = http_build_query($get);
        if ($query_str != '') {
            $s .= '?' . $query_str;
        }

        return $s;
    }

    static public function locate($path)
    {
        header("Location: {$path}");
    }
}