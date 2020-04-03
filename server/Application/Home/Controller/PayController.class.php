<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class PayController extends IsTokenController {
	
	public function __construct(){
		parent::__construct();
		import("Common/Org/Unionpay");
	}
	
	//银联
	public function unionpay(){
		$upay=new \unionpay\Unionpay;
		$ordernum=get_int($_POST["ordernum"]);
		$price=floatval($_POST["price"]);
		$result=$upay->submit($ordernum,$price*100);
		$data=array(
			"code"=>$result["respCode"],
			"ordernum"=>$result["orderId"],
			"tn"=>$result["tn"]
		);
		MsgLogic::success(200,$data);
	}
	
	//银联回调
	public function unionpaycallback(){
			if (isset ( $_POST ['signature'] )) {
				//echo \com\unionpay\acp\sdk\AcpService::validate ( $_POST ) ? '验签成功' : '验签失败';
				$ordernum = $_POST ['orderId']; //其他字段也可用类似方式获取
				$respCode = $_POST ['respCode']; //判断respCode=00或A6即可认为交易成功
				$queryId=$_POST["queryId"];//交易流水号
				$price=$_POST["txnAmt"];//支付价格
				$data=array(
					"code"=>$respCode,
					"ordernum"=>$ordernum,
					"tn"=>$queryId,
					"price"=>$price
				);
				//$ordernum=927761314;
				$ocode=rand(100000,999999);
				$order=D("Order");
				$order->paySuccess($ordernum,$price,3,$ocode);
				MsgLogic::success(200,$data);
			} else {
				MsgLogic::error(302,urlencode("签名为空"));	
			}
	}
	
	//支付宝回调地址
	public function alipaycallback(){
		require(dirname(__FILE__)."/../../../SDK/alipay/lib/alipay_notify.class.php");
		$alipayConfig=array(
			"partner"=>2088421324229191,
			"private_key_path"=>"./SDK/alipay/key/rsa_private_key.pem",
			"ali_public_key_path"=>"./SDK/alipay/key/alipay_public_key.pem",
			"sign_type"=>strtoupper('RSA'),
			"input_charset"=>strtolower('utf-8'),
			"cacert"=>getcwd().'/SDK/alipay/cacert.pem',
			"transport"=>"http"
		);
		$alipayNotify = new \AlipayNotify($alipayConfig);
		$verify_result = $alipayNotify->verifyNotify();
		$trade_status = $_POST['trade_status'];
		$price=$_POST["total_fee"];
		$ordernum = $_POST['out_trade_no'];
		$ocode=rand(100000,999999);
		$order=D("Order");
		
		if($trade_status == 'TRADE_SUCCESS'){
			$order->paySuccess($ordernum,$price,1,$ocode);
			//获取手机号
			$odata=$order->field("cellphone,iscode")->where("ordernum=%d",array($ordernum))->find();
			if($odata["iscode"]=='0'){
				$this->sendTemplateSMS($odata["cellphone"],array($ocode),112452);
				$order->where("ordernum=%d",array($ordernum))->save(array("iscode"=>"1"));
			}
			
		}else if($trade_status == 'TRADE_FINISHED'){
				
		}
	}
	
	private function sendTemplateSMS($to,$datas,$tempId){
		include("./SDK/SMS/ccpsms/Rest.class.php");
		 // 初始化REST SDK
		$rest = new \Rest(C("smsServerIP"),C("smsServerPort"),C("smsSoftVersion"));
		$rest->setAccount(C("smsSid"),C("smsToken"));
		$rest->setAppId(C("smsAppid"));
		$rest->sendTemplateSMS($to,$datas,$tempId);
		 // 发送模板短信
		 /*echo "Sending TemplateSMS to $to <br/>";
		 if($result == NULL ) {
			 echo "result error!";
			 break;
		 }
		 if($result->statusCode!=0) {
			 echo "error code :" . $result->statusCode . "<br>";
			 echo "error msg :" . $result->statusMsg . "<br>";
			 //TODO 添加错误处理逻辑
		 }else{
			 echo "Sendind TemplateSMS success!<br/>";
			 // 获取返回信息
			 $smsmessage = $result->TemplateSMS;
			 echo "dateCreated:".$smsmessage->dateCreated."<br/>";
			 echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
			 //TODO 添加成功处理逻辑
		 }*/
	}
	
}