<?php
namespace Common\Logic;

class MsgLogic{
	static $responseCode="";
	private function __construct(){}
    public static function getResponseCode() {
		if(self::$responseCode==""){
        	self::$responseCode = C('RESPONSE_CODE');
		}
        return self::$responseCode;
    }

    public static function error($code, $message = '')
    {
        $responseCode = self::getResponseCode();
        $jsondata=json_encode(array(
            'status'=>0,
            'code' => $code,
            'data' => empty($message) ? $responseCode[$code] : $message
        ));
        exit(urldecode($jsondata));
    }

    public static function success($code, $message="",$pageinfo='')
    {
        $responseCode = self::getResponseCode();
		if($pageinfo!=""){
			$jsondata=json_encode(array(
				'status'=>1,
				'code' => $code,
				'data' => empty($message) ? $responseCode[$code] : $message,
				"pageinfo"=>$pageinfo
			));
		}else{
			$jsondata=json_encode(array(
				'status'=>1,
				'code' => $code,
				'data' => empty($message) ? $responseCode[$code] : $message
			));
		}
		
        exit(urldecode($jsondata));

    }
}