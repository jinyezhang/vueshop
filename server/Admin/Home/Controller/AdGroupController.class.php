<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\LogModel;
class AdGroupController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(19);
	}
	
	public function index(){
		$group=M("AdGroup");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$group->count();
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$group->order("num asc,id asc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		$this->display();
	}
	
	public function add(){
		$title=get_str(trim($_POST["title"]));
		if($title!=""){
			$group=M("AdGroup");
			$total=$group->where("title='%s'",array($title))->count();
			if($total>0){
				echo "<script>alert('您输入的名称已存在！');history.go(-1)</script>";
			}else{
				$group->create();
				$group->add();
				LogModel::setLog("添加广告分组“{$title}”","添加");
				echo "<script>alert ('添加成功！');location.href='".__CONTROLLER__."'</script>";
				exit;
			}
		}else{
			echo "<script>alert('请输入广告分组名称');history.go(-1)</script>";	
		}
	}
	
	public function edit(){
		$group=M("AdGroup");
		$getgroup=$group->where("id=%d",array($this->id))->find();
		$this->assign("getgroup",$getgroup);
		$this->display();
	}
	
	public function mod(){
		if($this->id>0){
			$title=get_str(trim($_POST["title"]));
			if($title!=""){
				$group=M("AdGroup");
				$group->where("id=%d",array($this->id))->save(array("title"=>$title));
				LogModel::setLog("修改广告分组“{$title}”","修改");
				echo "<script>alert('修改成功！');location.href='".__CONTROLLER__."'</script>";
				exit;
			}
		}
	}
	
	public function del(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$group=M("AdGroup");
			$group->where("id in ({$del})")->delete();
			$ad=M("Ad");
			$ad->where("gid in ({$del})")->delete();
			LogModel::setLog("删除广告分组","删除");
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";	
		}	
	}
	
	//排序
	public function order(){
		$ad=M("AdGroup");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$ad->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."'</script>";
		exit;
	}
	
}