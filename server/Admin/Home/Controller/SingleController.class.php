<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class SingleController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->safeColumn($this->id);
	}
	
	public function index(){
		$column=D("Columns");
		$cname=$column->getTitle($this->id);
		$this->assign("cname",$cname);
		
		$data=$column->where("id=%d",array($this->id))->field("c_names,bodys")->find();
		$this->assign("data",$data);
		$this->assign("bodys",htmlspecialchars(stripslashes($data["bodys"])));
		
		$this->display();
	}
	
	public function add(){
		$bodys=get_str($_POST["bodys"],1);
		$column=M("Columns");
		$column->where("id=%d",array($this->id))->save(array("bodys"=>$bodys));
		echo "<script>alert('添加成功！');location.href='".__CONTROLLER__."?id={$this->id}'</script>";
		exit;
	}
	
}