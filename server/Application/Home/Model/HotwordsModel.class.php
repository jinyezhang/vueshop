<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class HotwordsModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//增加热门搜索关键词
	public function setHotWords($kwords){
		$sql="select id from __HOTWORDS__ where title=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($kwords));
		$total=$stmt->rowCount();
		if($total>0){
			$sql="update __HOTWORDS__ set scount=scount+1 where title=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute(array($kwords));
		}else{
			$sql="insert into __HOTWORDS__ (title) values (?)";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute(array($kwords));	
		}
	}
	
}