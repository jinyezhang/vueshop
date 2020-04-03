<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\LogModel;
class AdminColumnController extends CommonController{
	
	public function index(){
		$an=get_str($_GET["an"]);
		$this->assign("an",$an);
		
		$userid=get_int($_GET["userid"]);
		$this->assign("userid",$userid);
		
		$admin=D("Admin");
		$this->assign("jurimenu",$admin->jurimenu(0));
		
		$this->display();
	}
	
	public function mod(){
		$an=get_str($_GET["an"]);
		$this->assign("an",$an);
		$userid=get_int($_REQUEST["userid"]);
		$an=get_str($_REQUEST["an"]);
		$cid=@implode(",",$_POST["cid"]);
		$admin=M("Admin");
		$admin->where("id=%d",array($userid))->save(array("c_path"=>$cid));
		
		LogModel::setLog("给{$an}分配权限","设置权限");
		
		echo "<script>alert ('权限分配成功!');location.href='".__CONTROLLER__."?an={$an}&userid={$userid}&page={$this->page}&kwords={$kwords}'</script>";
		exit;
	}
	
}