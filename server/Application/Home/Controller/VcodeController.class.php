<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class VcodeController extends IsTokenController {

    //验证码
    public function chkcode(){
        import("Common.Org.ValidationCode");
        $image = new \ValidationCode(60,26,4);
        $image->showImage();
        $_SESSION["imgcode"] =strtoupper($image->getCheckCode());
    }
	
}