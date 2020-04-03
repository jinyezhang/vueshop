<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class OrderController extends IsTokenController {
	
	public function add(){
        $uid=get_str($_POST["uid"]);
        $addsid=get_int($_POST["addsid"]);
        $goodsData=json_decode(filterCode($_POST["goodsData"]),true);
        if($uid!='') {
            if ($addsid>0 && is_array($goodsData)) {
                $order = D("Order");
                $order->addOrder($uid,$addsid,$goodsData);
                MsgLogic::success(200,urlencode("提交成功！"));
            } else {
                MsgLogic::error(302, urlencode("请输入正确的数据"));
            }
        }else{
            MsgLogic::error(302,urlencode("请登录会员"));
        }
		
	}

	//获取最后的订单
    public function lastOrdernum(){
        $uid=get_str($_GET["uid"]);
        if($uid!=''){
            $order=M("Order");
            $row=$order->field("ordernum")->where("myid='%s'",array($uid))->order("id desc")->find();
            if(count($row)>0){
                $data=array(
                    "ordernum"=>$row["ordernum"]
                );
                MsgLogic::success(200,$data);
            }else{
                MsgLogic::success(201);
            }
        }else{
            MsgLogic::error(302,urlencode("请登录会员"));
        }
    }

}