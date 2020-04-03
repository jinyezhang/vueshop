<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\LogModel;
class GoodsListController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->safeColumn($this->id);
	}

    public function index(){
        $this->display();
    }
	
	public function manage(){
		
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->id))->field("fun")->find();
		$this->assign("cdata",$cdata);
		
		//文章移动无限级分类
		$this->assign("moveselpcls",$column->movegoodslist(0,0,0,$this->id));
		
		if($this->kwords!=""){
			$strname="&kwords=".$this->kwords;
			$this->assign("strname",$strname);
		}

		$goods=D("Goods");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
        $total=$goods->getGoodsTotal($this->id,urldecode($this->kwords));
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
        $datalist=$goods->getGoodsPage($pageInfo["row_offset"],$pageInfo["row_num"],$this->id,urldecode($this->kwords));
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."/manage?",$strname."&id={$this->id}"));
		}
		
		$this->display();
	}

    public function left(){
        $column=D("Columns");
        $cdata=$column->where("id=%d",array($this->id))->field("fun")->find();
        $this->assign("cdata",$cdata);
        $this->display();
    }

	//排序
	public function order(){
        $goods=M("Goods");
		for ($i=0;$i<count($_POST["num"]);$i++){
            $goods->where("qid=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."/manage?id={$this->id}'</script>";
		exit;
	}
	
	//移动数据
	public function movedata(){
		$this->modsql();
		$column=D("Columns");
		$column->moveColumn();
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$goods=M("goods");
			$goods->where("id in ({$del})")->save(array("parentid"=>$colid));
			header("location:".__CONTROLLER__."/manage?id={$this->id}");
			exit;
		}else{
			echo "<script>alert('请选择要移动的数据');history.go(-1)</script>";	
		}
	}
	
	//删除
	public function del(){
        $goods=D("Goods");
        $goods->delgoods($this->id);
	}
	
	//产品
	public function goods(){
		$column=D("Columns");
        $attr=D("Attr");

		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);

        //获取属性
        $oneattr=$attr->goodsAttr($this->id);
        $this->assign("oneattr",$oneattr);

        //获取规格
        $specData=$attr->field("id,name")->where("cid=%d and type=%d",array($this->id,1))->order("num desc,id asc")->select();
        $this->assign("specTotal",count($specData));
        $this->assign("specData",$specData);

		
		if($this->action=='add'){
			$this->addsql();
			$goods=D("Goods");
            $goods->addGoodsData($this->id);
		}
		
		$this->display();
	}
	
	//产品修改
	public function editgoods(){
		$column=D("Columns");
        $attr=D("Attr");
        $goods=D("Goods");

		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modgoodslist(0,0,0,$cdata["id"],$cdata["c_names"]));

		$data=$goods->where("qid=%d",array($this->id))->field("title,dates,bodys,price,freight")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));

        //获取属性
        $oneattr=$attr->goodsAttr($this->cid,$this->id);
        $this->assign("oneattr",$oneattr);

        //产品规格
        $specData=$attr->getSpec($this->cid,$this->id);
        $this->assign("specTotal",count($specData));
        $this->assign("specData",$specData);

		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
            $goods->modGoods($this->cid,$this->id,$this->page);
			
		}
		$this->display();
	}
    //ajax产品图片
    public function ajxgoodsimgs(){
        $gimgs=M("Goodsimgs");
        $gid=get_int($_GET["gid"]);
        $this->assign("gid",$gid);
        $action=get_str($_GET["action"]);
        if($action=="del"){
            $gimgs->where("id=%d",array($this->id))->delete();
        }

        $images=$gimgs->field("id,photo")->where("gid=%d",array($gid))->select();
        $this->assign("images",$images);

        $this->display();
    }

    //属性管理
    public function attr(){
        $type=get_int($_GET["type"]);
        $this->assign("type",$type);

        $column=D("Columns");
        $cname=$column->getTitle($this->id);
        $this->assign("cname",$cname);

        $attr=D("Attr");
        $current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
        $this->assign("page",$current_page);
        $total=$attr->getParaTotal($this->id,$type);
        $this->assign("total",$total);
        $fpage=new PageOrg($total,$current_page,12);
        $pageInfo=$fpage->getPageInfo();
        $datalist=$attr->getParaPage($pageInfo["row_offset"],$pageInfo["row_num"],$this->id,$type);
        if($datalist){
            $this->assign("datalist",$datalist);
            $this->assign("getpage",$fpage->getpage($current_page,__ACTION__."?","&id={$this->id}"));
        }

        if($this->action=='add'){
            $this->addsql();
            $attr->addPara($this->id,$type);
        }
        if($this->action=='del'){
            $this->delsql();
            $attr->delPara($this->id,$type);
        }
        if($this->action=="order"){
            $attr->orderPara($this->id,$type);
        }
        $this->display();
    }

    //参数修改
    public function editattr(){
        $type=get_int($_GET["type"]);
        $this->assign("type",$type);
        //标题文章列表
        $column=D("Columns");
        $attr=D("Attr");
        $cname=$column->getTitle($this->cid);
        $this->assign("cname",$cname);

        $getattr=$attr->field("name,id,ftype")->where("id=%d",array($this->id))->find();
        $this->assign("getattr",$getattr);

        //获取值
        $getVal=$attr->getParaVal($this->id);
        $this->assign("getVal",$getVal);

        //修改值
        if($this->action=='mod'){
            $this->modsql();
            $attr->modParaVal($this->cid,$this->id);
        }

        $this->display();
    }

    //ajax删除参数值
    public function ajaxdelpara(){
        $attr=D("Attr");
        //删除
        if($this->id>0){
            $this->delsql();
            $attr->where("id=%d",array($this->id))->delete();
        }

        //获取值
        $getVal=$attr->getParaVal($this->cid);
        $this->assign("getVal",$getVal);


        $this->display();
    }
	
	//选项卡
	public function card(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$this->assign("date",date("Y-m-d"));
		
		//获取产品编号
		$randnum=chr(mt_rand(65,90)).chr(mt_rand(65,90)).mt_rand(1000,10000);
		$this->assign("randnum",$randnum);
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->parentid=$this->id;
				$art->dates=date("Y-m-d");
				$art->num=999;
				$art->add();
				LogModel::setLog("添加“{$title}”","添加");
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//选项卡修改
	public function editcard(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cdata["id"],$cdata["c_names"]));
		
		$art=M("Article");
		$data=$art->where("id=%d",array($this->id))->field("title,photo,money,pronumber")->find();
		$this->assign("data",$data);
		if($this->action=='mod'){
			$this->modsql();
			$column=D("Columns");
			$column->moveColumn();
			$title=get_str(trim($_POST["title"]));
			$columns=get_int($_POST["columns"]);
			if($title!=""){
				$art=M("Article");
				$art->create();
				$art->parentid=$columns;
				$art->where("id=%d",array($this->id))->save();
				LogModel::setLog("修改“{$title}”","修改");
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//内容列表
	public function cardlist(){
		
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		//获取栏目名称
		$art=M("Article");
		$adata=$art->where("id=%d",array($this->id))->field("title")->find();
		$this->assign("adata",$adata);
		
		$content=M("Content");
		$current_page=isset($_REQUEST["pg"])?intval($_REQUEST["pg"]):1;
		$this->assign("cupg",$current_page);
		$total=$content->where("pid=%d",array($this->id))->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$content->where("pid=%d",array($this->id))->field("id,title,num")->order("num asc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage2($current_page,__ACTION__."?","&id={$this->id}&cid={$this->cid}&page={$this->page}"));
		}
		
		//修改排序
		if($this->action=='order'){
			$content=M("Content");
			for ($i=0;$i<count($_POST["num"]);$i++){
				$content->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
			}
			echo "<script>alert ('排序修改成功');location.href='".__ACTION__."?id={$this->id}&page={$this->page}&cid={$this->cid}'</script>";
			exit;
		}
		
		//删除
		if($this->action=="del"){
			$del=@implode(",",$_POST["del"]);
			if($del!=""){
				$content=M("Content");
				$content->where("id in ({$del})")->delete();
				echo "<script>alert('删除成功');location.href='".__ACTION__."?id={$this->id}&cid={$this->cid}&page={$this->page}&pg={$this->pg}'</script>";
				exit;
			}else{
				echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";	
			}
		}
		
		$this->display();
	}
	
	//添加选项卡内容
	public function cardcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		//获取栏目名称
		$art=M("Article");
		$adata=$art->where("id=%d",array($this->id))->field("title")->find();
		$this->assign("adata",$adata);
		
		if($this->action=='add'){
			$this->addsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$content=M("Content");
				$data["title"]=$title;
				$data["bodys"]=$bodys;
				$data["pid"]=$this->id;
				$data["num"]=999;
				$content->add($data);
				echo "<script>alert ('添加成功');location.href='".__ACTION__."?id={$this->id}&cid={$this->cid}&page={$this->page}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
	//修改选项卡内容
	public function editcardcontent(){
		$column=D("Columns");
		$cname=$column->getTitle($this->cid);
		$this->assign("cname",$cname);
		
		//获取栏目名称
		$art=M("Article");
		$adata=$art->where("id=%d",array($this->id))->field("title")->find();
		$this->assign("adata",$adata);
		
		$content=M("Content");
		$data=$content->where("id=%d",array($this->cardid))->field("title,bodys")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		if($this->action=='mod'){
			$this->modsql();
			$title=get_str(trim($_POST["title"]));
			$bodys=get_str($_POST["bodys"],1);
			if($title!=""){
				$content=M("Content");
				$data["title"]=$title;
				$data["bodys"]=$bodys;
				$content->where("id=%d",array($this->cardid))->save($data);
				echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$this->id}&cid={$this->cid}&page={$this->page}&pg={$this->pg}&cardid={$this->cardid}';</script>";
				exit;
			}
		}
		
		$this->display();
	}
	
}