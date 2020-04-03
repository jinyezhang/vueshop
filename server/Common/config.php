<?php
//ini_set("display_errors","off");//开启服务器错误提示on:开启 off关闭
//error_reporting(E_ALL&~E_NOTICE);
session(array("cache_limiter"=>"private,must-revalidate"));
return array(
	"DB_TYPE"=>"mysql",
	"DB_HOST"=>"localhost",
	"DB_NAME"=>"vueshop",
	"DB_USER"=>"ghost8080",
	"DB_PWD"=>"123456",
	"DB_PORT"=>"3306",
	"DB_PREFIX"=>"app_",
	'DB_CHARSET'=>'utf8',
	'URL_CASE_INSENSITIVE' =>true
);