<?php
namespace Home\Org;
//数据采集类
class Collect{
	protected $webs,$charset,$firsthtml,$lasthtml,$labels,$err;
	public $exwebs,$title,$bodys;
	//获取表单信息
	function __construct(){
		$this->webs=str_replace("\r\n","|||",$_POST["webs"]);
		$this->webs=str_replace("\r","|||",$this->webs);
		$this->webs=str_replace("\n","|||",$this->webs);
		$this->exwebs=@explode("|||",$this->webs);
		$this->charset=$_POST["charset"];
		$this->firsthtml=str_replace("/","\/",$_POST["firsthtml"]);
		$this->lasthtml=str_replace("/","\/",$_POST["lasthtml"]);
		$this->labels=@implode(",",$_POST["labels"]);
		$this->err=$_POST["err"];
	}
	
	
	//执行采集设置
	function setCollect($v){
		$fcontents = @file_get_contents($v);
		$ifcontent=iconv("{$this->charset}","utf-8//IGNORE",$fcontents);
	
		//去除标签
		if(strstr($this->labels,"a")){
			$ifcontent=preg_replace("/<a(.+?)>/is","",$ifcontent);
			$ifcontent=preg_replace("/<\/a>/is","",$ifcontent);
		}
	
		if(strstr($this->labels,"img")){
			$ifcontent=preg_replace("/<img(.+?)>/is","",$ifcontent);
		}
		
		if(strstr($this->labels,"iframe")){
			$ifcontent=preg_replace("/<iframe(.+?)>/is","",$ifcontent);
			$ifcontent=preg_replace("/<\/iframe>/is","",$ifcontent);
		}
		
		if(strstr($this->labels,"script")){
			$ifcontent=preg_replace("/<script(.+?)>(.+?)<\/script>/is","",$ifcontent);
		}
		
		//获取标题
		$tmode="/<title>(.+?)<\/title>/is";
		if(preg_match($tmode,$ifcontent,$title)){
		}
		
		$title=str_replace("<title>","",$title[0]);
		$this->title=str_replace("</title>","",$title);
		
		//获取内容
		$mode="/".$this->firsthtml."(.*?)".$this->lasthtml."/is{$this->err}";
		if(preg_match($mode,$ifcontent,$content)){
			$this->bodys=$content[0];
			return "{$v}->采集成功<br />";
		}else{
			return "{$v}->采集失败<br />";	
		}
		
	}
}
?>