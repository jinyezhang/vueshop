<?php
namespace Common\Model;
use Think\Model;
use Think\MyModel;
class ArticleModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function getArticle($id,$offset,$num,$ishome=""){
		$sql="select a.title,a.parentid,a.dates,a.title,a.photo,a.id,c.tpl from __COLUMNS__ c,__ARTICLE__ a where c.id=a.parentid and (a.parentid=? or c.parentpath like ?) order by ";
		if($ishome==1){
			$sql.="a.ishome desc,";
		}
		$sql.="a.num asc,a.dates desc,a.id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id,"%|{$id}|%"));
		while($row=$stmt->fetch()){
			$data[]=$row;
		}
		return $data;
	}
	
	public function artFind($id,$offset,$ishome=""){
		$sql="select a.id,a.title,a.parentid,a.dates,a.bodys,c.tpl from __COLUMNS__ c,__ARTICLE__ a where c.id=a.parentid and (a.parentid=? or c.parentpath like ?) order by ";
		if($ishome==1){
			$sql.="a.ishome desc,";
		}
		$sql.="a.num asc,a.dates desc,a.id desc limit {$offset},1";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id,"%|{$id}|%"));
		$data=$stmt->fetch(\PDO::FETCH_ASSOC);
		$data["title"]=urlencode($data["title"]);
		$data["bodys"]=urlencode($data["bodys"]);
		$data["url"]=getArturl($data["tpl"]);
		return $data;
	}
	
	//相关文章
	public function relatedArt($aid,$id,$fields,$kwords,$offset,$num){
		if($kwords!=""){
			$expk=@explode(" ",$kwords);
			$counts=count($expk);
			$sql="select {$fields} from __ARTICLE__ where id<>? and parentid=?";
			$sql.=" and (relwords like '%".$expk[0]."%'";
			for($i=1;$i<$counts;$i++){
				$sql.=" or relwords like '%".$expk[$i]."%'";
			}
			$sql.=") order by num asc,dates desc,id desc limit {$offset},{$num}";
		}
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute(array($aid,$id));
		$data=$query->fetchAll();
		return $data;
	}
	
	public function getRowTotal($id){
		if($id>0){
			$sql="select a.id from __ARTICLE__ a,__COLUMNS__ c where a.parentid=c.id and (a.parentid=? or c.parentpath like ?)";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute(array($id,"%|{$id}|%"));
			$result=$stmt->rowcount();
			return $result;
		}
	}
	
	public function getPageRows($offset,$num,$id){
		if($id>0){
			$sql="select a.id,a.parentid,a.title,a.dates,a.image,a.viewnum,a.source from __ARTICLE__ a,__COLUMNS__ c where a.parentid=c.id and (a.parentid=? or c.parentpath like ?)";
			$sql.=" order by a.num asc,a.dates desc,a.id desc limit {$offset},{$num}";
			$result=$this->pdo->prepare(MyModel::parseSql($sql));
			$result->execute(array($id,"%|{$id}|%"));
			if($result)
			{
				while($row=$result->fetch()){
					$allcolumns[]=array("aid"=>$row["id"],"cid"=>$row["parentid"],"title"=>urlencode($row["title"]),"date"=>$row["dates"],"image"=>urlencode(getHost()."/uploadfiles/".$row["image"]),"viewnum"=>$row["viewnum"],"source"=>urlencode($row["source"]));
				}
				return $allcolumns;
			}else{
				return false;
			}
		}
	}
	
	public function allArticle($id,$ishome=""){
		$sql="select a.title,a.parentid,a.dates,a.title,a.photo,a.id,a.bodys,c.tpl from __COLUMNS__ c,__ARTICLE__ a where c.id=a.parentid and (a.parentid=? or c.parentpath like ?) order by ";
		if($ishome==1){
			$sql.="a.ishome desc,";
		}
		$sql.="a.num asc,a.dates desc,a.id desc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id,"%|{$id}|%"));
		while($row=$stmt->fetch(\PDO::FETCH_ASSOC)){
			$data[]=array(
				"title"=>urlencode($row["title"]),
				"content"=>urlencode(strsub(delspace(strip_tags($row["bodys"])),60)),
				"aid"=>$row["id"],
				"cid"=>$row["parentid"]
			);
		}
		return $data;
	}
	
	public function getSlide($offset,$num){
		$sql="select a.parentid,a.title,a.image,a.id from __COLUMNS__ c,__ARTICLE__ a where c.id=a.parentid and a.isslide=?";
		$sql.=" order by a.num asc,a.dates desc,a.id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array(1));
		while($row=$stmt->fetch(\PDO::FETCH_ASSOC)){
			$data[]=array(
				"title"=>urlencode($row["title"]),
				"image"=>urlencode(C("DHOST")."/uploadfiles/".$row["image"]),
				"cid"=>$row["parentid"],
				"aid"=>$row["id"]
			);
		}
		return $data;
	}
	
	public function getSearchTotal($kwords){
			$sql="select a.id from __ARTICLE__ a,__COLUMNS__ c where a.parentid=c.id and a.title like ?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute(array("%{$kwords}%"));
			$result=$stmt->rowcount();
			return $result;
	}
	
	public function getSearchRows($offset,$num,$kwords){
			$sql="select a.id,a.parentid,a.title,a.dates,a.photo,a.content from __ARTICLE__ a,__COLUMNS__ c where a.parentid=c.id and a.title like ?";
			$sql.=" order by a.num asc,a.dates desc,a.id desc limit {$offset},{$num}";
			$result=$this->pdo->prepare(MyModel::parseSql($sql));
			$result->execute(array("%{$kwords}%"));
			if($result)
			{
				while($row=$result->fetch()){
					$allcolumns[]=array("aid"=>$row["id"],"cid"=>$row["parentid"],"title"=>urlencode($row["title"]),"date"=>$row["dates"],"image"=>C("DHOST")."/uploadfiles/".$row["photo"],"content"=>urlencode($row["content"]));
				}
				return $allcolumns;
			}else{
				return false;
			}
	}
	
	//推荐
	public function getRecom(){
		$sql="select a.dates from __ARTICLE__ a,__COLUMNS__ c where a.parentid=c.id and c.tpl=? group by a.dates order by a.dates desc,a.id desc limit 0,5";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("News"));
		
		$sql2="select a.id,a.parentid,a.content,a.times from __ARTICLE__ a,__COLUMNS__ c where a.parentid=c.id and c.tpl=? and a.dates=? order by a.dates desc,a.id desc";
		$stmt2=$this->pdo->prepare(MyModel::parseSql($sql2));
		while($row=$stmt->fetch()){
			$stmt2->execute(array("News",$row["dates"]));
			while($row2=$stmt2->fetch()){
				$sub[]=array("aid"=>$row2["id"],"cid"=>$row2["parentid"],"times"=>$row2["times"],"content"=>urlencode($row2["content"]));
			}
			$row["sub"]=$sub;
			$data[]=$row;
			unset($sub);
		}
		return $data;
		
	}
	
}