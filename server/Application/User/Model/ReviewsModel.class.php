<?php
namespace User\Model;
use Think\Model;
use Think\MyModel;
class ReviewsModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function getReviewsTotal($uid){
		$sql="select r.id from __REVIEWS__ r inner join __CREATEACT__ c on r.actid=c.id where r.myid=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($uid));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getReviewsPage($offset,$num,$uid){
		$sql="select r.id,c.title,r.actid,r.targetid from __REVIEWS__ r inner join __CREATEACT__ c on r.actid=c.id where r.myid=?";
		$sql.=" order by r.times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($uid));
		
		//图片
		$isql="select image from __ACTIMGS__ where actid=?";
		$istmt=$this->pdo->prepare(MyModel::parseSql($isql));
		
		//活动日期
		$dsql="select dates,enddate from __PACKAGEDATE__ where pid in (select id from __PACKAGE__ where actid=? order by id desc) order by dates desc";
		$dstmt=$this->pdo->prepare(MyModel::parseSql($dsql));
		
		//报名人数
		$msql="select title,amount from __ORDERACT__ where actid=?";
		$mstmt=$this->pdo->prepare(MyModel::parseSql($msql));
		while($row=$stmt->fetch()){
			//图片
			$istmt->execute(array($row["actid"]));
			$irow=$istmt->fetch();
			if($irow["image"]!=""){
				$image=urlencode(getHost()."/userfiles/images/".$irow["image"]);
			}else{
				$image="";	
			}
			
			
			//活动日期
			$dstmt->execute(array($row["actid"]));
			$drow=$dstmt->fetch();
			
			//报名人数
			$mstmt->execute(array($row["actid"]));
			$mrow=$mstmt->fetch();
			$tarr=json_decode($mrow["title"],true);
			if(count($tarr)>0){
				foreach($tarr as $v){
					$mannum+=$v["count"];
				}
			}
			$amount+=$mrow["amount"];
			$signnum=$mannum*$amount;
			$data[]=array(
				"id"=>$row["id"],
				"title"=>urlencode($row["title"]),
				"actid"=>$row["actid"],
				"image"=>$image,
				"dates"=>urlencode(date("m/d",strtotime($drow["dates"]))."-".date("m/d",strtotime($drow["enddate"]))),
				"signnum"=>$signnum,
				"targetid"=>$row["targetid"]
			);
		}
		return $data;
	}
	
}