<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class LinkController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(6);
	}
	
	public function index(){
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		
		$link=M("Link");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$link->where("webs like '%s' or title like '%s'",array("%".urldecode($this->kwords)."%","%".urldecode($this->kwords)."%"))->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$link->where("webs like '%s' or title like '%s'",array("%".urldecode($this->kwords)."%","%".urldecode($this->kwords)."%"))->field("id,title,num,webs,photo")->order("num asc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname));
		}
		
		$this->display();
	}
	
	public function add(){
		$title=get_str(trim($_POST["title"]));
		$webs=get_str($_POST["webs"]);
		$photo=get_str($_POST["photo"]);
		if($title!="" && $webs!=""){
			$link=M("Link");
			$link->title=$title;
			$link->webs=$webs;
			$link->photo=$photo;
			$link->num=999;
			$link->add();
			echo "<script>alert ('添加成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
	//排序
	public function order(){
		$link=M("Link");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$link->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."'</script>";
		exit;
	}
	
	//删除
	public function del(){
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$link=M("Link");
			$link->where("id in ({$del})")->delete();
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
	public function edit(){
		$link=M("Link");
		$getlink=$link->where("id=%d",array($this->id))->find();
		$this->assign("getlink",$getlink);
		$this->display();	
	}
	
	public function mod(){
		$title=get_str(trim($_POST["title"]));
		$webs=get_str($_POST["webs"]);
		$photo=get_str($_POST["photo"]);
		if($title!="" && $webs!=""){
			$link=M("Link");
			$data["title"]=$title;
			$data["webs"]=$webs;
			$data["photo"]=$photo;
			$link->where("id=%d",array($this->id))->save($data);
			echo "<script>{alert ('修改成功');location.href='".__CONTROLLER__."'}</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
}