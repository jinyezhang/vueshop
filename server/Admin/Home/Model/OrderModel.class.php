<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class OrderModel extends Model{
	private $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//删除
	public function delOrder(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$sql="delete from __ORDERDESC__ where ordernum in ({$del});delete from __ORDER__ where ordernum in ({$del});";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的订单'); history.go(-1);</script>";	
		}
	}
	
	//订单数据
	public function getOrderTotal($kwords,$screen){
        $sql="select o.id from __ORDER__ o inner join __USER__ u on o.myid=u.qid left join __ADDRESS__ adds on o.addsid=adds.id where 1=1";
        if($kwords!=""){
            switch($screen){
                case "1";
                    $sql.=" and o.ordernum like '%{$kwords}%'";
                    break;
                case "2";
                    $sql.=" and u.cellphone like '%{$kwords}%'";
                    break;
            };
        }
		$sql.=" group by o.id";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getOrderPage($offset,$num,$kwords,$screen,$otime,$paytype,$status){
		$sql="select o.id,o.times,adds.name,adds.cellphone,o.ordernum,o.iscomm,o.status,o.paytype,u.cellphone as ucellphone from __ORDER__ o inner join __USER__ u on o.myid=u.qid left join __ADDRESS__ adds on o.addsid=adds.id where 1=1";
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and o.ordernum like '%{$kwords}%'";
				break;
				case "2";
					$sql.=" and u.cellphone like '%{$kwords}%'";
				break;
			};
		}
		$sql.=" order by ";
		if($otime!=""){
			$sql.="o.times {$otime},";
		}
		if($paytype!=""){
			$sql.="o.paytype {$paytype},";
		}
		if($status!=""){
			$sql.="o.status {$status},";
		}
		$sql.="o.times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		while($row=$stmt->fetch()){
			$data[]=$row;
		}
		return $data;
	}
	
	public function param(){
		return array(
			"startdate"=>get_str($_REQUEST["startdate"]),
			"enddate"=>get_str($_REQUEST["enddate"]),
			"datetype"=>get_int($_REQUEST["datetype"])
		);	
	}
	
	//财务统计数据
	public function getFinanceTotal($kwords,$screen){
		$param=$this->param();
		$sql="select o.id,u.username from __ORDER__ o inner join __USER__ u on o.targetid=u.qid inner join __ORDERACT__ oa on o.ordernum=oa.ordernum inner join __USER__ my on o.myid=my.qid where 1=1";
		switch($param["datetype"]){
			case "1":
				if($param["startdate"]!="" && $param["enddate"]!=""){
					$sql.=" and o.dates between '".$param['startdate']."' and '".$param['enddate']."'";
				}
			break;
			case "2":
				if($param["startdate"]!="" && $param["enddate"]!=""){
					$sql.=" and FROM_UNIXTIME(o.finaltime) between '".$param['startdate']."' and '".$param['enddate']."'";
				}
			break;
		}
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and u.username like '%{$kwords}%'";
				break;
				case "2";
					$sql.=" and oa.act_name like '%{$kwords}%'";
				break;
				case "3";
					$sql.=" and oa.package_name like '%{$kwords}%'";
				break;
				case "4";
					$sql.=" and my.cellphone like '%{$kwords}%'";
				break;
			};
		}
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		$total=$stmt->rowCount();
		return $total;
	}
	
	public function getFinancePage($offset,$num,$kwords,$screen){
		$param=$this->param();
		$subtotal=0;
		$sql="select u.username,u.name,o.times,oa.act_name,oa.package_name,oa.price,oa.amount,oa.title,o.myid,o.ordernum,o.paytype,o.finaltime,o.isendpay,o.endpaytime,o.id,my.cellphone,o.isrefund from __ORDER__ o inner join __USER__ u on o.targetid=u.qid inner join __ORDERACT__ oa on o.ordernum=oa.ordernum inner join __USER__ my on o.myid=my.qid where 1=1";
		switch($param["datetype"]){
			case "1":
				if($param["startdate"]!="" && $param["enddate"]!=""){
					$sql.=" and o.dates between '".$param['startdate']."' and '".$param['enddate']."'";
				}
			break;
			case "2":
				if($param["startdate"]!="" && $param["enddate"]!=""){
					$sql.=" and FROM_UNIXTIME(o.finaltime) between '".$param['startdate']."' and '".$param['enddate']."'";
				}
			break;
		}
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and u.username like '%{$kwords}%'";
				break;
				case "2";
					$sql.=" and oa.act_name like '%{$kwords}%'";
				break;
				case "3";
					$sql.=" and oa.package_name like '%{$kwords}%'";
				break;
				case "4";
					$sql.=" and my.cellphone like '%{$kwords}%'";
				break;
			};
		}
		$sql.=" order by o.times desc limit {$offset},{$num}";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("%{$kwords}%"));
		
		$usql="select username,name from __USER__ where qid=?";
		$ustmt=$this->pdo->prepare(MyModel::parseSql($usql));
		while($row=$stmt->fetch()){
			$ustmt->execute(array($row["myid"]));
			$urow=$ustmt->fetch();
			
			//小计
			$subtotal=$row["price"]*$row["amount"];
			$row["title"]=actjsontostr($row["title"]);
			$row["uusername"]=$urow["username"];
			$row["uname"]=$urow["name"];
			$row["subtotal"]=$subtotal;
			if($row['finaltime']!=0){
				$row['finaltime']=date("Y-m-d H:i:s",$row['finaltime']);
			}else{
				$row['finaltime']=0;
			}
			if($row['endpaytime']!=0){
				$row['endpaytime']=date("Y-m-d H:i:s",$row['endpaytime']);
			}else{
				$row['endpaytime']=0;
			}
			$data[]=$row;
		}
		return $data;
	}
	
	//总收入
	public function grossIncome($kwords,$screen){
		$param=$this->param();
		$total=0;
		$sql="select u.username,u.name,o.times,oa.act_name,oa.package_name,oa.price,oa.amount,oa.title,o.myid,c.safe_price from __ORDER__ o inner join __USER__ u on o.targetid=u.qid inner join __ORDERACT__ oa on o.ordernum=oa.ordernum left join __CREATEACT__ c on oa.actid=c.id inner join __USER__ my on o.myid=my.qid where o.status=?";
		if($param["startdate"]!="" && $param["enddate"]!=""){
			$sql.=" and o.dates between '".$param['startdate']."' and '".$param['enddate']."'";
		}
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and u.username like '%{$kwords}%'";
				break;
				case "2";
					$sql.=" and oa.act_name like '%{$kwords}%'";
				break;
				case "3";
					$sql.=" and oa.package_name like '%{$kwords}%'";
				break;
				case "4";
					$sql.=" and my.cellphone like '%{$kwords}%'";
				break;
			};
		}
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array(2));
		while($row=$stmt->fetch()){
			//验证过的订单金额
			$total+=(($row["price"]*$row["amount"])-$row["safe_price"]);
		}
		if(floatval($total<=0)){
			$total=0;
		}
		
		//退款订单金额
		$sql="select sum(oa.price*oa.amount) as reftotal from __ORDER__ o inner join __USER__ u on o.targetid=u.qid inner join __ORDERACT__ oa on o.ordernum=oa.ordernum inner join __USER__ my on o.myid=my.qid where o.isrefund='1'";
		if($param["startdate"]!="" && $param["enddate"]!=""){
			$sql.=" and o.dates between '".$param['startdate']."' and '".$param['enddate']."'";
		}
		if($kwords!=""){
			switch($screen){
				case "1";
					$sql.=" and u.username like '%{$kwords}%'";
				break;
				case "2";
					$sql.=" and oa.act_name like '%{$kwords}%'";
				break;
				case "3";
					$sql.=" and oa.package_name like '%{$kwords}%'";
				break;
				case "4";
					$sql.=" and my.cellphone like '%{$kwords}%'";
				break;
			};
		}
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute();
		$row=$stmt->fetch();
		$reftotal=floatval($row["reftotal"]);
		$alltotal=round((floatval($total)-$reftotal)*(1-0.006),2);
		if($alltotal<=0){
			$alltotal=0;
		}
		
		return $alltotal;
	}
	
	public function excelData($startdate,$enddate){
		$subtotal=0;
		$sql="select u.username,u.name,o.times,oa.act_name,oa.package_name,oa.price,oa.amount,oa.title,o.myid,o.ordernum,o.paytype,o.finaltime,o.isendpay,o.endpaytime,o.id,my.cellphone from __ORDER__ o inner join __USER__ u on o.targetid=u.qid inner join __ORDERACT__ oa on o.ordernum=oa.ordernum inner join __USER__ my on o.myid=my.qid where 1=1";
		if($startdate!="" && $enddate!=""){
			$sql.=" and o.dates between '".$startdate."' and '".$enddate."'";
		}
		$sql.=" order by o.times desc";
		$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
		$stmt->execute(array("%{$kwords}%"));
		
		$usql="select username,name from __USER__ where qid=?";
		$ustmt=$this->pdo->prepare(MyModel::parseSql($usql));
		while($row=$stmt->fetch()){
			$ustmt->execute(array($row["myid"]));
			$urow=$ustmt->fetch();
			
			//小计
			$subtotal=$row["price"]*$row["amount"];
			$row["title"]=actjsontostr($row["title"]);
			$row["uusername"]=$urow["username"];
			$row["uname"]=$urow["name"];
			$row["subtotal"]=$subtotal;
			if($row['finaltime']!=0){
				$row['finaltime']=date("Y-m-d H:i:s",$row['finaltime']);
			}else{
				$row['finaltime']=0;
			}
			if($row['endpaytime']!=0){
				$row['endpaytime']=date("Y-m-d H:i:s",$row['endpaytime']);
			}else{
				$row['endpaytime']=0;
			}
			$data[]=$row;
		}
		return $data;
	}
	
}