<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\CommonController;
use Common\Logic\MsgLogic;
class IndexController extends CommonController {
	private $id,$offset,$length,$column,$goods;
	
	public function __construct(){
		parent::__construct();
		$this->id=get_int($_GET["id"]);
		$this->offset=get_int($_GET["offset"]);
		$this->length=get_int($_GET["length"]);
		$this->column=new \Common\Model\ColumnsModel;
        $this->goods=new \Common\Model\GoodsModel;
	}
	
	//幻灯片
	public function slide(){
		$ad=D("Ad");
		$data=$ad->where("gid=%d",array(1))->order("num desc,id asc")->select();
        if(count($data)>0){
            foreach($data as $v) {
                if($v["photo"]!=""){
                    $image=getHost()."/uploadfiles/".$v["photo"];
                }else{
                    $image='';
                }
                $datalist[] = array(
                    "title" => urlencode($v["title"]),
                    "image"=>$image,
                    "webs"=>$v["webs"]
                );
            }
            MsgLogic::success(200,$datalist);
        }else{
            MsgLogic::error(201);
        }
	}
	
	//导航
	public function nav(){
		$data=$this->column->menuList(0,0,4);
		if(count($data)>0){
			foreach($data as $v){
				if($v["image"]!=""){
					$image=getHost()."/uploadfiles/".$v["image"];
				}else{
					$image="";	
				}
				$datas[]=array(
					"cid"=>$v["id"],
					"title"=>urlencode($v["c_names"]),
					"image"=>$image,
				);
			}
            MsgLogic::success(200,$datas);
		}else{
            MsgLogic::error(201);
        }
	}


    //首页产品
    public function goodsLevel(){
        $gdata=$this->goods->getGoods(0,7);
        if(count($gdata)>0){
            MsgLogic::success(200,$gdata);
        }else{
            MsgLogic::error(201);
        }
    }

    //推荐
    public function recom(){
        $data=$this->goods->recomData();
        if(count($data)>0){
            MsgLogic::success(200,$data);
        }else{
            MsgLogic::error(201);
        }
    }
	
}