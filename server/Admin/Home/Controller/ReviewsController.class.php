<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class ReviewsController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(41);
	}
	
	public function index(){
		
		$this->display();
	}
	
	public function manage(){
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		
		$reviews=D("Reviews");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$reviews->getReviewsTotal(urldecode($this->kwords));
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$reviews->getReviewsPage($pageInfo["row_offset"],$pageInfo["row_num"],urldecode($this->kwords));
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__ACTION__."?",$strname.""));
		}
		
		$this->display();
	}
	
	public function left(){
		$this->display();	
	}
	
	public function del(){
		$this->delsql();
		$reivews=D("Reviews");
		$reivews->delReviews();
	}
	
	//审核通过
	public function audit(){
		$reivews=D("Reviews");
		$ids=@implode(",",$_POST["del"]);
		if($ids!=""){
			$reivews->where("id in ({$ids})")->save(array("audit"=>"1"));
			echo "<script>alert ('审核成功'); location.href='".__CONTROLLER__."/manage';</script>";
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//撤销审核
	public function unaudit(){
		$reivews=D("Reviews");
		$ids=@implode(",",$_POST["del"]);
		if($ids!=""){
			$reivews->where("id in ({$ids})")->save(array("audit"=>"0"));
			echo "<script>alert ('审撤销核成功'); location.href='".__CONTROLLER__."/manage';</script>";
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
}