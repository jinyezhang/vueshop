<?php
namespace Home\Controller;
use Think\Controller;
class ColumnManageController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(2);
	}
	
	public function index(){
		
		$sname=str_replace("%","\%",$_POST["search"]);
		$sname=str_replace("_","\_",$sname);
		$sname=trim(get_str($sname));
		
		$column=D("Columns");
		
		$this->assign("menu",$column->menu(0,$sname));
		$this->assign("total",$column->total);
		
		$this->display();
	}
	
	//栏目排序
	public function corder(){
		$num=$_GET["num"];
		$column=D("Columns");
		//上移排序
		if($this->action=='uppaixu'){
			$column->prevorder($this->pid,$this->id,$num);
		}else if($this->action=='downpaixu'){//下移排序
			$column->nextorder($this->pid,$this->id,$num);
		}
		echo $column->menu(0,'');
	}
	
	//删除
	public function del(){
		$this->delsql();
		$column=D("Columns");
		$column->delcolumn($this->id);
	}
	
	//编辑
	public function edit(){
		$this->safeColumn($this->id);
		$this->modsql();
		$column=D("Columns");
		if($this->pid==0){
			$cdata=$column->where("id=%d",array($this->id))->field("id,c_names,fun")->find();
			$cnb=$cdata["c_names"];
			$cnid=0;
		}else{
			$cdata=$column->where("id=%d",array($this->pid))->field("id,c_names,fun")->find();
			$cnb=$cdata["c_names"];
			$cnid=$cdata["id"];
		}
		
		//无限级分类表单
		$this->assign("modselpcls",$column->modselpcls(0,0,0,$cnid,$cnb));
		
		$data=$column->where("id=%d",array($this->id))->field("fun,tpl,c_names,bodys,image")->find();
		$sabodys=htmlspecialchars($data["bodys"]);
		$this->assign("sabody",$sabody);
		$this->assign("data",$data);
		
		$this->display();	
	}
	
	//显示模板
	public function showtpl(){
		$fun=get_str($_POST["fun"]);
		if($fun!=""){
			$tpl=M("Tpl");
			$data=$tpl->where("fun='%s'",array($fun))->field("id,title")->select();
			if($data){
				$html="[";
				foreach($data as $v){
					$rhtml.="{\"id\":\"".$v["id"]."\",\"title\":\"".$v["title"]."\"},";
				}
				$html.=rtrim($rhtml,",");
				$html.="]";
			}else{
				$html="[{\"id\":\"0\"}]";
			}
			echo $html;
		}
	}
	
	public function mod(){
		$this->modsql();
		$column=D("Columns");
		$column->modColumn($this->id,$this->pid);
	}
	
	//添加页面
	public function add(){
		$this->safeColumn($this->id);
		$column=M("Columns");
		$data=$column->where("id=%d",array($this->id))->field("id,c_names,fun,bodys,tpl")->find();
		$this->assign("datas",$data);
		
		$tpl=M("Tpl");
		$tpldata=$tpl->where("id=%d",array($data["tpl"]))->field("title")->find();
		$this->assign("tpldata",$tpldata);
		
		$article=M("Article");
		$artcount=$article->where("parentid=%d",array($this->id))->count();
		if($artcount>0 || $data["bodys"]!=""){
			echo "<script>alert('此栏目下面还有数据，请先移除数据再添加栏目！');location.href='".__CONTROLLER__."#{$this->id}'</script>";
			exit;
		}
		
		$this->display();
	}
	
	public function incrData(){
		$this->addsql();
		$column=D("Columns");
		$data=$column->where("id=%d",array($this->id))->field("bodys,fun")->find();
		$bodys=$data["bodys"];
		$fun=$data["fun"];
		$column->addColumn($this->id,$fun,$bodys);
		
	}
	
	//添加到桌面
	public function addDesk(){
		$column=M("Columns");
		$cdata=$column->where("id=%d",array($this->cid))->field("id,c_names")->find();
		if($cdata["id"]>0){
			//判断桌面上是否已经有这个栏目
			$desktop=M("Desktop");
			$total=$desktop->where("cid=%d and uid=%d",array($cdata["id"],$_SESSION["adminid"]))->count();
			if($total>0){
				echo "桌面上已有此栏目，请不要重复添加！";
			}else{
				$data["cid"]=$cdata["id"];
				$data["title"]=$cdata["c_names"];
				$data["pic_path"]=mt_rand(1,40).".png";
				$data["uid"]=$_SESSION["adminid"];
				$desktop->add($data);
				echo "添加到桌面成功！";
			}
		}	
	}
	
}