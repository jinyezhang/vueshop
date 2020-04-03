<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class PublicController extends IsTokenController {

	public function __construct(){
		parent::__construct();
	}
	
	//城市
	public function city(){
		$city=M("City");
		$data=$city->field("id,title,image")->order("num desc,id asc")->select();
		if(count($data)>0){
			foreach($data as $v){
				if($v["image"]!=""){
					$image=getHost()."/uploadfiles/".$v["image"];
				}else{
					$image="";	
				}
				$datalist[]=array(
					"id"=>$v["id"],
					"title"=>urlencode($v["title"]),
					"image"=>$image
				);
			}
			MsgLogic::success(200,$datalist);
		}else{
			MsgLogic::error(201);	
		}
	}

	//栏目单篇信息
	public function singleinfo(){
		$cid=get_int($_GET["cid"]);
		if($cid>0){
			$column=M("Columns");
			$data=$column->field("c_names,bodys")->where("id=%d",array($cid))->find();
			$datalist=array(
				"c_names"=>urlencode($data["c_names"]),
				"bodys"=>urlencode($data["bodys"])
			);
			MsgLogic::success(200,$datalist);
		}else{
			MsgLogic::error(302,urlencode("获取失败"));
		}
	}
	
	//我的消息最新数据
	public function lastMsg(){
		$pushmsg=M("Pushmsg");
		$data=$pushmsg->field("id")->order("id desc")->limit(0,1)->find();
		$datalist=array("msgid"=>get_int($data["id"]));
		MsgLogic::success(200,$datalist);
	}

    public function hotwords(){
        $hw=M("Hotwords");
        $data=$hw->field("title")->order("num desc,scount desc")->limit(0,12)->select();
        if(count($data)>0){
            foreach($data as $v){
                $datalist[]=array(
                    "title"=>urlencode($v["title"])
                );
            }
            MsgLogic::success(200,$datalist);
        }else{
            MsgLogic::error(201);
        }

    }
	
}