<?php
$config=require('./Common/config.php');
$route=require('route.php');
$array=array(
	'TMPL_L_DELIM' => '{?',
	'TMPL_R_DELIM' => '?}',
	'MODULE_ALLOW_LIST' => array('Home','User',"Seller"),
	"DEFAULT_MODULE"=>"Home",
	'URL_ROUTER_ON'   => true,
    "DOMAIN"=>"http://shop.glbuys.com",
	'URL_ROUTE_RULES'=>$route,
	'RESPONSE_CODE'=>array(
		200=>urlencode('成功'),
		201=>urlencode('没有数据'),
		
		301=>urlencode("验证失败"),
		302=>urlencode("请输入必填项")
		
	)
);
return array_merge($config,$array);