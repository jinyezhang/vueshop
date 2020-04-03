<?php
namespace User\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class PushmsgController extends IsTokenController {
	
	//我的消息
	public function index(){
		$pushmsg=D("Pushmsg");
		$pagesize=6;
		$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
		$total=$pushmsg->count();
		$fpage=new PageOrg($total,$current_page,$pagesize);
		$pageInfo=$fpage->getPageInfo();
		$data=$pushmsg->order("times desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if(count($data)>0){
			foreach($data as $v){
				$datalist[]=array(
					"msgid"=>$v["id"],
					"content"=>urlencode($v["content"]),
					"times"=>$v["times"]
				);
			}
			MsgLogic::success(200,$datalist,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"].""));
		}else{
			MsgLogic::error(201);	
		}
	}
	
	//消息详情
	public function desc(){
		$msgid=get_int($_GET["msgid"]);
		if($msgid>0){
			$pushmsg=M("Pushmsg");
			$data=$pushmsg->where("id=%d",array($msgid))->find();
			if(count($data)>0){
				$msgdata=array(
					"content"=>urlencode($data["content"]),
					"times"=>$data["times"]
				);
				MsgLogic::success(200,$msgdata);
			}else{
				MsgLogic::error(201);	
			}
		}else{
			MsgLogic::error(302,urlencode("获取失败"));
		}
	}
	
	
}