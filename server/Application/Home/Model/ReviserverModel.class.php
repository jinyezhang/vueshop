<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class ReviserverModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//评价服务后的数据
	public function getService($targetid){
		$sql="select id from __REVIRESULT__ group by myid";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		$mancount=$stmt->rowCount();//评价的人数
		
		$sql="select id,title from __REVISERVER__ order by num asc,id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		
		$rsql="select sum(score) as sumscore from __REVIRESULT__ where targetid=? and rsid=?";
		$rstmt=$this->pdo->prepare(MyModel::parseSql($rsql));
		while($row=$stmt->fetch()){
			$rstmt->execute(array($targetid,$row["id"]));
			$rrow=$rstmt->fetch();
			$score=intval(($rrow["sumscore"]/($mancount*5))*100);
			if($score>0){
				$score=$score."%";
			}else{
				$score=urlencode("没有评价");	
			}
			$data[]=array(
				"title"=>urlencode($row["title"]),
				"score"=>$score
			);
		}
		return $data;
	}
	
}