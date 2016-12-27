<?php

error_reporting(E_ALL);

define("ROOT_DIR", __DIR__);
include("utils/path.php");

// 类自动加载机制的定义
spl_autoload_register(function ($class) {
    $class = strtolower($class);

    $maybe_paths = array(Path::join(ROOT_DIR, $class . ".php"),
                  Path::join(ROOT_DIR, "model", $class . ".php"),
                  Path::join(ROOT_DIR, "controller", $class . ".php"));

    foreach($maybe_paths as $path) {
        if (file_exists($path)) {
            include($path);
            return;
        }
    }
    echo "错误，尝试在以下路径寻找文件失败" . print_r($maybe_paths, true) . '\n';
});

// 网站的入口
function web_main()
{
    /* 网站初始化 */
    Session::init();


    /* 切分请求 */
    $querys = explode("/", $_SERVER["REQUEST_URI"]);
    $querys = array_values(array_filter($querys, function ($item) {
        return $item != "";
    }));

    if (count($querys) <= 0) {
        $controll_name = "index";
    } else {
        $controll_name = $querys[0];
    }


    Controller::dispath($controll_name);
}

