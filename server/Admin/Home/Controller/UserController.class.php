<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class UserController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(11);
	}
	
	public function index(){
		
		$this->display();
	}
	
	public function manage(){
		if($this->kwords!=""){
			$strname.="&kwords=".$this->kwords;
			$this->assign("kwords",urldecode($this->kwords));
		}
		$screen=get_int($_REQUEST["screen"]);
		$this->assign("screen",$screen);
		if($screen>0){
			$strname.="&screen=".$screen;
		}
		$utype=get_int($_GET["utype"]);
		$this->assign("utype",$utype);
		$strname.="&utype=".$utype;
		$timeorder=get_str($_GET["timeorder"]);
		if($timeorder!=""){
			$strname.="&timeorder=".$timeorder;
			$this->assign("timeorder",$timeorder);
		}
		$this->assign("strname",$strname);
		$user=D("User");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$user->getUserTotal(urldecode($this->kwords),$screen,$utype);
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$user->getUserPage($pageInfo["row_offset"],$pageInfo["row_num"],urldecode($this->kwords),$screen,$utype,$timeorder);
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__ACTION__."?",$strname.""));
		}
		$this->display();
	}
	
	public function del(){
		$this->delsql();
		$user=D("User");
		$user->delUser();
	}
	
	public function edit(){
		$this->modsql();
		if($this->kwords!=""){
			$strname.="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		$screen=get_int($_REQUEST["screen"]);
		if($screen>0){
			$strname.="&screen=".$screen;
		}
		$utype=get_int($_GET["utype"]);
		$strname.="&utype=".$utype;
		$this->assign("strname",$strname);
		$user=M("User");
		$udata=$user->where("qid='%s'",array($this->id))->find();
		$this->assign("udata",$udata);
		
		if($this->action=='mod'){
			$password=get_str(trim($_POST['password']));
			$head=get_str($_POST["head"]);
			$nickname=get_str($_POST["nickname"]);
			$cellphone=get_str($_POST["cellphone"]);
            $gender=get_int($_POST["gender"]);
			if($this->id!='' && $cellphone!=""){
				if($password!=""){
					$user->create();
					$user->password=md5($password);
					$user->where("qid='%s'",array($this->id))->save();
				}else{
					$data["head"]=$head;
					$data["nickname"]=$nickname;
					$data["cellphone"]=$cellphone;
                    $data["gender"]=$gender;
					$user->where("qid='%s'",array($this->id))->save($data);
				}
				echo "<script>alert('修改成功！');location.href='".__ACTION__."?id={$this->id}&page={$this->page}{$strname}'</script>";
				exit;
			}
			
		}
		
		$this->display();
	}
	
	//成为商家
	public function audit(){
		$ids=@implode(",",$_POST["del"]);
		$utype=get_int($_GET["utype"]);
		if($ids!=""){
			$user=M("User");
			$user->where("qid in ({$ids})")->save(array("utype"=>"1"));
			
			$verify=M("Verify");
			$verify->where("uid in ({$ids})")->save(array("ispass"=>'1'));
			echo "<script>alert ('审核成功'); location.href='".__CONTROLLER__."/manage?utype={$utype}';</script>";
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//撤销商家
	public function unaudit(){
		$ids=@implode(",",$_POST["del"]);
		$utype=get_int($_GET["utype"]);
		if($ids!=""){
			$user=M("User");
			$user->where("qid in ({$ids})")->save(array("utype"=>"0"));
			
			$verify=M("Verify");
			$verify->where("uid in ({$ids})")->save(array("ispass"=>'0'));
			echo "<script>alert ('撤销审核成功'); location.href='".__CONTROLLER__."/manage?utype={$utype}';</script>";
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//添加会员
	public function add(){
		$this->addsql();
		if($this->action=='add'){
			$password=get_str(trim(md5($_POST['password'])));
			$cellphone=get_str($_POST["cellphone"]);
			if($password!="" && $cellphone!=""){
				$user=M("User");
				
				$udata=$user->field("cellphone")->where("cellphone='%s'",array($cellphone))->find();
				if($udata['cellphone']==$cellphone){
					echo "<script>alert('此用户名已注册过！');history.go(-1)</script>";
				}else{
					$user->create();
					$user->qid=uniqueId();
					$user->times=date("Y-m-d H:i:s");
					$user->password=$password;
					$user->add();
					echo "<script>alert('添加成功！');location.href='".__ACTION__."'</script>";
				}
			}else{
				echo "<script>alert('请填写必填项');history.go(-1)</script>";	
			}
			
		}
		$this->display();
	}
	
}