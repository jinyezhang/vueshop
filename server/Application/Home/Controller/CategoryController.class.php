<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class CategoryController extends IsTokenController {
	private $cid,$offset,$length,$goods,$column,$aid;
	
	public function __construct(){
		parent::__construct();
		$this->cid=get_int($_GET["cid"]);
		$this->aid=get_int($_GET["aid"]);
		$this->offset=get_int($_GET["offset"]);
		$this->length=get_int($_GET["length"]);
		$this->column=new \Common\Model\ColumnsModel;
		$this->goods=new \Common\Model\GoodsModel;
	}
	
	//产品左侧菜单
	public function menu(){
        $data=$this->column->field("id,c_names")->where("parent_id=%d and tpl='%s'",array(0,'goods'))->order("num asc,id asc")->select();
        if(count($data)>0){
            foreach($data as $v){
                $datalist[]=array(
                    "cid"=>$v["id"],
                    "title"=>urlencode($v["c_names"])
                );
            }
            MsgLogic::success(200,$datalist);
        }else{
            MsgLogic::error(201);
        }
	}

    //产品展示
    public function show(){
        $data=$this->goods->showData($this->cid);
        if(count($data)>0){
            MsgLogic::success(200,$data);
        }else{
            MsgLogic::error(201);
        }
    }
	
}