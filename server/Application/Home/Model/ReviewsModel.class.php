<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class ReviewsModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function getViewsTotal($gid,$isreviews){
        $sql="select u.nickname,u.head,r.content,r.times from __USER__ u inner join __REVIEWS__ r on u.qid=r.myid where r.gid=?";
        if($isreviews=='1'){
            $sql.=" and r.audit='1'";
        }
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($gid));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getViewsPage($offset,$num,$gid,$isreviews){
		$sql="select u.nickname,u.head,r.content,r.times from __USER__ u inner join __REVIEWS__ r on u.qid=r.myid where r.gid=?";
		if($isreviews=='1'){
			$sql.=" and r.audit='1'";
		}
		$sql.=" order by r.times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($gid));
		while($row=$stmt->fetch()){
			if($row["head"]!=""){
				$head=getHost()."/userfiles/head/".$row["head"];
			}else{
				$head="";	
			}
			$data[]=array(
				"nickname"=>urlencode($row["nickname"]),
				"head"=>$head,
				"content"=>urlencode(faceDecode($row["content"])),
				"times"=>$row["times"]
			);
		}
		return $data;
	}
	
	//提交数据
	public function addReviews($uid,$gid,$content,$rsdata,$ordernum){
				if(count($rsdata)>0){
					$sql="insert into __REVIEWS__ (myid,gid,content,times) values (?,?,?,?)";
					$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
					$stmt->execute(array($uid,$gid,$content,date("Y-m-d H:i:s")));
					
					//添加数据
					$addsql="insert into __REVIRESULT__ (myid,gid,rsid,score) values (?,?,?,?)";
					$addstmt=$this->pdo->prepare(MyModel::parseSql($addsql));

					foreach($rsdata as $v){
                        $addstmt->execute(array($v["myid"],$v["gid"],$v["rsid"],$v["score"]));
					}
					
					$osql="update __ORDERDESC__ set isreview=? where myid=? and ordernum=?";
					$ostmt=$this->pdo->prepare(MyModel::parseSql($osql));
					$ostmt->execute(array('1',$uid,$ordernum));
					
					return "谢谢您的评价，我们会尽快审核！";
				}
	}
	
}