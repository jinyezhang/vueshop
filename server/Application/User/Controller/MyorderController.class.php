<?php
namespace User\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class MyorderController extends IsTokenController {
	
	//订单
	public function index(){
		$uid=get_str($_GET["uid"]);
		if($uid!=''){
            $status=get_str($_GET["status"]);
			$order=D("Order");
			$pagesize=5;
			$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
			$total=$order->orderTotal($uid,$status);
			$fpage=new PageOrg($total,$current_page,$pagesize);
			$pageInfo=$fpage->getPageInfo();
			$data=$order->orderData($pageInfo["row_offset"],$pageInfo["row_num"],$uid,$status);
			if(count($data)>0){
				MsgLogic::success(200,$data,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"].""));
			}else{
				MsgLogic::error(201);	
			}
		}else{
			MsgLogic::error(201,urlencode("没有获取到会员ID"));	
		}
	}

	
	//会员订单详情
	public function desc(){
		$uid=get_str($_GET["uid"]);
		$ordernum=get_int($_GET["ordernum"]);
		if($uid!='' && $ordernum>0){
			$order=D("Order");
			$data=$order->descData($uid,$ordernum);
			if(count($data)>0){
				MsgLogic::success(200,$data);
			}else{
				MsgLogic::error(201);
			}
		}else{
			MsgLogic::error(201,urlencode("获取失败"));
		}
	}
	
	//取消订单
	public function clearorder(){
		$uid=get_str($_GET["uid"]);
		$ordernum=get_int($_GET["ordernum"]);
		if($uid!='' && $ordernum>0){
			$order=M("Order");
			$order->where("myid='%s' and ordernum=%d",array($uid,$ordernum))->save(array("status"=>"-1"));
			MsgLogic::success(200,urlencode("取消订单成功！"));
		}else{
			MsgLogic::error(201,urlencode("获取失败"));	
		}	
	}

    //确认收货
    public function finalOrder(){
        $uid=get_str($_GET["uid"]);
        $ordernum=get_int($_GET["ordernum"]);
        if($uid!='' && $ordernum>0){
            $order=M("Order");
            $order->where("myid='%s' and ordernum=%d",array($uid,$ordernum))->save(array("status"=>2));
            MsgLogic::success(200,urlencode("确认收货成功！"));
        }else{
            MsgLogic::error(201,urlencode("获取失败"));
        }
    }

    //待评价订单
    public function reviewOrder(){
        $uid=get_str($_GET["uid"]);
        if($uid!=''){
            $order=D("Order");
            $pagesize=5;
            $current_page=isset($_GET["page"])?intval($_GET["page"]):1;
            $total=$order->reviewOrderTotal($uid);
            $fpage=new PageOrg($total,$current_page,$pagesize);
            $pageInfo=$fpage->getPageInfo();
            $data=$order->reviewOrderPage($pageInfo["row_offset"],$pageInfo["row_num"],$uid);
            if(count($data)>0){
                MsgLogic::success(200,$data,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"].""));
            }else{
                MsgLogic::error(201);
            }
        }else{
            MsgLogic::error(201,urlencode("请登录会员"));
        }
    }

}