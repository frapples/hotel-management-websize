<?php
// router.php

// 都报错
error_reporting(E_ALL);

if (!preg_match('/\.(?:html|htm|php)$/', $_SERVER["REQUEST_URI"]))
    return false;    // 直接返回请求的文件
else {
    include("web_main.php");
    web_main();
}