<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class ArticleModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//删除文章
	public function artDel($id){
		$del=@implode(",",$_POST["del"]);
		if ($del!=""){
		$sql="delete from __ARTICLE__ where id in($del)";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			LogModel::setLog("删除文章","删除");
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."/manage?id={$id}'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";
		}	
	}
	
	//添加图库
	public function addPics($id){
		$title=get_str(trim($_POST["title"]),1);
		$dates=get_str($_POST["dates"]);
		$model=get_str($_POST["model"]);
		$bodys=get_str($_POST["bodys"],1);
		$imgnum=get_int($_POST["imgnum"]);
		if($title!=""){
			//判断照片是否为空
			for($i=1;$i<=$imgnum;$i++){
				if($_POST["photo{$i}"]==""){
					$isnull=true;
				}
			}
			if($isnull){
				echo "<script>alert('请填写必填项！');history.go(-1)</script>";
			}else{
				$sql="insert into __ARTICLE__ (title,parentid,num,dates,bodys,model) values (?,?,?,?,?,?)";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				$stmt->execute(array($title,$id,999,$dates,$bodys,$model));
				$lastid=$this->pdo->lastInsertId();
				
				//添加到图片库
				$sql="insert into __PICS__ (pid,photo) values (?,?)";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				for($i=1;$i<=$imgnum;$i++){
					$stmt->execute(array($lastid,$_POST["photo{$i}"]));
				}
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$id}';</script>";
				exit;
			}
		}
	}
	
	//修改图片库
	public function modPics($id,$cid,$page){
		$title=get_str(trim($_POST["title"]),1);
		$dates=get_str($_POST["dates"]);
		$model=get_str($_POST["model"]);
		$bodys=get_str($_POST["bodys"],1);
		$columns=get_int($_POST["columns"]);
		$imgnum=get_int($_POST["imgnum"]);
		if($title!=""){
			
			//判断照片是否为空
			for($i=1;$i<=$imgnum;$i++){
				if($_POST["photo{$i}"]=="" && $_POST["oldphoto{$i}"]==""){
					$isnull=true;
				}
			}
			if($isnull){
				echo "<script>alert('请填写必填项！');history.go(-1)</script>";
			}else{
				$sql="update __ARTICLE__ set title=?,parentid=?,dates=?,bodys=?,model=? where id=?";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				$stmt->execute(array($title,$columns,$dates,$bodys,$model,$id));
				
				//修改图片
				$sql="update __PICS__ set photo=? where id=?";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				for($i=0;$i<count($_POST["oldid"]);$i++){
					$stmt->execute(array($_POST["oldphoto".($i+1).""],$_POST["oldid"][$i]));
				}
				//添加图片
				$sql="insert into __PICS__ (pid,photo) values (?,?)";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				for($i=1;$i<=$imgnum;$i++){
					if($_POST["photo{$i}"]!=""){
						$stmt->execute(array($id,$_POST["photo{$i}"]));
					}
				}
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$id}&page={$page}&cid={$cid}'</script>";
				exit;
			}
		}
	}
	
}