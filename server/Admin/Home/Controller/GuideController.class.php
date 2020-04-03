<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class GuideController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(45);
	}
	
	public function index(){
		
		$guide=M("Guide");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$guide->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$guide->field("id,num,image")->order("num desc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		
		$this->display();
	}
	
	public function add(){
		$this->addsql();
		$image=get_str(trim($_POST["image"]));
		if($image!=""){
			$guide=M("Guide");
			$guide->create();
			$guide->add();
			echo "<script>alert ('添加成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
	//排序
	public function order(){
		$guide=M("Guide");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$guide->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."'</script>";
		exit;
	}
	
	//删除
	public function del(){
		$this->delsql();
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$guide=M("Guide");
			$guide->where("id in ({$del})")->delete();
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
	public function edit(){
		$this->modsql();
		$guide=M("Guide");
		$data=$guide->where("id=%d",array($this->id))->find();
		$this->assign("data",$data);
		$this->display();	
	}
	
	public function mod(){
		$this->modsql();
		$image=get_str(trim($_POST["image"]));
		if($image!=""){
			$guide=M("Guide");
			$guide->create();
			$guide->where("id=%d",array($this->id))->save();
			echo "<script>{alert ('修改成功');location.href='".__CONTROLLER__."'}</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
}