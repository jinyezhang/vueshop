<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class FavModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//删除
	public function delFav(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="delete from __FAV__ where id in ({$del});";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//收藏数据
	public function getFavTotal(){
        $sql="select f.id,f.uid,g.title,u.nickname,f.times,u.cellphone from __FAV__ f inner join __USER__ u on f.uid=u.qid inner join __GOODS__ g on f.gid=g.qid";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getFavPage($offset,$num){
		$sql="select f.id,f.uid,g.title,u.nickname,f.times,u.cellphone from __FAV__ f inner join __USER__ u on f.uid=u.qid inner join __GOODS__ g on f.gid=g.qid";
        $sql.=" order by f.times desc";
		$sql.=" limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		
		while($row=$stmt->fetch()){
			$data[]=$row;
		}
		return $data;
	}
	
}