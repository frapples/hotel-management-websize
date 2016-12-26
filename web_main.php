<?php

error_reporting(E_ALL);

define("ROOT_DIR", __DIR__);

spl_autoload_register(function ($class) {
    $class = strtolower($class);

    $path = path_join(ROOT_DIR, $class . ".php");
    if (file_exists($path)) {
        include($path);
        return;
    }

    $path = path_join(ROOT_DIR, "model", $class . ".php");
    if (file_exists($path)) {
        include($path);
        return;
    }
        echo "错误，文件不存在" . $path . '\n';
});

// 网站的入口
function web_main()
{
    /* 网站初始化 */
    Session::init();


    /* 切分请求 */
    $querys = explode("/", $_SERVER["REQUEST_URI"]);
    $querys = array_filter($querys, function ($item) {
        return $item != "";
    });

    print_r($querys);
}

function path_join()
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
