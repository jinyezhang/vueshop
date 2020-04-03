<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class UserModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//商家展示信息
	public function sellerInfo($targetid){
		$sql="select remarks,nickname,head from __USER__ where qid=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($targetid));
		$urow=$stmt->fetch();
		if($urow["head"]!=""){
			$head=getHost()."/userfiles/head/".$urow["head"];
		}else{
			$head="";	
		}
		
		//活动数量
		$sql="select id from __CREATEACT__ where uid=? and audit=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($targetid,'1'));
		$actnum=$stmt->rowCount();
		
		//活动评价数量
		$sql="select id from __REVIEWS__ where targetid=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($targetid));
		$reviewsnum=$stmt->rowCount();
		
		//报名人数
		$sql="select oa.title,oa.amount from __ORDER__ o inner join __ORDERACT__ oa on o.ordernum=oa.ordernum where o.status=? and o.targetid=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array(1,$targetid));
		while($row=$stmt->fetch()){
			$mancount=json_decode($row["title"],true);
			if(is_array($mancount) && count($mancount)>0){
				foreach($mancount as $v){
					$mannum+=$v["count"];
				}
			}
			$amount+=$row["amount"];
		}
		$enrollnum=$mannum*$amount;
		
		$data=array(
			"nickname"=>urlencode($urow["nickname"]),
			"remarks"=>urlencode($urow["remarks"]),
			"head"=>$head,
			"actnum"=>$actnum,
			"reviewnum"=>$reviewsnum,
			"enrollnum"=>$enrollnum
		);
		return $data;
	}
}