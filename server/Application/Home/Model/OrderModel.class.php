<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class OrderModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//提交订单
	public function addOrder($uid,$addsid,$goods){
        $ordernum=uniqueId();
        if(count($goods)>0){
            //获取商品信息
            $freights=array();
            $sql="select qid,freight,price,title from __GOODS__ where qid=?";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            for($i=0;$i<count($goods);$i++){
                $stmt->execute(array($goods[$i]['gid']));
                $row=$stmt->fetch();
                //将运费添加到数组
                $freights[]=$row['freight'];
                $goods[$i]['price']=$row['price'];
            }
            //获取数组里最大的运费
            $freight=max($freights);
            $sql="insert into __ORDER__ (ordernum,times,myid,addsid,freight) values (?,?,?,?,?)";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $ores=$stmt->execute(array($ordernum,date("Y-m-d H:i:s"),$uid,$addsid,$freight));

            if(count($goods)>0 && $ores){
                $sql="insert into __ORDERDESC__ (ordernum,myid,gid,title,amount,price,param) values (?,?,?,?,?,?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=0;$i<count($goods);$i++){
                    $param=json_encode($goods[$i]["attrs"],JSON_UNESCAPED_UNICODE);
                    $stmt->execute(array($ordernum,$uid,$goods[$i]["gid"],$goods[$i]["title"],$goods[$i]["amount"],$goods[$i]["price"],$param));
                }
            }
        }
	}

}