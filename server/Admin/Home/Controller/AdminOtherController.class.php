<?php
namespace Home\Controller;
use Think\Controller;
class AdminOtherController extends CommonController{
	
	public function index(){
		$an=get_str($_GET["an"]);
		$this->assign("an",$an);
		
		$userid=get_int($_GET["userid"]);
		$this->assign("userid",$userid);
		
		$admin=D("Admin");
		$adata=$admin->allowFunset($userid);
		$this->assign("funlist",$adata);
		$this->display();
	}
	
	public function mod(){
		$an=get_str($_GET["an"]);
		$this->assign("an",$an);
		
		$userid=get_int($_GET["userid"]);
		$fid=@implode(",",$_POST["fid"]);
		if($userid>0){
			$admin=M("Admin");
			$admin->where("id=%d",array($userid))->save(array("f_path"=>$fid));
			echo "<script>alert('分配成功！');location.href='".__CONTROLLER__."?an={$an}&userid={$userid}&page={$this->page}'</script>";
			exit;
		}
	}
	
}