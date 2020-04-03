<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\LogModel;
class NewsListController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
	}
	
	public function index(){
		$this->safeColumn($this->id);
		$this->display();
	}
	
	public function left(){
		$column=D("Columns");
		$cdata=$column->where("id=%d",array($this->id))->field("fun")->find();
		$this->assign("cdata",$cdata);
		$this->display();	
	}
	
	public function manage(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->id))->field("fun")->find();
		$this->assign("cdata",$cdata);
		
		//文章移动无限级分类
		$this->assign("moveselpcls",$column->moveselpcls(0,0,0,$this->id));
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}
		
		$art=M("Article");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$art->where("parentid=%d and title like '%s'",array($this->id,"%".urldecode($this->kwords)."%"))->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$art->where("parentid=%d and title like '%s'",array($this->id,"%".urldecode($this->kwords)."%"))->field("id,dates,title,num,ishome,isrecom,viewnum")->order("num desc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__ACTION__."?",$strname."&id={$this->id}"));
		}
		
		$this->display();
	}
	
	//推荐首页
	public function rechome(){
		if($this->id>0){
			$art=M("Article");
			$arr=$art->where("id=%d",array($this->id))->save(array("ishome"=>get_int($_GET["rec"])));
			header("location: ".$_SERVER['HTTP_REFERER']."");
			exit;
		}
	}
	
	//排序
	public function order(){
		$art=M("Article");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$art->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."/manage?id={$this->id}'</script>";
		exit;
	}
	
	//移动数据
	public function movedata(){
		$this->modsql();
		$column=D("Columns");
		$column->moveColumn();
		$del=@implode(",",$_POST["del"]);
		$colid=get_int($_POST["columns"]);
		if($del!=""){
			$art=M("Article");
			$art->where("id in ({$del})")->save(array("parentid"=>$colid));
			header("location:".__CONTROLLER__."/manage?id={$this->id}");
			exit;
		}else{
			echo "<script>alert('请选择要移动的数据');history.go(-1)</script>";	
		}
	}
	
	//删除
	public function del(){
		$this->delsql();
		$art=D("Article");
		$art->artDel($this->id);
	}
	
	//多篇
	public function macontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$this->assign("date",date("Y-m-d"));
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->bodys=$bodys;
				$art->parentid=$this->id;
				$art->add();
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//多篇修改
	public function editmacontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,dates,bodys,isrecom,image,source")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			$columns=get_int($_POST["columns"]);
			$isrecom=get_int($_POST["isrecom"]);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->bodys=$bodys;
				$art->parentid=$columns;
				$art->where("id=%d",array($this->id))->save();
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
				exit;
			}
			
		}
		
		$this->display();
	}
	
	//链接
	public function lnkcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			if($title!=""){
				$art=M("Article");
				$art->create();
				$data["dates"]=date("Y-m-d");
				$art->parentid=$this->id;
				$art->num=999;
				$art->add();
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//链接修改
	public function editlnkcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,webs,photo")->find();
		$this->assign("data",$data);
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$title=get_str(trim($_POST["title"]));
			$columns=get_int($_POST["columns"]);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->parentid=$columns;
				$art->where("id=%d",array($this->id))->save();
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
				exit;
			}
			
		}
		
		$this->display();
	}
	
	//下载
	public function downcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$setting=M("Setting");
		$getset=$setting->field("filetype")->find();
		$this->assign("getset",$getset);
		
		$this->assign("date",date("Y-m-d"));
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->parentid=$this->id;
				$art->num=999;
				$art->bodys=$bodys;
				$art->add();
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//下载修改
	public function editdowncontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$setting=M("Setting");
		$getset=$setting->field("filetype")->find();
		$this->assign("getset",$getset);
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,down,dates,bodys")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$title=get_str(trim($_POST["title"]));
			$columns=get_int($_POST["columns"]);
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->parentid=$columns;
				$art->bodys=$bodys;
				$art->where("id=%d",array($this->id))->save();
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
				exit;
			}
			
		}
		
		$this->display();
	}
	
	//积分
	public function intecontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->bodys=$bodys;
				$art->dates=date("Y-m-d");
				$art->parentid=$this->id;
				$art->num=999;
				$art->add();
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//积分修改
	public function editintecontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,inte,bodys,photo")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			$columns=get_int($_POST["columns"]);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->parentid=$columns;
				$art->bodys=$bodys;
				$art->where("id=%d",array($this->id))->save();
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
				exit;
			}
			
		}
		
		$this->display();
	}
	
	//视频
	public function vocontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$this->assign("date",date("Y-m-d"));
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			$wtype=get_str($_POST["wtype"]);
			if($title!=""){
				if($wtype=='app'){
					$video=get_str($_POST["video"]);
				}else if($wtype=='web'){
					$video=get_str($_POST["webvideo"]);
				}
				$art=M("Article");
				$art->create();
				$art->bodys=$bodys;
				$art->parentid=$this->id;
				$art->num=999;
				$art->video=$video;
				$art->add();
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//视频修改
	public function editvocontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,dates,bodys,video,cover,vtype,wtype")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			$columns=get_int($_POST["columns"]);
			$wtype=get_str($_POST["wtype"]);
			if($title!=""){
				if($wtype=='app'){
					$video=get_str($_POST["video"]);
				}else if($wtype=='web'){
					$video=get_str($_POST["webvideo"]);
				}
				$art=M("Article");
				$art->create();
				$art->bodys=$bodys;
				$art->parentid=$columns;
				$art->video=$video;
				$art->where("id=%d",array($this->id))->save();
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
				exit;
			}
			
		}
		
		$this->display();
	}
	
	//图库
	public function pics(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$this->assign("date",date("Y-m-d"));
		
		if($this->action=='add'){
			$this->addsql();
			$art=D("Article");
			$art->addPics($this->id);
		}
		
		$this->display();
	}
	
	//图库修改
	public function editpics(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,dates,bodys,model")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		
		$pics=M("Pics");
		$imgs=$pics->where("pid=%d",array($this->id))->field("id,photo")->select();
		$this->assign("imgs",$imgs);
		
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$art=D("Article");
			$art->modPics($this->id,$this->cid,$this->page);
			
		}
		
		$this->display();
	}
	
	//删除图片
	public function delimg(){
		$pid=get_int($_GET["pid"]);
		$imgid=get_int($_GET["imgid"]);
		$this->assign("id",$pid);
		if($imgid>0){
			$pics=M("Pics");
			$pics->where("id=%d",array($imgid))->delete();
		}
		$pics=M("Pics");
		$imgs=$pics->where("pid=%d",array($pid))->field("id,photo")->select();
		$this->assign("imgs",$imgs);
		
		$this->display();	
	}
	
	//内容列表
	public function cardlist(){
		
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		//获取栏目名称
		$art=M("Article");
		$adata=$art->where("id=%d",array($this->id))->field("title")->find();
		$this->assign("adata",$adata);
		
		$content=M("Content");
		$current_page=isset($_REQUEST["pg"])?intval($_REQUEST["pg"]):1;
		$this->assign("cupg",$current_page);
		$total=$content->where("pid=%d",array($this->id))->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$content->where("pid=%d",array($this->id))->field("id,title,num")->order("num asc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage2($current_page,__ACTION__."?","&id={$this->id}&cid={$this->cid}&page={$this->page}"));
		}
		
		//修改排序
		if($this->action=='order'){
			$content=M("Content");
			for ($i=0;$i<count($_POST["num"]);$i++){
				$content->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
			}
			echo "<script>alert ('排序修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
			exit;
		}
		
		//删除
		if($this->action=="del"){
			$del=@implode(",",$_POST["del"]);
			if($del!=""){
				$content=M("Content");
				$content->where("id in ({$del})")->delete();
				echo "<script>alert('删除成功');location.href='".__ACTION__."?id={$this->id}&cid={$this->cid}&page={$this->page}&pg={$this->pg}'</script>";
				exit;
			}else{
				echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";	
			}
		}
		
		$this->display();
	}
	
	//添加选项卡内容
	public function cardcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		//获取栏目名称
		$art=M("Article");
		$adata=$art->where("id=%d",array($this->id))->field("title")->find();
		$this->assign("adata",$adata);
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$content=M("Content");
				$data["title"]=$title;
				$data["bodys"]=$bodys;
				$data["pid"]=$this->id;
				$data["num"]=999;
				$content->add($data);
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}&cid={$this->cid}&page={$this->page}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//修改选项卡内容
	public function editcardcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		//获取栏目名称
		$art=M("Article");
		$adata=$art->where("id=%d",array($this->id))->field("title")->find();
		$this->assign("adata",$adata);
		
		$content=M("Content");
		$data=$content->where("id=%d",array($this->cardid))->field("title,bodys")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		if($this->action=='mod'){
			$this->modsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$content=M("Content");
				$data["title"]=$title;
				$data["bodys"]=$bodys;
				$content->where("id=%d",array($this->cardid))->save($data);
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&cid={$this->cid}&page={$this->page}&pg={$this->pg}&cardid={$this->cardid}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
}