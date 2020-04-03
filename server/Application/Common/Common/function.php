<?php
require("./Common/function.php");
function getTplurl($level,$tpl){
	if(array_key_exists($tpl,C("TPLURL{$level}"))){
		$url=C("TPLURL{$level}.".$tpl."");
	}else{
		$url="";	
	}
	return $url;
}
function getArturl($tpl){
	if(array_key_exists($tpl,C("ARTURL"))){
		$url=C("ARTURL.".$tpl."");
	}else{
		$url="";	
	}
	return $url;
}
function getController(){
	return  str_replace(array("index.php/","Home/"),array("",""),__CONTROLLER__);
}

function getModule(){
	return  str_replace(array("index.php/","Home/"),array("",""),__MODULE__);
}

function getAction(){
	return  str_replace(array("index.php/","Home/"),array("",""),__ACTION__);
}

function getIP() { 
	if (@$_SERVER["HTTP_X_FORWARDED_FOR"]){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}else if (@$_SERVER["HTTP_CLIENT_IP"]){ 
		$ip = $_SERVER["HTTP_CLIENT_IP"]; 
	}else if (@$_SERVER["REMOTE_ADDR"]){ 
		$ip = $_SERVER["REMOTE_ADDR"]; 
	}else if (@getenv("HTTP_X_FORWARDED_FOR")){
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	}else if (@getenv("HTTP_CLIENT_IP")){
		$ip = getenv("HTTP_CLIENT_IP");
	}else if (@getenv("REMOTE_ADDR")){ 
		$ip = getenv("REMOTE_ADDR");
	}else{ 
		$ip = "Unknown";
	}
	return $ip; 
}

//利用生日算出年龄
function getAge($date){
	if($date!=""){
		$now=time();
		$birthday=$now-strtotime($date);
		return round($birthday/31536000);
	}else{
		return "";
	}
}