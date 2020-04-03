<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class ReviewsModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//删除
	public function delReviews(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="delete from __REVIEWS__ where id in ({$del});";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."/manage';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的数据'); history.go(-1);</script>";	
		}
	}
	
	//评价数据
	public function getReviewsTotal($kwords){
        $sql="select r.id from __REVIEWS__ r inner join __USER__ u on r.myid=u.qid inner join __GOODS__ g on r.gid=g.qid where u.cellphone like ?";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("%{$kwords}%"));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getReviewsPage($offset,$num,$kwords){
		$sql="select r.id,r.myid,g.title,u.cellphone,u.nickname,r.times,r.content,r.audit from __REVIEWS__ r inner join __USER__ u on r.myid=u.qid inner join __GOODS__ g on r.gid=g.qid where u.cellphone like ?";
		$sql.=" order by r.times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("%{$kwords}%"));
		

		while($row=$stmt->fetch()){

			$row["content"]=faceDecode($row["content"]);
			$data[]=$row;
		}
		return $data;
	}
	
}