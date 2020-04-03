<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class OrderController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(39);
	}
	
	public function index(){
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
		}
		$screen=get_int($_REQUEST["screen"]);
		$this->assign("screen",$screen);
		if($screen>0){
			$strname.="&screen=".$screen;
		}
		$otime=get_str($_GET["otime"]);
		if($otime!=""){
			$strname.="&otime=".$otime;
			$this->assign("otime",$otime);
		}
		$paytype=get_str($_GET["paytype"]);
		if($paytype!=""){
			$strname.="&paytype=".$paytype;
			$this->assign("paytype",$paytype);
		}
		$status=get_str($_GET["status"]);
		if($status!=""){
			$strname.="&status=".$status;
			$this->assign("status",$status);
		}
		$this->assign("strname",$strname);
		$order=D("Order");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$order->getOrderTotal(urldecode($this->kwords),$screen);
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$order->getOrderPage($pageInfo["row_offset"],$pageInfo["row_num"],urldecode($this->kwords),$screen,$otime,$paytype,$status);
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname.""));
		}

		$action=get_str($_GET['action']);
		$ids=get_str(@implode(",",$_POST['del']));
		//操作订单状态
		if($action=='orderstatus'){
		    if($ids){
                $status=get_str($_GET['status']);
                $order->where("ordernum in (".$ids.")")->save(array("status"=>$status));
                header("location: ".$_SERVER['HTTP_REFERER']."");
                exit;
            }else{
		        echo "<script>alert('请选择数据');history.go(-1)</script>";
            }
        }
		
		$this->display();
	}

	public function del(){
		$this->delsql();
		$order=D("Order");
		$order->delOrder();
	}
	
	public function desc(){
		$order=M("Order");
		$user=M("User");
		$orderdesc=M("Orderdesc");
        $adds=M("Address");
		
		$odata=$order->where("ordernum=%d",array($this->id))->find();
		$this->assign("odata",$odata);
		
		//会员信息
		$udata=$user->field("cellphone")->where("qid=%d",array($odata["myid"]))->find();
		$this->assign("udata",$udata);

        //收货人信息
        $addsData=$adds->where("id=%d",array($odata["addsid"]))->find();
        $this->assign("addsData",$addsData);
		
		//订单信息
		$oddata=$orderdesc->where("ordernum=%d",array($this->id))->select();
		$this->assign("oddata",$oddata);
		
		if(count($oddata)>0){
			foreach($oddata as $v){
				$param=json_decode($v["param"],true);
				$gdata[]=array(
					"title"=>$v["title"],
					"price"=>$v["price"],
					"amount"=>$v["amount"],
                    "subtotal"=>$v["subtotal"],
                    "param"=>$param
				);
				unset($title);
				$total+=$v["price"]*$v["amount"];
			}
			$trueTotal=floatval(round($total+$odata["freight"]));
			$this->assign("gdata",$gdata);
			$this->assign("total",$trueTotal);
			
		}
		
		$this->display();
	}
	
}