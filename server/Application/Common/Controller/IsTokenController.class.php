<?php
namespace Common\Controller;
use Think\Controller;
use Common\Logic\MsgLogic;
class IsTokenController extends Controller{
	public function __construct(){
		parent::__construct();
//        if(getHost()!=C("DOMAIN")){
//            die("没有权限获取接口！");
//        }

		$token=M("Token");
		$tkdata=$token->field("token")->find();
		if($tkdata['token']!=get_str($_REQUEST["token"])){
			MsgLogic::error(301);
		}	
	}
}