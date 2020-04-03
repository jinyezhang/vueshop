<?php
namespace Home\Controller;
use Think\Controller;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class WxpayController extends Controller {
	
	//微信支付
	public function notify(){
		require "./SDK/wxpay/PayNotifyCallback.php";
		$notify = new \PayNotifyCallBack();
		$notify->Handle(false);
		$returnValues=$notify->GetValues();
		//交易成功  
		if(!empty($returnValues['return_code']) && $returnValues['return_code'] == 'SUCCESS'){  
			$xmlData = $GLOBALS['HTTP_RAW_POST_DATA']; 
			$postObj = simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA);
			$ordernum=$postObj->out_trade_no;
			$price=floatval($postObj->total_fee/100);
			$ocode=rand(100000,999999);
			$order=D("Order");
			$order->paySuccess($ordernum,$price,2,$ocode);
			
			//获取手机号
			$odata=$order->field("cellphone,iscode")->where("ordernum=%d",array($ordernum))->find();
			if($odata["iscode"]=='0'){
				$this->sendTemplateSMS($odata['cellphone'],array($ocode),112452);
				$order->where("ordernum=%d",array($ordernum))->save(array("iscode"=>"1"));
			}
		}              
		echo $notify->ToXml();//返回给微信确认
	}
	
	private function sendTemplateSMS($to,$datas,$tempId){
		include("./SDK/SMS/ccpsms/Rest.class.php");
		 // 初始化REST SDK
		$rest = new \Rest(C("smsServerIP"),C("smsServerPort"),C("smsSoftVersion"));
		$rest->setAccount(C("smsSid"),C("smsToken"));
		$rest->setAppId(C("smsAppid"));
		$rest->sendTemplateSMS($to,$datas,$tempId);
		 // 发送模板短信
		 //echo "Sending TemplateSMS to $to <br/>";
//		// if($result == NULL ) {
////			 echo "result error!";
////			 break;
////		 }
//		 if($result->statusCode!=0) {
//			 echo "error code :" . $result->statusCode . "<br>";
//			 echo "error msg :" . $result->statusMsg . "<br>";
//			 //TODO 添加错误处理逻辑
//		 }else{
//			 echo "Sendind TemplateSMS success!<br/>";
//			 // 获取返回信息
//			 $smsmessage = $result->TemplateSMS;
//			 echo "dateCreated:".$smsmessage->dateCreated."<br/>";
//			 echo "smsMessageSid:".$smsmessage->smsMessageSid."<br/>";
//			 //TODO 添加成功处理逻辑
//		 }
	}
	
}