<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class VerifyController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(33);
	}
	
	public function index(){
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		
		$verify=D("Verify");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$verify->getVerifyTotal(urldecode($this->kwords));
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$verify->getVerifyPage($pageInfo["row_offset"],$pageInfo["row_num"],urldecode($this->kwords));
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname.""));
		}
		
		$this->display();
	}
	
	public function del(){
		$this->delsql();
		$verify=D("Verify");
		$verify->delVerify();
	}
	
	//审核通过
	public function audit(){
		$verify=D("Verify");
		$ids=@implode(",",$_POST["del"]);
		if($ids!=""){
			$verify->where("id in ({$ids})")->save(array("ispass"=>"1"));
			
			//会员升级为商家
			$vdata=$verify->field("uid")->where("id in ({$ids})")->select();
			if(count($vdata)>0){
				foreach($vdata as $v){
					$uidarr[]=$v["uid"];
				}
				$uids=@implode(",",$uidarr);
				$user=M("User");
				$user->where("qid in ({$uids})")->save(array("utype"=>'1'));
			}
			
			echo "<script>alert ('审核成功'); location.href='".__CONTROLLER__."';</script>";
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//撤销审核
	public function unaudit(){
		$verify=D("Verify");
		$ids=@implode(",",$_POST["del"]);
		if($ids!=""){
			$verify->where("id in ({$ids})")->save(array("ispass"=>"0"));
			
			//商家恢复为会员
			$vdata=$verify->field("uid")->where("id in ({$ids})")->select();
			if(count($vdata)>0){
				foreach($vdata as $v){
					$uidarr[]=$v["uid"];
				}
				$uids=@implode(",",$uidarr);
				$user=M("User");
				$user->where("qid in ({$uids})")->save(array("utype"=>'0'));
			}
			echo "<script>alert ('审撤销核成功'); location.href='".__CONTROLLER__."';</script>";
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//审核失败原因
	public function fail(){
		if($this->action=='add'){
			$albumid=get_str($_POST["albumid"]);
			$failcontent=get_str($_POST['failcontent']);
			if($albumid!="" && $failcontent!=""){
			$verify=D("Verify");
			$data["fail"]=$failcontent;
			$data["ispass"]="-1";
			$verify->where("id in ({$albumid})")->save($data);
			
			//商家恢复为会员
			$vdata=$verify->field("uid")->where("id in ({$albumid})")->select();
			if(count($vdata)>0){
				foreach($vdata as $v){
					$uidarr[]=$v["uid"];
				}
				$uids=@implode(",",$uidarr);
				$user=M("User");
				$user->where("qid in ({$uids})")->save(array("utype"=>'0'));
			}
			
			echo "<script>alert('提交成功！');location.href='".__ACTION__."';window.opener.location.reload();</script>";
			}
		}
		$this->display();	
	}
	
}