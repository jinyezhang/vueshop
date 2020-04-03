<?php
header("Content-Type: text/html; charset=utf-8");
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:x-requested-with,content-type');
define("APP_NAME","Home");
define("APP_PATH","./Application/");
define("APP_DEBUG",true);
require './ThinkPHP/ThinkPHP.php';
?>