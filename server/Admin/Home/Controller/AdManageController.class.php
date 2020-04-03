<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\AdminModel;
use Home\Model\LogModel;
class AdManageController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(19);
	}

    public function index(){

        $this->display();
    }

    public function left(){

        $this->display();
    }

	public function manage(){
		//获取广告分组
		$group=M("AdGroup");
		$gdata=$group->order("num asc,id asc")->select();
		$this->assign("gdata",$gdata);

		$ad=M("Ad");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$ad->join("inner join __AD_GROUP__ on __AD__.gid=__AD_GROUP__.id")->count();
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$ad->join("inner join __AD_GROUP__ on __AD__.gid=__AD_GROUP__.id")->field("__AD__.title,__AD__.id,__AD_GROUP__.title as gtitle,__AD__.photo,__AD__.num,__AD__.webs")->order("__AD__.num desc,__AD__.id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."/manage?",$strname));
		}
		
		$this->display();
	}
	
	public function add(){
		$title=get_str($_POST["title"]);
		if($title!=""){
			$ad=M("Ad");
			$ad->create();
			$ad->add();
			LogModel::setLog("添加广告“{$title}”","添加");
			echo "<script>alert('添加成功');location.href='".__CONTROLLER__."/manage'</script>";
			exit;
		}
	}
	
	public function edit(){
		$group=M("Ad_group");
		$gdata=$group->order("num asc,id asc")->field("id,title")->select();
		$this->assign("gdata",$gdata);
		
		$ad=M("Ad");
		$data=$ad->join("inner join __AD_GROUP__ on __AD__.gid=__AD_GROUP__.id")->where("__AD__.id=%d",array($this->id))->field("__AD__.id,__AD__.title,__AD__.gid,__AD_GROUP__.title as gtitle,__AD__.photo,__AD__.webs")->find();
		$this->assign("data",$data);
		$this->display();	
	}
	
	public function mod(){
		if($this->id>0){
			$gid=get_int($_POST["gid"]);
			$title=get_str($_POST["title"]);
			$photo=get_str($_POST["photo"]);
			$webs=get_str($_POST["webs"]);
			if($gid>0 && $title!="" && $photo!="" && $webs!=""){
				$ad=M("Ad");
				$data["gid"]=$gid;
				$data["title"]=$title;
				$data["photo"]=$photo;
				$data["webs"]=$webs;
				$ad->where("id=%d",array($this->id))->save($data);
				LogModel::setLog("修改广告“{$title}”","修改");
				echo "<script>alert('修改成功！');location.href='".__CONTROLLER__."/edit?id={$this->id}'</script>";
				exit;
			}
		}
	}
	
	public function del(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$ad=M("Ad");
			$ad->where("id in ({$del})")->delete();
			LogModel::setLog("删除广告","删除");
			echo "<script>{alert ('删除成功'); location.href='".__CONTROLLER__."/manage';}</script>";
			exit;
		}else{
			echo "<script>{alert ('请选择要删除的数据'); history.go(-1);}</script>";	
		}
	}
	
	//排序
	public function order(){
		$ad=M("Ad");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$ad->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."/manage'</script>";
		exit;
	}
	
}