<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class PointsController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(13);
	}
	
	public function index(){
		
		//获取刷新时间
		$ref=M("Ref");
		$refdata=$ref->field("num")->find();
		$this->assign("refdata",$refdata);
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		
		$inte=M("Integral");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$inte->where("username like '%s' or title like '%s' or times like '%s'",array("%".urldecode($this->kwords)."%","%".urldecode($this->kwords)."%","%".urldecode($this->kwords)."%"))->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$inte->where("username like '%s' or title like '%s' or times like '%s'",array("%".urldecode($this->kwords)."%","%".urldecode($this->kwords)."%","%".urldecode($this->kwords)."%"))->field("id,title,username,times,decide,times2")->order("id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname));
		}
		
		$this->display();
	}
	
	//删除
	public function del(){
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$inte=M("Integral");
			$inte->where("id in ({$del})")->delete();
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
	//修改刷新时间
	public function modref(){
		$refnum=get_int($_POST["ref"]);
		if($refnum>=3){
			$ref=M("Ref");
			$ref->where('id=%d',array(1))->save(array("num"=>$refnum));
			echo "<script>{alert ('设置成功');location.href='".__CONTROLLER__."'}</script>";
			exit;
		}else{
			echo "<script>{alert ('刷新值不能小于3秒');history.go(-1)}</script>";
		}		
	}
	
	//查看详情
	public function desc(){
		$inte=M("Integral");
		$list=$inte->join("__USERS__ on __INTEGRAL__.userid=__USERS__.id")->field("__USERS__.pointstrue,__INTEGRAL__.title,__INTEGRAL__.id,amount,__INTEGRAL__.username,__INTEGRAL__.times,decide,times2,__INTEGRAL__.sex,__INTEGRAL__.names,__INTEGRAL__.phone,__INTEGRAL__.address,__INTEGRAL__.zipcode,__INTEGRAL__.nowpoint,__INTEGRAL__.zpoint,__INTEGRAL__.content")->find();
		$this->assign("list",$list);
		$this->display();	
	}
	
	//积分兑换处理
	public function handle(){
		$inte=M("Integral");
		$inte->where("id=%d",array($this->id))->save(array("decide"=>'1',"times2"=>date("Y-m-d H:i:s")));
		echo "<script>alert ('处理成功!');location.href='".__CONTROLLER__."/desc?id={$this->id}&page={$this->page}&kwords={$this->kwords}'</script>";	
		exit;
	}
	
}