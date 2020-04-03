<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\CommonController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class ReviewsController extends CommonController {
	
	public function index(){
		$gid=get_int($_GET["gid"]);
		if($gid>0){
			$isreviews=$this->isReviews();
			
			$reviews=D("Reviews");
			$pagesize=8;
			$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
			$total=$reviews->getViewsTotal($gid,$isreviews);
			$fpage=new PageOrg($total,$current_page,$pagesize);
			$pageInfo=$fpage->getPageInfo();
			$datalist=$reviews->getViewsPage($pageInfo["row_offset"],$pageInfo["row_num"],$gid,$isreviews);
			if($datalist){
				MsgLogic::success(200,$datalist,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"]."","total"=>$total));
			}else{
				MsgLogic::error(201);
			}
		}else{
				MsgLogic::error(302,urlencode("获取失败"));
		}
	}
	
	//提交评价
	public function add(){
        //$rsdata='[{"gid":286026274,"myid":696443691,"rsid":1,"score":2},{"gid":286026274,"myid":696443691,"rsid":7,"score":5}]';
		$uid=get_str($_POST["uid"]);
		$gid=get_int($_POST["gid"]);
		$content=filterCode(faceEncode($_POST["content"]));
		$ordernum=get_int($_POST["ordernum"]);
		$rsarr=json_decode(filterCode($_POST["rsdata"]),true);
		if(is_array($rsarr)) {
            if ($uid != '' && $gid > 0 && $ordernum > 0) {
                if ($content == "" || $content == "undefined" || $content == "null") {
                    MsgLogic::error(302, urlencode("请输入评价内容"));
                }
                $reviews = D("Reviews");
                $msg = $reviews->addReviews($uid, $gid, $content, $rsarr, $ordernum);
                MsgLogic::success(200, urlencode($msg));
            } else {
                MsgLogic::error(302, urlencode("获取失败"));
            }
        }else{
            MsgLogic::error(302, urlencode("数据格式不正确"));
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
	public function serviced()
    {
        $targetid = get_int($_GET["targetid"]);
        if ($targetid > 0) {
            $reviserver = D("Reviserver");
            $data = $reviserver->getService($targetid);
            MsgLogic::success(200, $data);
        } else {
            MsgLogic::error(302, urlencode("没有获取到活动ID"));
        }
    }

	
}