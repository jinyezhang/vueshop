<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\AdminModel;
use Home\Model\LogModel;
class AdminManageController extends CommonController{
	
	public function index(){
		
		$this->display();
	}
	
	public function manage(){
		//获取管理员分组
		$group=M("AdminGroup");
		$gdata=$group->order("id asc")->select();
		$this->assign("gdata",$gdata);
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		$admin=D("Admin");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$admin->join("left join __ADMIN_GROUP__ on __ADMIN__.gid=__ADMIN_GROUP__.id")->where("__ADMIN__.adminname like '%s'",array("%".urldecode($this->kwords)."%"))->count();
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$admin->getAdminPage($pageInfo["row_offset"],$pageInfo["row_num"],urldecode($this->kwords));
		if($datalist){
			
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__ACTION__."?",$strname));
		}
		
		$this->display();	
	}
	
	public function add(){
		$admin=D("Admin");
		if(!$admin->create()){
			echo "<script>alert('".$admin->getError()."');history.go(-1)</script>";
		}else{
			$adminname=get_str(trim($_POST["adminname"]));
			$total=$admin->where("adminname='%s'",array($adminname))->count();
			if($total>0){
				echo "<script>alert('该用户名已存在！');history.go(-1)</script>";
			}else{
				$qid=uniqueId();
				$admin->qid=$qid;
				$admin->content=get_str(delspace($_POST["content"]));
				$uid=$admin->add();
				
				//获取功能表里的数据
				$funset=M("Funset");
				$fdata=$funset->order("id asc")->field("id,title")->select();
				
				//将功能表里的数据添加到我的桌面
				for($i=0;$i<count($fdata);$i++){
					$datalist[]=array("uid"=>$qid,"fid"=>$fdata[$i]["id"],"title"=>$fdata[$i]["title"],"pic_path"=>mt_rand(1,40).".png");
				}
				if(count($datalist)>0){
					$desktop=M("Desktop");
					$desktop->addAll($datalist);
				}
				LogModel::setLog("添加管理员“{$adminname}”","添加");
				echo "<script>alert('添加成功');location.href='".__CONTROLLER__."/manage'</script>";
				exit;
			}
		}
	}
	
	public function edit(){
		$group=M("Admin_group");
		$gdata=$group->order("id asc")->field("id,groupname")->select();
		$this->assign("gdata",$gdata);
		
		$admin=M("Admin");
		$data=$admin->join("left join __ADMIN_GROUP__ on __ADMIN__.gid=__ADMIN_GROUP__.id")->where("__ADMIN__.id=%d",array($this->id))->field("__ADMIN__.id,__ADMIN__.adminname,__ADMIN__.gid,groupname")->find();
		$this->assign("data",$data);
		$this->display();	
	}
	
	public function mod(){
		if($this->id>0){
			$gid=get_int($_POST["gid"]);
			$adminname=get_str(trim($_POST["adminname"]));
			$password=get_str(md5(trim($_POST["password"])));
			$qpwd=get_str(md5(trim($_POST["qpwd"])));
			if($gid>0 && $adminname!="" && $password!="" && $qpwd!=""){
				if($password!=$qpwd){
					echo "<script>alert('您输入的密码不一致');history.go(-1)</script>";
				}else{
					$admin=M("Admin");
					$data["gid"]=$gid;
					$data["adminname"]=$adminname;
					$data["password"]=$password;	
					$admin->where("id=%d",array($this->id))->save($data);
					LogModel::setLog("修改管理员“{$adminname}”","修改");
					echo "<script>alert('修改成功！');location.href='".__CONTROLLER__."/edit?id={$this->id}'</script>";
					exit;
				}
			}
		}
	}
	
	public function del(){
		$admin=D("Admin");
		$admin->delAdmin();
	}
	
	//设置添加权限
	public function addper(){
		$admin=D("Admin");
		$admin->setAddSql();
	}
	
	//取消添加权限
	public function clearadd(){
		$admin=D("Admin");
		$admin->clearAddSql();	
	}
	
	//启用修改权限
	public function modper(){
		$admin=D("Admin");
		$admin->setModSql();
	}
	
	//取消修改权限
	public function clearmod(){
		$admin=D("Admin");
		$admin->clearModSql();	
	}
	
	//启用删除权限
	public function delper(){
		$admin=D("Admin");
		$admin->setDelSql();
	}
	
	//取消删除权限
	public function cleardel(){
		$admin=D("Admin");
		$admin->clearDelSql();
	}
	
}