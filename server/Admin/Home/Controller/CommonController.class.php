<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends PublicController{
	public function _initialize(){
		if($_SESSION["loginjk"]!=1){
			header("location:".__MODULE__."/Index/");
			exit;
		}
		$this->superAdmin();
	}
	
	//判断超级管理员
	private function superAdmin(){
		if($_SESSION["gid"]!=1){
			echo "<center><img src='".__ROOT__."/Public/admin/images/qx.gif' width='60' height='60' /> 对不起,您不是超级管理员!</center>";
			exit;
		}	
	}
}