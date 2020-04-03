<?php
namespace Common\Controller;
use Think\Controller;
use Common\Logic\MsgLogic;
class CommonController extends IsTokenController{
	public function __construct(){
		parent::__construct();
	}

    //评价审核状态
    public function isReviews(){
        $rs=M("Reviewsetting");
        $data=$rs->where("id=%d",array(1))->find();
        return $data["isreviews"];
    }
}