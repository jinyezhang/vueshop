<?php
namespace User\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class FavController extends IsTokenController {
	
	//我的收藏
	public function index(){
		$uid=get_str($_GET["uid"]);
		if($uid!=''){
			$fav=D("fav");
			$pagesize=6;
			$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
			$total=$fav->getFavTotal($uid);
			$fpage=new PageOrg($total,$current_page,$pagesize);
			$pageInfo=$fpage->getPageInfo();
			$data=$fav->getFavPage($pageInfo["row_offset"],$pageInfo["row_num"],$uid);
			if(count($data)>0){
				MsgLogic::success(200,$data,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"]."","itemTotal"=>$total));
			}else{
				MsgLogic::error(201);	
			}
		}else{
			MsgLogic::error(201,urlencode("请登录会员"));
		}
	}
	
	//删除我的收藏
	public function del(){
		$uid=get_str($_GET["uid"]);
		$fid=get_int($_GET["fid"]);
		if($uid!='' && $fid>0){
			$fav=M("Fav");
			$fav->where("id = {$fid} and uid='{$uid}'")->delete();
			MsgLogic::success(200,urlencode("删除成功！"));
		}else{
			MsgLogic::error(201,urlencode("获取失败"));
		}	
	}
	
	
}