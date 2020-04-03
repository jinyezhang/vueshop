<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class LogController extends CommonController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(18);
	}
	
	public function index(){
		$log=M("Log");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$log->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$log->order("id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname.""));
		}
		
		$this->display();
	}
	
	public function del(){
		$log=M("Log");
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$log->where("id in ({$del})")->delete();
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的日志'); history.go(-1);</script>";	
		}
	}
	
}