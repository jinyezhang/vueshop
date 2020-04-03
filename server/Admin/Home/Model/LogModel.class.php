<?php
namespace Home\Model;
use Think\Model;
class LogModel extends Model{
	static function setLog($content,$state){
		if(C("ISLOG")){
			$log=M("Log");
			$data["content"]=$content;
			$data["state"]=$state;
			$data["admin"]=$_SESSION["adminname"];
			$data["times"]=date("Y-m-d H:i:s");
			$log->add($data);	
		}
	}
}
