<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\LogModel;
class AdminGroupController extends CommonController{
	
	public function index(){
		$group=M("AdminGroup");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$group->count();
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$group->order("id asc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		$this->display();
	}
	
	public function add(){
		$groupname=get_str(trim($_POST["groupname"]));
		if($groupname!=""){
			$group=M("AdminGroup");
			$total=$group->where("groupname='%s'",array($groupname))->count();
			if($total>0){
				echo "<script>alert('您输入的名称已存在！');history.go(-1)</script>";
			}else{
				$group->create();
				$group->add();
				LogModel::setLog("添加管理员分组“{$groupname}”","添加");
				echo "<script>alert ('添加成功！');location.href='".__CONTROLLER__."'</script>";
				exit;
			}
		}else{
			echo "<script>alert('请输入管理员级别名称');history.go(-1)</script>";	
		}
	}
	
	public function edit(){
		$group=M("AdminGroup");
		$getgroup=$group->where("id=%d",array($this->id))->find();
		$this->assign("getgroup",$getgroup);
		$this->display();
	}
	
	public function mod(){
		if($this->id>0){
			$groupname=get_str(trim($_POST["groupname"]));
			if($groupname!=""){
				$group=M("AdminGroup");
				$group->where("id=%d",array($this->id))->save(array("groupname"=>$groupname));
				LogModel::setLog("修改管理员分组“{$groupname}”","修改");
				echo "<script>alert('修改成功！');location.href='".__CONTROLLER__."'</script>";
				exit;
			}
		}
	}
	
	public function del(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$group=M("AdminGroup");
			$group->where("id in ({$del})")->delete();
			LogModel::setLog("删除管理员分组","删除");
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";	
		}	
	}
	
}