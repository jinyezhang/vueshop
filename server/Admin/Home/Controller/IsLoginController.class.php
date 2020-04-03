<?php
namespace Home\Controller;
use Think\Controller;
class IsLoginController extends PublicController{
	public function _initialize(){
		$this->isLogin();
	}
	
	//栏目权限
	protected function safeColumn($id){
		$admin=M("Admin");
		$data=$admin->where("qid=%d",array($_SESSION["adminid"]))->field("c_path")->find();
		$c_path=@explode(",",$data["c_path"]);
		if(!(in_array($id,$c_path) || $_SESSION["gid"]==1)){
			echo "<center><img src='".__ROOT__."/Public/admin/images/qx.gif' width='60' height='60' /> 对不起您没有权限访问!请联系管理员.</center>";
			exit;
		}
	}
	
}