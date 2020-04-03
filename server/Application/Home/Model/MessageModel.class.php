<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class MessageModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function getMessageTotal($actid){
		$sql="select m.id from __MESSAGE__ m inner join __USER__ u on m.uid=u.qid where m.actid=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($actid));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getMessagePage($offset,$num,$actid){
		$sql="select u.head,u.nickname,m.content,m.times,m.id from __MESSAGE__ m inner join __USER__ u on m.uid=u.qid where m.actid=?";
		$sql.=" order by m.times desc,m.id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($actid));
		
		//回复的数据
		$rsql="select r.times,r.content,a.adminname from __MSGREPLY__ r inner join __ADMIN__ a on r.adminid=a.qid where r.msgid=? order by r.times desc,r.id desc";
		$rstmt=$this->pdo->prepare(MyModel::parseSql($rsql));
		while($row=$stmt->fetch()){
			//回复的数据
			$rstmt->execute(array($row["id"]));
			while($rrow=$rstmt->fetch()){
				$reply[]=array(
					"times"=>$rrow["times"],
					"content"=>urlencode($rrow["content"]),
					"adminname"=>urlencode($rrow["adminname"])
				);
			}
			
			
			if($row["head"]!=""){
				$head=getHost()."/userfiles/head/".$row["head"];
			}else{
				$head="";	
			}
			
			$data[]=array(
				"nickname"=>urlencode($row["nickname"]),
				"head"=>$head,
				"content"=>urlencode(faceDecode($row["content"])),
				"times"=>$row["times"],
				"reply"=>$reply
			);
			unset($reply);
		}
		return $data;
	}
	
	
}