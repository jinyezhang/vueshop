<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class GetcouponController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(38);
	}
	
	public function index(){
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		
		$gcp=M("Getcoupon");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$gcp->alias("gcp")->field("gcp.id")->join("inner join __USER__ u on gcp.uid=u.qid inner join __COUPON__ cp on gcp.cpid=cp.id")->where("u.username like '%".urldecode($this->kwords)."%'")->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$gcp->alias("gcp")->field("u.username,gcp.id,gcp.times,cp.title,cp.price")->join("inner join __USER__ u on gcp.uid=u.qid inner join __COUPON__ cp on gcp.cpid=cp.id")->where("u.username like '%".urldecode($this->kwords)."%'")->order("gcp.times desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname.""));
		}
		
		$this->display();
	}
	
	public function del(){
		$this->delsql();
		$ids=@implode(",",$_POST["del"]);
		if($ids!=""){
			$gcp=M("Getcoupon");
			$gcp->where("id in ({$ids})")->delete();
			echo "<script>alert('删除成功！');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择数据');history.go(-1)</script>";	
		}
	}
	
}