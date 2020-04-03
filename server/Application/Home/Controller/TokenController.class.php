<?php
namespace Home\Controller;
use Think\Controller;
use Common\Logic\MsgLogic;
class TokenController extends Controller {
	//private $exptime=86400;//一天过期
	
    public function index(){
		$this->getToken();
	}
	
	//更新token
	private function updateToken(){
		$token=M("Token");
		$token->where("id=%d",array(2))->save(array("token"=>md5(rand()),"times"=>time()));
	}
	
	//读取token
	public function getToken(){
		$token=M("Token");
		$tkdata=$token->field("token,times")->find();
//		if(time()-$tkdata["times"]>=$this->exptime){
//			$this->updateToken();
//		}
		if($tkdata){
			$exptimeval=intval($this->exptime-(time()-$tkdata["times"]));
			$tkdata["exp_time"]=$exptimeval<=0?0:$exptimeval;
			MsgLogic::success(200,$tkdata);
		}else{
			MsgLogic::error(201);
		}
	}
	
}