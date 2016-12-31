<?php
// router.php

// 都报错
error_reporting(E_ALL);


$path_parts = pathinfo(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
$ext = isset($path_parts['extension']) ? $path_parts['extension'] : "";

if (!in_array($ext, array('html', 'htm', 'php', '')))
    return false;    // 直接返回请求的文件
else {
    include("web_main.php");
    web_main();
}