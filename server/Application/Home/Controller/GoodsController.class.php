<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
use Common\Org\PageOrg;
class GoodsController extends IsTokenController {
	public $kwords;

    public function __construct(){
        parent::__construct();
        $this->kwords=get_str((trim($_REQUEST["kwords"])));
        $this->kwords=str_replace("%","\%",$this->kwords);
        $this->kwords=str_replace("_","\_",$this->kwords);
        $this->kwords=urlencode($this->kwords);
    }

    //属性参数
    public function param(){
        if($this->kwords!=''){
            $goods=M("Goods");
            $attr=D("Attr");

            $crow=$goods->field("parentid")->where("title like '%s'",array("%".urldecode($this->kwords)."%"))->order("num desc,id desc")->limit(0,1)->find();
            $cid=$crow["parentid"];

            $attrData=$attr->getParam($cid);

            if(count($attrData)>0){
                MsgLogic::success(200,$attrData);
            }else{
                MsgLogic::error(201);
            }

        }else{
            MsgLogic::error(201);
        }
    }

	//搜索
	public function search(){
            //$jsonparam='["949","956"]';
            $param=json_decode(filterCode($_GET["param"]),true);
            $price1=get_str($_GET["price1"]);
            $price2=get_str($_GET["price2"]);
            $otype=get_str($_GET["otype"]);
            $cid=get_int($_GET["cid"]);
            $goods=D("Goods");
            $pagesize=10;
			$current_page=isset($_GET["page"])?intval($_GET["page"]):1;
			$total=$goods->getGoodsTotal(urldecode($this->kwords),$param,$price1,$price2,$cid);
			$fpage=new PageOrg($total,$current_page,$pagesize);
			$pageInfo=$fpage->getPageInfo();
			$datalist=$goods->getGoodsPage($pageInfo["row_offset"],$pageInfo["row_num"],urldecode($this->kwords),$param,$price1,$price2,$otype,$cid);
			if(count($datalist)>0){
                //增加热门关键词
                $hw=M("Hotwords");
                $hw->execute("insert into __HOTWORDS__ (title,scount) values ('".urldecode($this->kwords)."',1) on duplicate KEY UPDATE scount=scount+1");

				MsgLogic::success(200,$datalist,array("pagesize"=>"{$pagesize}","page"=>"".$current_page."","pagenum"=>"".$pageInfo["page_num"]."","total"=>$total));

			}else{
				MsgLogic::error(201);
			}
	}

    //商品详情
    public function info(){
        $type=get_str($_GET["type"]);
        if($type!=''){
            $cid=get_int($_GET["cid"]);
            $gid=get_int($_GET["gid"]);
            switch($type){
                case "details":
                    $this->details($gid);
                    break;
                case "spec":
                    $this->specData($gid);
                    break;
            }
        }else{
            MsgLogic::error(302,urlencode("获取失败"));
        }
    }

    //商品详情参数
    private function details($gid){
        if($gid>0) {
            $goods = M("Goods");
            $img = M("Goodsimgs");

            $grow = $goods->field("title,price,freight,sales,bodys,qid")->where("qid=%d", array($gid))->find();

            $imgrow = $img->field("id,photo")->where("gid=%d", array($gid))->select();
            if (count($imgrow) > 0) {
                foreach ($imgrow as $v) {
                    if ($v["photo"] != '') {
                        $photo = getHost() . "/uploadfiles/" . $v["photo"];
                    } else {
                        $photo = "";
                    }
                    $imgs[] = $photo;
                }
            }

            $data = array(
                "gid"=>$grow["qid"],
                "title" => urlencode($grow["title"]),
                "price" => floatval($grow["price"]),
                "freight" => floatval($grow["freight"]),
                "sales" => $grow["sales"],
                "bodys"=>stripslashes($grow["bodys"]),
                "images" => $imgs
            );
            MsgLogic::success(200, $data);
        }else{
            MsgLogic::error(302, urlencode("获取失败"));
        }
    }

    //商品规格
    private function specData($gid){
        if($gid>0){
            $spec=D("Specval");
            $data=$spec->getSpec($gid);
            if(count($data)>0){
                MsgLogic::success(200, $data);
            }else{
                MsgLogic::error(201);
            }
        }else{
            MsgLogic::error(302, urlencode("获取失败"));
        }
    }

    //收藏
    public function fav(){
        $uid=get_str($_GET["uid"]);
        $gid=get_int($_GET["gid"]);
        if($uid!='' && $gid>0) {
            $fav = M("Fav");
            $favcount=$fav->where("uid='%s' and gid=%d",array($uid,$gid))->count();
            if($favcount>0){
                MsgLogic::error(302,urlencode("已收藏过！"));
            }else{
                $data["uid"]=$uid;
                $data["gid"]=$gid;
                $data["times"]=date("Y-m-d H:i:s");
                $fav->add($data);
                MsgLogic::success(200,urlencode("收藏成功！"));
            }
        }else{
            MsgLogic::error(302, urlencode("请登录会员"));
        }
    }

}