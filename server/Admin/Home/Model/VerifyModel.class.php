<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class VerifyModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//删除
	public function delVerify(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="delete from __VERIFY__ where id in ({$del});";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//分页
	public function getVerifyTotal($kwords){
		$sql="select v.id from __VERIFY__ v,__USER__ u where v.uid=u.qid and u.username like ?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("%{$kwords}%"));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getVerifyPage($offset,$num,$kwords){
		$sql="select v.id,v.ispass,v.cert,v.company,u.username,v.fail,v.times,v.name,v.cellphone,v.city,v.intent from __VERIFY__ v,__USER__ u where v.uid=u.qid and u.username like ?";
		$sql.=" order by v.times desc,id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("%{$kwords}%"));
		while($row=$stmt->fetch()){
			$data[]=$row;
		}
		return $data;
	}
	
}