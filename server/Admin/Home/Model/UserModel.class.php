<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class UserModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//删除会员
	public function delUser(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="delete from __REVIEWS__ where myid in ({$del});delete from __USER__ where qid in ({$del});delete from __FAV__ where uid in ({$del});delete from __REVIRESULT__ where myid in ({$del});delete from __ADDRESS__ where uid in ({$del})";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			
			//操作日志
			LogModel::setLog("删除会员","删除");
			
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."/manage';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的会员'); history.go(-1);</script>";	
		}
	}
	
	//会员分页
	public function getUserTotal($kwords,$screen,$utype){
		$sql="select qid from __USER__ where utype='{$utype}'";
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and qid like '%{$kwords}%'";
				break;
				case "3";
					$sql.=" and cellphone like '%{$kwords}%'";
				break;
				case "5";
					$sql.=" and nickname like '%{$kwords}%'";
				break;
			};
		}
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getUserPage($offset,$num,$kwords,$screen,$utype,$timeorder){
		$sql="select qid,times,cellphone,utype,nickname from __USER__ where utype='{$utype}'";
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and qid like '%{$kwords}%'";
				break;
				case "3";
					$sql.=" and cellphone like '%{$kwords}%'";
				break;
				case "5";
					$sql.=" and nickname like '%{$kwords}%'";
				break;
			};
		}
		if($timeorder==""){
			$timeorder="desc";
		}
		$sql.=" order by times {$timeorder},id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		
		while($row=$stmt->fetch()){
			$data[]=$row;
		}
		return $data;
	}
	
}