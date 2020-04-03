<?php
namespace User\Model;
use Think\Model;
use Think\MyModel;
class FavModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function getFavTotal($uid){
        $sql="select f.id from __FAV__ f inner join __GOODS__ g on f.gid=g.qid where f.uid=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($uid));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getFavPage($offset,$num,$uid){
		$sql="select f.id,g.title,f.gid,g.price,g.parentid from __FAV__ f inner join __GOODS__ g on f.gid=g.qid where f.uid=?";
		$sql.=" order by f.times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($uid));

		//图片
		$isql="select photo from __GOODSIMGS__ where gid=?";
		$istmt=$this->pdo->prepare(MyModel::parseSql($isql));

		while($row=$stmt->fetch()){
			//图片
			$istmt->execute(array($row["gid"]));
			$irow=$istmt->fetch();
			if($irow["photo"]!=""){
				$image=urlencode(getHost()."/uploadfiles/".$irow["photo"]);
			}else{
				$image="";	
			}
			$data[]=array(
                "fid"=>$row["id"],
				"title"=>urlencode($row["title"]),
				"gid"=>$row["gid"],
                "cid"=>$row["parentid"],
				"image"=>$image,
				"price"=>floatval($row["price"])
			);
		}
		return $data;
	}
	
}