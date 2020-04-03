<?php
namespace User\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class ReviewsController extends IsTokenController {
	
	//我的评价
	public function index(){
		$uid=get_int($_GET["uid"]);
		if($uid>0){
			$reviews=D("Reviews");
			$pagesize=6;
			$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
			$total=$reviews->getReviewsTotal($uid);
			$fpage=new PageOrg($total,$current_page,$pagesize);
			$pageInfo=$fpage->getPageInfo();
			$data=$reviews->getReviewsPage($pageInfo["row_offset"],$pageInfo["row_num"],$uid);
			if(count($data)>0){
				MsgLogic::success(200,$data,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"].""));
			}else{
				MsgLogic::error(201);	
			}
		}else{
			MsgLogic::error(201,urlencode("没有获取到会员ID"));	
		}
	}
	
	
}