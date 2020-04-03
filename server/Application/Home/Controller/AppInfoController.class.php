<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class AppInfoController extends IsTokenController {
	
	public function index(){
		$setting=M("Setting");
		$data=$setting->find();
		$datalist=array(
			"andr_ver"=>$data["andr_ver"],
			"ios_ver"=>$data["ios_ver"],
			"androidpath"=>$data["and_url"],
			"iospath"=>$data["ios_url"],
			"isandlevel"=>$data["isandlevel"],
			"isioslevel"=>$data["isioslevel"],
			"ioslevelmsg"=>urlencode($data["ioslevelmsg"])
		);
		if($datalist){
			MsgLogic::success(200,$datalist);
		}else{
			MsgLogic::error(201);
		}
	}
	
}