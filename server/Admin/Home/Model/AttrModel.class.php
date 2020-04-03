<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class AttrModel extends Model{
	public $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//添加产品参数
	public function addPara($id,$type){
		$attr=get_str(trim($_POST["attr"]));
		$ftype=get_str(trim($_POST["ftype"]));
			//判断必填项
			for($i=0;$i<count($_POST["val"]);$i++){
				if($_POST["val"][$i]==""){
					$isnull=true;
				}
			}
			if($isnull){
				echo "<script>alert('请填写完整信息！');history.go(-1)</script>";
			}else{
				//插入属性
				$lastid=$this->setAttr($id,$attr,$ftype,$type);
				
				//插入值
				$sql="insert into __ATTR__ (name,pid,cid,ftype,num) values (?,?,?,?,?)";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				for($i=0;$i<count($_POST["val"]);$i++){
					$stmt->execute(array("".get_str(trim($_POST["val"][$i]))."",$lastid,$id,$ftype,0));
				}
				echo "<script>alert('添加成功！');location.href='".__ACTION__."?id={$id}&type={$type}'</script>";
			}
	}
	
	//插入属性
	function setAttr($id,$attr,$ftype,$type){
		$sql="insert into __ATTR__ (name,pid,cid,ftype,num,type) values (?,?,?,?,?,?)";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($attr,0,$id,$ftype,0,$type));
		return $this->pdo->lastInsertId();
	}
	
	//删除产品参数
	public function delPara($id,$type){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="delete from __ATTR__ where id in ({$del});delete from __ATTR__ where pid in ({$del});delete from __ATTRVAL__ where attrid in ({$del});delete from __SPECVAL__ where attrid in ({$del})";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			echo "<script>alert('删除成功！');location.href='".__ACTION__."?id={$id}&type={$type}'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";	
		}
	}
	
	//参数排序
	public function orderPara($id,$type){
		for ($i=0;$i<count($_POST["num"]);$i++){	
			$sql="update __ATTR__ set num=".$_POST["num"][$i]." where id=".$_POST["numid"][$i]."";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
		}
		echo "<script>alert ('排序修改成功');location.href='".__ACTION__."?id={$id}&type={$type}'</script>";
	}
	
	//参数分页数据显示
	public function getParaTotal($id,$type){
		$sql="select id from __ATTR__ where cid=? and pid=0 and type=?";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id,$type));
		return $stmt->rowCount();
	}
	
	public function getParaPage($offset,$num,$id,$type){
		$sql="select name,id,num from __ATTR__ where cid=? and pid=0 and type=?";
		$sql.=" order by num desc,id asc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id,$type));
		while($row=$stmt->fetch()){
			$rows[]=$row;
		}	
		return $rows;
	}
	
	//获取参数值
	public function getParaVal($id){
		$sql="select name,ftype,id,pid from __ATTR__ where pid=? order by id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id));
		while($row=$stmt->fetch()){
			if($row["ftype"]=='price'){
				$expname=@explode("-",$row["name"]);
				$name=array($expname[0],$expname[1]);
			}else{
				$name=$row["name"];	
			}
			$rows[]=array(
				"id"=>$row["id"],
				"ftype"=>$row["ftype"],
				"name"=>$name,
				"pid"=>$row["pid"]
			);
		}
		return $rows;
	}
	
	//修改值
	public function modParaVal($id,$mid){
		$ftype=get_str($_POST["ftype"]);
		$attr=get_str(trim($_POST["attr"]));
        $buyselect=get_int($_POST["buyselect"]);
		if($ftype!="" && $attr!=""){
			//修改数据
			$sql="update __ATTR__ set name=?,ftype=?,buyselect=? where id=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute(array(get_str($attr),$ftype,$buyselect,$mid));
			
			$sql="update __ATTR__ set name=?,ftype=? where id=?";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			for($i=0;$i<count($_POST['oldval']);$i++){
				if($_POST["oldval"][$i]!=""){
				$stmt->execute(array(get_str(trim($_POST["oldval"][$i])),$ftype,$_POST["oid"][$i]));
				}
			}
			
			$sql="insert into __ATTR__ (name,ftype,pid,cid,num) values (?,?,?,?,?)";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			for($i=0;$i<count($_POST["val"]);$i++){
				if($_POST["val"][$i]!=""){
				$stmt->execute(array(get_str(trim($_POST["val"][$i])),get_str($ftype),$mid,$id,0));
				}
			}
		
			echo "<script>alert('修改成功！');location.href='".__ACTION__."?cid={$id}&id={$mid}'</script>";
			exit;
		}
	}
	
	//在添加产品是获取的属性
	public function goodsAttr($id,$gid=0){
        //属性
		$sql="select id,name,ftype from __ATTR__  where cid=? and pid=? and type=? order by num desc,id asc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($id,0,0));

        //值
		$sql2="select id,name,ftype from __ATTR__ where pid=? order by id asc";
		$stmt2=$this->pdo->prepare(MyModel::parseSql($sql2));

        //产品值
		$vsql="select id,value from __ATTRVAL__ where gid=? and attrid=? and value=? and isparam=?";
		$vstmt=$this->pdo->prepare(MyModel::parseSql($vsql));
		while($row=$stmt->fetch()){

			$stmt2->execute(array($row["id"]));
			while($row2=$stmt2->fetch()){
				$vstmt->execute(array($gid,$row["id"],$row2["id"],1));
				$vrow=$vstmt->fetch();
				$row2["value"]=$vrow["value"];
				$row2["vid"]=$vrow["id"];
				$sub[]=$row2;
			}
			$row["sub"]=$sub;
			$data[]=$row;
			unset($sub);
		}
		return $data;
	}

    //产品规格
    public function getSpec($cid,$gid){
        $sql="select id,name from __ATTR__ where cid=? and type=? order by num desc,id asc";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($cid,1));

        //值
        $vsql="select id,value from __SPECVAL__ where gid=? and attrid=? and isparam=? order by id asc";
        $vstmt=$this->pdo->prepare(MyModel::parseSql($vsql));
        while($row=$stmt->fetch()){
            $vstmt->execute(array($gid,$row["id"],1));
            while($vrow=$vstmt->fetch()){
                $sub[]=$vrow;
            }
            $row["sub"]=$sub;
            $data[]=$row;
            unset($sub);
        }
        return $data;
    }
	
}