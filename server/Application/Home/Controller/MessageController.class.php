<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class MessageController extends IsTokenController {
	
	public function index(){
		$actid=get_int($_GET["actid"]);
		if($actid>0){
			$msg=D("Message");
			$pagesize=12;
			$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
			$total=$msg->getMessageTotal($actid);
			$fpage=new PageOrg($total,$current_page,$pagesize);
			$pageInfo=$fpage->getPageInfo();
			$datalist=$msg->getMessagePage($pageInfo["row_offset"],$pageInfo["row_num"],$actid);
			if($datalist){
				MsgLogic::success(200,$datalist,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"].""));
			}else{
				MsgLogic::error(201);
			}
		}else{
				MsgLogic::error(302,urlencode("获取失败"));
		}
	}
	
	//提交留言
	public function add(){
		$uid=get_int($_POST["uid"]);
		$actid=get_int($_POST["actid"]);
		$content=get_str(delwrap(faceEncode($_POST["content"])));
		if($uid>0 && $actid>0){
			if($content=="" || $content=="undefined" || $content=="null"){
				MsgLogic::error(302,urlencode("请输入留言内容"));
			}
			$act=M("Createact");
			$actdata=$act->field("title")->where("id=%d",array($actid))->find();
			$actname=$actdata['title'];
			
			$msg=M("Message");
			$msgdata=array(
				"uid"=>$uid,
				"actid"=>$actid,
				"actname"=>$actname,
				"content"=>$content,
				"times"=>date("Y-m-d H:i:s")
			);
			$msg->add($msgdata);
			MsgLogic::success(200,urlencode("谢谢您的留言，我们会尽快回复！"));
		}else{
			MsgLogic::error(302,urlencode("获取失败"));	
		}
	}
	
	//上传图片
	public function upimage(){
		import("Common.Org.UploadFile");
		$uf=new \UploadFile();
		$imgcount=get_int($_POST["imgcount"]);
		if($imgcount>0){
			for($i=1;$i<=$imgcount;$i++){
				$uf->upfileload("image{$i}",'./userfiles/reviews',array("jpg","gif","png","jpeg"),10*1024*1024,"zip");
				$imgarr[]=json_decode($uf->msg,true);
			}
			if(count($imgarr)>0){
				for($i=0;$i<count($imgarr);$i++){
					if($imgarr[$i]["msg"]=='1'){
						$imgs[]=urlencode($imgarr[$i]["msbox"]);	
					}
				}
			}
			MsgLogic::success(200,$imgs);
		}else{
			MsgLogic::error(302,urlencode("请上传图片"));	
		}
	}
	
	//评价项目管理
	public function service(){
		$reviserver=M("Reviserver");
		$data=$reviserver->field("id,title")->order("num asc,id asc")->select();
		if(count($data)>0){
			foreach($data as $v){
				$datalist[]=array(
					"rsid"=>$v["id"],
					"title"=>urlencode($v["title"])
				);
			}
			MsgLogic::success(200,$datalist);
		}else{
			MsgLogic::error(201);	
		}
	}
	
	//评价后的服务
	public function serviced(){
		$actid=get_int($_GET["actid"]);
		if($actid>0){
			$reviserver=D("Reviserver");
			$data=$reviserver->getService($actid);
			MsgLogic::success(200,$data);
		}else{
			MsgLogic::error(302,urlencode("没有获取到活动ID"));	
		}
	}
	
}