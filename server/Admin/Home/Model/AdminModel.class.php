<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class AdminModel extends Model{
	protected $pdo,$prinfo;
	protected $_validate=array(
		array("username","require","请输入您的账号",0),
		array("password","require","请输入您的密码"),
		array("vdcode","require","请输入验证码",0),
		array('vdcode','checkCode','验证码错误!',0,'callback',1),
		array("adminname","require","请输入用户名",0),
		array("gid","require","请选择管理员分组",0),
		array("password","qpwd","您输入的密码不一致",0,"confirm"),
	);
	
	protected $_auto=array(
		array("shell","0"),
		array("logintime","getDate",1,"callback"),
		array("password","md5",3,"function")
	);
	
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	protected function getDate(){
		return date('Y-m-d H:i:s');	
	}
	
	protected function checkCode($code){
		if(strtoupper($code)!=$_SESSION['imgcode']){
			return false;
		}else{
			return true;
		}
	}
	
	public function getAdminPage($offset,$num,$kwords){
		$sql="select a.adminname,g.groupname,a.shell,a.id,a.gid,a.qid from __ADMIN__ as a left join __ADMIN_GROUP__ as g on a.gid=g.id where a.adminname like '%{$kwords}%'";
		$sql.=" order by a.id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		while($list=$stmt->fetch()){
			if($list['gid']==1){
				$printf="<font color=\"#0000FF\">权限开放</font>";
			}else if($list["shell"]!=0){
					if($list['shell']&ADD){
						$printf.="添加权限,";
						$chk=1;
					}
					if($list["shell"]&MOD){
						$printf.= "修改权限,";
					}
					
					if($list["shell"]&DEL){
						$printf.= "删除权限,";
						
					}
			}else{
						$printf="<font color=\"#FF0000\">未分配权限</font>";	
					}
			$allist[]=array(
				'adminname'=>$list['adminname'],
				'groupname'=>$list['groupname'],
				'shell'=>$list['shell'],
				'id'=>$list['id'],
				'gid'=>$list['gid'],
				"qid"=>$list["qid"],
				'printf'=>rtrim($printf,",")
			);
			unset($printf);
		}
		return $allist;
	}
	
	//删除管理员
	public function delAdmin(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="select gid from __ADMIN__ where qid in ({$del})";
			$query=$this->pdo->prepare(MyModel::parseSql($sql));
			$query->execute();
			$rs=$query->fetch();
			if($rs["id"]==36){
				echo "<script>{alert ('不能删除此用户');history.go(-1);}</script>";	
			}
			else{
				$sql="delete from __DESKTOP__ where uid in ({$del});delete from __ADMIN__ where qid in ({$del})";
				$query=$this->pdo->prepare(MyModel::parseSql($sql));
				$query->execute();
				LogModel::setLog("删除管理员","删除");
				echo "<script>{alert ('删除成功'); location.href='".__CONTROLLER__."/manage';}</script>";
				exit;
			}
		}else{
			echo "<script>{alert ('请选择要删除的管理员'); history.go(-1);}</script>";	
		}	
	}
	
	//判断用户是否分配过权限
	protected function chkShell($del){
		$sql="select shell,qid from __ADMIN__ where qid in ({$del})";
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute();
		while($list=$query->fetch()){
			$alldata[]=array(
				'shell'=>$list["shell"],
				'qid'=>$list["qid"]
			);
		}
		return $alldata;
	}
	
	//设置添加权限
	public function setAddSql(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			//判断用户是否分配过权限并分配权限
			$shellData=$this->chkShell($del);
			$sql="update __ADMIN__ set shell=shell+".ADD." where qid=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			foreach($shellData as $v){
				if(!($v["shell"]&ADD)){
					$stmt->execute(array($v["qid"]));
				}
			}
			echo ("<script>alert ('添加权限设置成功!');location.href='".__CONTROLLER__."/manage'</script>");
			exit;
		}else{
			echo "<script>alert('请选中管理员');history.go(-1)</script>";	
		}	
	}
	
	//取消添加权限
	public function clearAddSql(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$shellData=$this->chkShell($del);
			$sql="update __ADMIN__ set shell=shell-".ADD." where qid=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			foreach($shellData as $v){
				if($v["shell"]&ADD){
					$stmt->execute(array($v["qid"]));
				}
			}
			echo ("<script>alert ('添加权限取消成功!');location.href='".__CONTROLLER__."/manage'</script>");
			exit;
		}else{
			echo "<script>alert('请选中管理员');history.go(-1)</script>";	
		}	
	}
	
	//设置修改权限
	public function setModSql(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$shellData=$this->chkShell($del);
			$sql="update __ADMIN__ set shell=shell+".MOD." where qid=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			foreach($shellData as $v){
				if(!($v["shell"]&MOD)){
					$stmt->execute(array($v["qid"]));
				}
			}
			echo ("<script>alert ('修改权限设置成功!');location.href='".__CONTROLLER__."/manage'</script>");
			exit;
		}else{
			echo "<script>alert('请选中管理员');history.go(-1)</script>";	
		}	
	}
	
	//取消修改权限
	public function clearModSql(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$shellData=$this->chkShell($del);
			$sql="update __ADMIN__ set shell=shell-".MOD." where qid=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			foreach($shellData as $v){
				if($v["shell"]&MOD){
					$stmt->execute(array($v["qid"]));
				}
			}
			echo ("<script>alert ('修改权限取消成功!');location.href='".__CONTROLLER__."/manage'</script>");
			exit;
		}else{
			echo "<script>alert('请选中管理员');history.go(-1)</script>";	
		}	
	}
	
	//设置删除权限
	public function setDelSql(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$shellData=$this->chkShell($del);
			$sql="update __ADMIN__ set shell=shell+".DEL." where qid=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			foreach($shellData as $v){
				if(!($v["shell"]&DEL)){
					$stmt->execute(array($v["qid"]));
				}
			}
			echo ("<script>alert ('删除权限设置成功!');location.href='".__CONTROLLER__."/manage'</script>");
			exit;
		}else{
			echo "<script>alert('请选中管理员');history.go(-1)</script>";	
		}	
	}
	
	//设置删除权限
	public function clearDelSql(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$shellData=$this->chkShell($del);
			$sql="update __ADMIN__ set shell=shell-".DEL." where qid=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			foreach($shellData as $v){
				if($v["shell"]&DEL){
					$stmt->execute(array($v["qid"]));
				}
			}
			echo ("<script>alert ('删除权限取消成功!');location.href='".__CONTROLLER__."/manage'</script>");
			exit;
		}else{
			echo "<script>alert('请选中管理员');history.go(-1)</script>";	
		}	
	}
	
	//栏目权限设置目录树
	public function jurimenu($id){
		$userid=get_int($_REQUEST["userid"]);
		$sql="select id,c_names,num,parent_id,parentpath from __COLUMNS__ where parent_id={$id} order by num asc,id asc";	
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute();
		$total=$query->rowCount();
		$csql="select id from __COLUMNS__ where parent_id=?";
		$ChildCount=$this->pdo->prepare(MyModel::parseSql($csql));
		$asql="select c_path from __ADMIN__ where c_path like ? and id=?";
		$aquery=$this->pdo->prepare(MyModel::parseSql($asql));
			while($v=$query->fetch()){
				$ChildCount->execute(array($v["id"]));
				$counts=$ChildCount->rowCount();
				$pathcount=count(@explode(",",$v["parentpath"]));
				$aquery->execute(array("%".$v["id"]."%",$userid));
				$this->prinfo.="<div class='outerdiv2' onmouseover='overColumn(this)' onmouseout='outColumn(this)' id='c".$v["id"]."'";
				$this->prinfo.=">
				<div class='movediv'>";
				if($v["parent_id"]==0){
					$this->prinfo.="<div style='width:48%;height:100%;float:left;margin-top:10px;position:relative;' class='back14'><img src='".__ROOT__."/Public/admin/images/minsign.jpg' />&nbsp;&nbsp;".$v["c_names"]."<a id='".$v["id"]."'></a></div>";
				}else{
					$this->prinfo.="<div style='width:48%;height:100%;float:left;margin-top:10px;'>
					<div style='margin-left:".($pathcount*20)."px;'><span class='";
					if($pathcount==2){
						$this->prinfo.="twobg";
					}else if($pathcount==3){
						$this->prinfo.="thrbg";	
					}else{
						$this->prinfo.="fourbg";	
					}
					$this->prinfo.="'>".$pathcount."级</span>&nbsp;&nbsp;".$v["c_names"]."<a id='".$v["id"]."'></a></div>
					</div>";
				}
				$this->prinfo.="<div style='width:23%;height:100%;float:left;margin-top:10px;'></div>
				<div style='width:20%;height:100%;float:left;margin-top:13px;'></div>
				<div style='width:8%;height:100%;float:left;margin-top:10px;'>";
				$this->prinfo.="<input type='checkbox' name='cid[]' id='dp".$v["id"]."' value='".$v["id"]."' ";
				$ars=$aquery->fetch();
				if(in_array($v["id"],@explode(",",$ars["c_path"]))){
					$this->prinfo.="checked='checked'";
				}
				$this->prinfo.=" />";
				$this->prinfo.="</div>
				</div>
				</div>";
				$this->prinfo.="<div class='pdiv' pvar='1'>";
				$this->jurimenu($v["id"]);
				$this->prinfo.="</div>";
		}
		return $this->prinfo;
	}
	
	//分配其他版块权限
	public function allowFunset($userid){
		$sql="select f_path from __ADMIN__ where id=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($userid));
		$udata=$stmt->fetch();
		$f_path=$udata["f_path"];
		$f_patharr=@explode(",",$f_path);
		
		$sql="select id,title from __FUNSET__ where isclose=? and id not in (1,5) order by id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array('1'));
		while($row=$stmt->fetch()){
			if(@in_array($row["id"],$f_patharr)){
				$isallot=1;
			}else{
				$isallot=0;	
			}
			$datas[]=array(
				"id"=>$row["id"],
				"title"=>$row["title"],
				"isallot"=>$isallot
			);
		}
		return $datas;
	}
	
	
}