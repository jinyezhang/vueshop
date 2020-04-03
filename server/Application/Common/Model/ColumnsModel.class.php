<?php
namespace Common\Model;
use Think\Model;
use Think\MyModel;
class ColumnsModel extends Model{
	private $pdo;
	public $rowcount;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function menuList($id,$offset=0,$num=10){
		$sql="select id,c_names,tpl,parent_id,image from __COLUMNS__ where parent_id=? order by num asc,id asc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		
		$sql2="select id,c_names,tpl,parent_id from __COLUMNS__ where parent_id=? order by num asc,id asc";
		$stmt2=$this->pdo->prepare(MyModel::parseSql($sql2));
		while($row=$stmt->fetch()){
			$stmt2->execute(array($row["id"]));
			while($row2=$stmt2->fetch()){
				$sub[]=$row2;
			}
			$row["sub"]=$sub;
			$data[]=$row;
			unset($sub);
		}
		return $data;
	}
	
	public function columnList($id,$offset,$num,$level="1"){
		$sql="select id,c_names,tpl from __COLUMNS__ where parent_id=?";
		$sql.=" order by num asc,id asc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		$this->rowcount=$stmt->rowCount();
		while($row=$stmt->fetch()){
			$data[]=$row;
		}
		return $data;
	}
	
	public function colNotIn($id,$ids,$level="1"){
		$sql="select id,c_names,tpl,webs from __COLUMNS__ where parent_id=? and id not in ({$ids}) order by num asc,id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		$this->rowcount=$stmt->rowCount();
		while($row=$stmt->fetch()){
			$row["url"]=getTplurl($level,$row["tpl"]);
			$data[]=$row;
		}
		return $data;
	}
	
	public function colAllList($id,$level){
		$sql="select id,c_names,tpl from __COLUMNS__ where parent_id=? order by num asc,id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		$this->rowcount=$stmt->rowCount();
		while($row=$stmt->fetch()){
			$row["url"]=getTplurl($level,$row["tpl"]);
			$data[]=$row;
		}
		return $data;
	}
	
	public function colFind($id,$offset,$level="1"){
		$sql="select id,c_names,tpl from __COLUMNS__ where parent_id=?";
		$sql.=" order by num asc,id asc limit {$offset},1";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		$row=$stmt->fetch();
		$row["url"]=getTplurl($level,$row["tpl"]);
		$data=$row;
		return $data;
	}
	
	//一级栏目id
	public function oneColid($id){
		//获取一级id
		$sql="select parentpath from __COLUMNS__ where id=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		$row=$stmt->fetch();
		if($row["parentpath"]=="|0|"){
			$colid=$id;
		}else{
			$expcolid=@explode(",",$row["parentpath"]);
			$colid=str_replace("|","",$expcolid[1]);
		}
		return $colid;
	}
	
	//左侧栏目
	public function leftColumn($id,$level='1'){
		$sql="select id,c_names,tpl from __COLUMNS__ where parent_id=? order by num asc,id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($this->oneColid($id)));
		while($row=$stmt->fetch()){
			$row["url"]=getTplurl($level,$row["tpl"]);
			$data[]=$row;
		}
		return $data;
	}
	
	//获取位置标题
	public function getPosName($id){
		$sql="select parentpath,c_names from __COLUMNS__ where id=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		$row=$stmt->fetch();
		$parpath=str_replace("|","",$row["parentpath"]);
		$expath=@explode(",",$parpath);
		if(count($expath)>0){
			$sql2="select c_names from __COLUMNS__ where id=?";
			$stmt2=$this->pdo->prepare(MyModel::parseSql($sql2));
			foreach($expath as $vid){
				$stmt2->execute(array($vid));
				$row2=$stmt2->fetch();
				$posname.=$row2["c_names"]."&nbsp;&gt;&nbsp;";
			}
			$posname=$posname.$row["c_names"];
		}else{
			$posname=$row["c_names"];
		}
		return $posname;
	}
	
	//获取单片信息数据
	public function getSinArt($field,$id){
		if($id>0){
			$sql="select {$field} from __COLUMNS__ where parent_id=$id order by num asc,id asc";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute(array($id));
			$total=$stmt->rowCount();
			if($total<=0){
				$sql="select {$field} from __COLUMNS__ where id=? order by num asc,id asc";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				$stmt->execute(array($id));
			}
			$data=$stmt->fetch();
			return $data;
		}
	}
	
	//一级栏目
	public function topColumn($offset,$level="1",$ishome=0){
		$sql="select id,c_names,tpl from __COLUMNS__ where parent_id=?";
		if($ishome==1){
			$sql.=" and ishome=1";
		}
		$sql.=" order by num asc,id asc limit ".$offset.",1";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array(0));
		$row=$stmt->fetch();
		$row["url"]=getTplurl($level,$row["tpl"]);
		$data=$row;
		return $data;
	}

}