<?php
namespace User\Model;
use Think\Model;
use Think\MyModel;
class OrderModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//订单
	public function orderTotal($uid,$status){
        $sql="select ordernum from __ORDER__ where myid=?";
        if($status!="all"){
            $sql.=" and status='{$status}'";
        }else{
            $sql.=" and status<>'-1'";
        }
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($uid));
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function orderData($offset,$num,$uid,$status){
		$sql="select ordernum,status,freight from __ORDER__ where myid=?";
        if($status!="all"){
            $sql.=" and status='{$status}'";
        }else{
            $sql.=" and status<>'-1'";
        }
		$sql.=" order by times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array($uid));

        $odsql="select g.qid,o.title,o.amount,o.price,o.subtotal,o.param from __ORDERDESC__ o left join __GOODS__ g on o.gid=g.qid where o.ordernum=? and o.myid=? order by o.id asc";
        $odstmt=$this->pdo->prepare(MyModel::parseSql($odsql));

        $isql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $istmt=$this->pdo->prepare(MyModel::parseSql($isql));
		while($row=$stmt->fetch()){
            $odstmt->execute(array($row["ordernum"],$uid));
            $total=0;
            while($odrow=$odstmt->fetch()){
                $istmt->execute(array($odrow["qid"]));
                $irow=$istmt->fetch();
                if($irow["photo"]!=''){
                    $image=getHost()."/uploadfiles/".$irow["photo"];
                }else{
                    $image='';
                }
                if($odrow["param"]!=""){
                    $param=json_decode($odrow["param"],true);
                }else{
                    $param='';
                }
                $odata[]=array(
                    "gid"=>$odrow["qid"],
                    "title"=>$odrow["title"],
                    "amount"=>$odrow["amount"],
                    "price"=>$odrow["price"],
                    "image"=>$image,
                    "subtotal"=>$odrow["subtotal"],
                    "param"=>$param
                );
                $total+=$odrow["price"]*$odrow["amount"];
            }
            $data[]=array(
                "ordernum"=>$row["ordernum"],
                "status"=>$row["status"],
                "total"=>floatval(round($total+$row["freight"])),
                "goods"=>$odata
            );
            unset($odata);
        }
		return $data;
	}

    public function descData($uid,$ordernum){
        $freight=0;
        $total=0;
        $truetotal=0;
        $sql="select o.ordernum,adds.name,adds.cellphone,o.status,adds.address,adds.province,adds.city,adds.area,o.times,o.freight from __ORDER__ o,__ADDRESS__ adds where o.addsid=adds.id and o.myid=? and o.ordernum=?";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($uid,$ordernum));
        $row=$stmt->fetch();

        $gsql="select gid,title,price,amount,param from __ORDERDESC__ where ordernum=? and myid=?";
        $gstmt=$this->pdo->prepare(MyModel::parseSql($gsql));
        $gstmt->execute(array($ordernum,$uid));

        $imgsql="select photo from __GOODSIMGS__ where gid=?";
        $imgstmt=$this->pdo->prepare(MyModel::parseSql($imgsql));
        while($grow=$gstmt->fetch()){
            $imgstmt->execute(array($grow["gid"]));
            $irow=$imgstmt->fetch();
            if($irow["photo"]!=''){
                $image=getHost()."/uploadfiles/".$irow["photo"];
            }else{
                $image="";
            }
            $total+=$grow["amount"]*$grow["price"];
            if($grow["param"]!=""){
                $param=json_decode($grow["param"],true);
            }else{
                $param='';
            }
            $goods[]=array(
                "gid"=>$grow["gid"],
                "title"=>$grow["title"],
                "price"=>floatval($grow["price"]),
                "amount"=>$grow["amount"],
                "param"=>$param,
                "image"=>$image

            );
        }
        $truetotal=$total+$row["freight"];
        $data=array(
            "ordernum"=>$row["ordernum"],
            "name"=>urlencode($row["name"]),
            "cellphone"=>$row["cellphone"],
            "status"=>$row["status"],
            "province"=>urlencode($row["province"]),
            "city"=>urlencode($row["city"]),
            "area"=>urlencode($row["area"]),
            "address"=>urlencode($row["address"]),
            "freight"=>floatval($row["freight"]),
            "total"=>floatval($total),
            "truetotal"=>floatval($truetotal),
            "ordertime"=>$row["times"],
            "goods"=>$goods
        );
        return $data;
    }

    //待评价订单
    public function reviewOrderTotal($uid){
        $sql="select ordernum from __ORDER__ where myid=? and status=?";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($uid,2));
        $total=$stmt->rowCount();
        return $total;
    }

    public function reviewOrderPage($offset,$num,$uid){
        $sql="select ordernum,status from __ORDER__ where myid=? and status=?";
        $sql.=" order by times desc limit {$offset},{$num}";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($uid,2));

        $odsql="select g.qid,o.title,o.amount,o.price,o.freight,o.isreview from __ORDERDESC__ o left join __GOODS__ g on o.gid=g.qid where o.ordernum=? and o.myid=? order by o.id asc";
        $odstmt=$this->pdo->prepare(MyModel::parseSql($odsql));

        $isql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $istmt=$this->pdo->prepare(MyModel::parseSql($isql));
        while($row=$stmt->fetch()) {
            $odstmt->execute(array($row["ordernum"], $uid));
            $total = 0;
            while ($odrow = $odstmt->fetch()) {
                $istmt->execute(array($odrow["qid"]));
                $irow = $istmt->fetch();
                if ($irow["photo"] != '') {
                    $image = getHost() . "/uploadfiles/" . $irow["photo"];
                } else {
                    $image = '';
                }
                $odata[] = array(
                    "gid" => $odrow["qid"],
                    "title" => urlencode($odrow["title"]),
                    "amount" => $odrow["amount"],
                    "price" => $odrow["price"],
                    "freight" => $odrow["freight"],
                    "image" => $image,
                    "isreview" => $odrow["isreview"]
                );
                $total += $odrow["price"] * $odrow["amount"] + $odrow["freight"];
            }
            $data[] = array(
                "ordernum" => $row["ordernum"],
                "status" => $row["status"],
                "total" => $total,
                "goods" => $odata
            );
            unset($odata);
        }
        return $data;

    }

}