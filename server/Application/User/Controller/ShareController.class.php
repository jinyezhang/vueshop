<?php
namespace User\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class ShareController extends IsTokenController {
	
	//分享的积分
	public function addpoints(){
		$uid=get_str($_POST["uid"]);
		if($uid!=''){
			$user=M("User");
			$res=$user->where("qid='%s'",array($uid))->setInc("points",10);
			if($res){
                MsgLogic::success(200,urlencode("恭喜您，分享成功，获得积分！"));
            }else{
                MsgLogic::error(202,urlencode("抱歉，分享失败，没有获取到积分！"));
            }
		}else{
			MsgLogic::error(205,urlencode("请登录会员"));
		}
	}
	
	
}