<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\UploadFileOrg;
class PublicController extends Controller{
	public $id,$pid,$action,$cid,$parentid,$kwords,$page,$pg,$cardid;
	public function __construct(){
		parent::__construct();
		$this->kwords=get_str((trim($_REQUEST["kwords"])));
		/*$this->kwords=str_replace("%","\%",$this->kwords);
		$this->kwords=str_replace("_","\_",$this->kwords);*/
		$this->kwords=urlencode($this->kwords);
		$this->assign("kwords",$this->kwords);
		
		$this->action=$_GET["action"];
		$this->assign("action",$this->action);
		
		$this->id=$_GET["id"];
		$this->assign("id",$this->id);
		
		$this->pid=$_GET["pid"];
		$this->assign("pid",$this->pid);
		
		$this->cid=$_GET["cid"];
		$this->assign("cid",$this->cid);
		
		$this->parentid=$_GET["parentid"];
		$this->assign("parentid",$this->parentid);
		
		$this->page=get_int($_REQUEST["page"]);
		$this->assign("page",$this->page);
		
		$this->pg=$_REQUEST["pg"];
		$this->assign("pg",$this->pg);
		
		$this->cardid=get_int($_GET["cardid"]);
		$this->assign("cardid",$this->cardid);
		
		//上次登录时间
		$this->assign("logintime",$_SESSION["logintime"]);
		
		//用户名
		$this->assign("adminname",$_SESSION["adminname"]);
		
		//用户组
		$this->assign("groupname",$_SESSION["groupname"]);

		//超级管理员分组id
        $this->assign("gid",$_SESSION["gid"]);
	}
	
	//执行其它版块权限
	protected function setOtherAllot($id){
		$admin=M("Admin");
		$data=$admin->where("qid=%d",array($_SESSION["adminid"]))->find();
		$f_path=@explode(",",$data["f_path"]);
		if(!(in_array($id,$f_path) || $_SESSION["gid"]==1)){
			echo "<center><img src='".__ROOT__."/Public/admin/images/qx.gif' width='60' height='60' /> 对不起您没有权限访问!请联系管理员.</center>";
			exit;
		}
	}
	
	//管理员添加权限
	protected function addsql(){
		if(!($_SESSION["shell"]&ADD) && $_SESSION["gid"]!=1){
			echo "<center><img src='".__ROOT__."/Public/admin/images/qx.gif' width='60' height='60' /> 对不起,您没有<font color='#ff0000'>添加</font>权限,请联系管理员!</center>";
			exit;
		}
	}
	
	//管理员修改权限
	protected function modsql(){
		if(!($_SESSION["shell"]&MOD) && $_SESSION["gid"]!=1){
			echo "<center><img src='".__ROOT__."/Public/admin/images/qx.gif' width='60' height='60' /> 对不起,您没有<font color='#ff0000'>修改</font>权限,请联系管理员!</center>";
			exit;
		}
	}
	
	//管理员删除权限
	protected function delsql(){
		if(!($_SESSION["shell"]&DEL) && $_SESSION["gid"]!=1){
			echo "<center><img src='".__ROOT__."/Public/admin/images/qx.gif' width='60' height='60' /> 对不起,您没有<font color='#ff0000'>删除</font>权限,请联系管理员!</center>";
			exit;
		}
	}
	
	protected function isLogin(){
		if($_SESSION["loginjk"]!=1){
			header("location:".__MODULE__."/Index/");
			exit;
		}	
	}
	
	//上传文件
	public function upload(){
		$this->isLogin();
		$setting=M("Setting");
		$sdata=$setting->field("filetype,filesize")->find();
		$filetype=$sdata["filetype"];//获取上传类型
		$arrfiletype=explode(",",$filetype);//类型分隔为数组
		$getfsize=$sdata["filesize"];//获取上传大小
		//图片上传
		if($this->action=='images'){
			$uf=new UploadFileOrg();
			$uf->upfileload('./uploadfiles',array("jpg","gif","png","jpeg"),$getfsize,$this->action);
		}
		//缩略图上传
		if($this->action=='thumb'){
			$uf=new UploadFileOrg();
			$uf->upfileload('./uploadfiles',array("jpg","gif","png","jpeg"),$getfsize,$this->action);
		}
		//视频上传
		if($this->action=='video'){
			$uf=new UploadFileOrg();
			$uf->upfileload('./videofiles',array("mp4"),$getfsize);
		}
		//文件上传
		if($this->action=='down'){
			$uf=new UploadFileOrg();
			$uf->upfileload('./downfiles',$arrfiletype,$getfsize);
		}
		//上传头像
		if($this->action=='head'){
			$uf=new UploadFileOrg();
			$uf->upfileload('./userfiles/head',array("jpg","gif","png","jpeg"),$getfsize,$this->action);
		}
		
		//会员发布活动图片
		if($this->action=='uimage'){
			$uf=new UploadFileOrg();
			$uf->upfileload('./userfiles/images',array("jpg","gif","png","jpeg"),$getfsize,$this->action);
		}
	}
	
}