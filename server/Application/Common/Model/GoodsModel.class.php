<?php
namespace Common\Model;
use Think\Model;
use Think\MyModel;
class GoodsModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	public function getGoods($offset,$num){
	    $i=1;
        $colsql="select id,c_names from __COLUMNS__ where parent_id=? order by num asc,id asc limit 0,3";
        $colstmt=$this->pdo->prepare(MyModel::parseSql($colsql));
        $colstmt->execute(array(0));

		$sql="select g.title,g.parentid,g.id,c.tpl,g.price,g.qid from __COLUMNS__ c,__GOODS__ g where c.id=g.parentid and (g.parentid=? or c.parentpath like ?) order by ";
		$sql.="g.num desc,g.id desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));

        $imgsql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $imgstmt=$this->pdo->prepare(MyModel::parseSql($imgsql));
        while($colrow=$colstmt->fetch()){
            $stmt->execute(array($colrow["id"],"%|".$colrow["id"]."|%"));
            while($row=$stmt->fetch()){
                $imgstmt->execute(array($row["qid"]));
                $irow=$imgstmt->fetch();
                $row["image"]=$irow["photo"];
                if($irow["photo"]!=""){
                    $image=getHost()."/uploadfiles/".$irow["photo"];
                }else{
                    $image='';
                }
                $sub[]=array(
                    "title"=>urlencode($row["title"]),
                    "gid"=>$row["qid"],
                    "cid"=>$row["parentid"],
                    "price"=>floatval($row["price"]),
                    "image"=>$image
                );
            }
            if($i%2==0){
                array_pop($sub);
            }
            $data[]=array(
                "title"=>urlencode($colrow["c_names"]),
                "items"=>$sub
            );
            unset($sub);

            $i++;
        }

		return $data;
	}

    public function recomData(){
        $sql="select qid,title,price,parentid from __GOODS__ order by num desc,id asc limit 0,12";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute();

        $isql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $istmt=$this->pdo->prepare(MyModel::parseSql($isql));
        while($row=$stmt->fetch()){
            $istmt->execute(array($row['qid']));
            $irow=$istmt->fetch();
            if($irow["photo"]!=''){
                $image=getHost()."/uploadfiles/".$irow["photo"];
            }else{
                $image='';
            }
            $data[]=array(
                "gid"=>$row["qid"],
                "cid"=>$row["parentid"],
                "title"=>urlencode($row["title"]),
                "price"=>$row["price"],
                "image"=>$image
            );
        }
        return $data;
    }

    //产品展示
    public function showData($cid){
        if($cid>0){
            $cid=$cid;
        }else{
            $sql="select id from __COLUMNS__ where parent_id=? order by num asc,id asc limit 0,1";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $stmt->execute(array(0));
            $row=$stmt->fetch();
            $cid=$row["id"];
        }
        $sql="select id,c_names from __COLUMNS__ where parent_id=? order by num asc,id asc";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($cid));

        $gsql="select qid,title,parentid from __GOODS__ where parentid=? order by num desc,id desc limit 0,9";
        $gstmt=$this->pdo->prepare(MyModel::parseSql($gsql));

        $isql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $istmt=$this->pdo->prepare(MyModel::parseSql($isql));

        while($row=$stmt->fetch()){
            $gstmt->execute(array($row["id"]));
            while($grow=$gstmt->fetch()){
                $istmt->execute(array($grow["qid"]));
                $irow=$istmt->fetch();
                if($irow["photo"]!=''){
                    $image=getHost()."/uploadfiles/".$irow["photo"];
                }else{
                    $image='';
                }
                $sub[]=array(
                    "gid"=>$grow["qid"],
                    "title"=>urlencode($grow["title"]),
                    "image"=>$image
                );
            }
            $data[]=array(
                "cid"=>$row["id"],
                "title"=>urlencode($row["c_names"]),
                "goods"=>$sub
            );
            unset($sub);
        }

        return $data;

    }

    //搜索后的结果
    public function getGoodsTotal($kwords,$attrdata,$price1,$price2,$cid){
        $sql="select g.qid from __GOODS__ g inner join __COLUMNS__ c on g.parentid=c.id where c.tpl='goods' and g.title like '%{$kwords}%'";
        if(is_array($attrdata) && count($attrdata)>0){
            $sql.=" and (";
            foreach($attrdata as $v){
                $sql.="g.goodspara like '%|{$v}|%' and ";
            }
            $rsql=rtrim($sql," and");
            $sql=$rsql.")";
        }
        if($price1!='' && $price2!=''){
            $sql.=" and price>={$price1} and price<={$price2}";
        }
        if($cid>0){
            $sql.=" and (c.parentpath like '%|{$cid}|%' or g.parentid='{$cid}')";
        }
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute();
        $total=$stmt->rowCount();
        return $total;
    }

    public function getGoodsPage($offset,$num,$kwords,$attrdata,$price1,$price2,$otype,$cid){
        $sql="select g.qid,g.parentid,g.title,g.price,g.sales from __GOODS__ g inner join __COLUMNS__ c on g.parentid=c.id where c.tpl='goods' and g.title like '%{$kwords}%'";
        if(is_array($attrdata) && count($attrdata)>0){
            $sql.=" and (";
            foreach($attrdata as $v){
                $sql.="g.goodspara like '%|{$v}|%' and ";
            }
            $rsql=rtrim($sql," and");
            $sql=$rsql.")";
        }
        if($price1!='' && $price2!=''){
            $sql.=" and price>={$price1} and price<={$price2}";
        }
        if($cid>0){
            $sql.=" and (c.parentpath like '%|{$cid}|%' or g.parentid='{$cid}')";
        }
        if($otype=='all' || $otype=='') {
            $sql.= " order by g.num desc,g.id desc";
        }else if($otype=='up'){
            $sql.=" order by g.price asc";
        }else if($otype=='down'){
            $sql.=" order by g.price desc";
        }else if($otype=='sales'){
            $sql.=" order by g.sales desc";
        }
        $sql.=" limit {$offset},{$num}";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute();

        $isql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $istmt=$this->pdo->prepare(MyModel::parseSql($isql));
        while($row=$stmt->fetch()){
            $istmt->execute(array($row["qid"]));
            $irow=$istmt->fetch();
            if($irow["photo"]!=''){
                $image=getHost()."/uploadfiles/".$irow["photo"];
            }else{
                $image='';
            }
            $data[]=array(
                "gid"=>$row["qid"],
                "cid"=>$row["parentid"],
                "title"=>urlencode($row["title"]),
                "price"=>$row["price"],
                "sales"=>$row["sales"],
                "image"=>$image
            );
        }
        return $data;
    }
	
}