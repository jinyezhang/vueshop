<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\OtherOrg;
class DefaultController extends IsLoginController{
	
	public function index(){
		$o=new OtherOrg();
		//获取ip
		$this->assign("ip",$o->getIP());
		
		//获取星期几
		$weekarray=array("日","一","二","三","四","五","六"); 
		$week="星期".$weekarray[date("w")];
		$this->assign("weekarray",$week);
		
		//日期
		$this->assign("nowdate",date("Y年m月d日"));
		
		//时间
		$this->assign("nowtime",date("H:i:s"));
		
		//下拉菜单栏目
		$funset=M("Funset");
		$menu=$funset->where("id in (1,2,5) and isclose='%s'",array('1'))->order("id asc")->field("title,webs")->select();
		$this->assign("menu",$menu);
		
		$this->display();
	}
	
	//退出系统
	public function outlogin(){
		unset($_SESSION["adminname"]);//删除单个session
		unset($_SESSION["password"]);
		unset($_SESSION["loginjk"]);
		unset($_SESSION["shell"]);
		unset($_SESSION["adminid"]);
		unset($_SESSION["gid"]);
		header("location: ".__APP__);
		exit;
	}
}