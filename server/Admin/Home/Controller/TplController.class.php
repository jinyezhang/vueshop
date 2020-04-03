<?php
namespace Home\Controller;
use Think\Controller;
class TplController extends CommonController{
	
	public function index(){
		$tpl=M("Tpl");
		$tdata=$tpl->select();
		$this->assign("tpl",$tdata);
		
		$this->display();
	}
	
	public function add(){
		$title=get_str(trim($_POST["title"]));
		$fun=get_str($_POST["fun"]);
		if($title!="" && $fun!=""){
			$tpl=M("Tpl");
			$tpl->add(array("title"=>$title,"fun"=>$fun));
			echo "<script>alert('添加成功！');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请填写必填项！');history.go(-1)</script>";
		}
	}
	
	public function edit(){
		if($this->id>0){
			$tpl=M("Tpl");
			$tdata=$tpl->where("id=%d",array($this->id))->field("title,fun")->find();
			$this->assign("tpl",$tdata);
		}
		$this->display();
	}
	
	public function mod(){
		if($this->id>0){
			$title=get_str(trim($_POST["title"]));
			$fun=get_str($_POST["fun"]);
			if($title!="" && $fun!=""){
				$tpl=M("Tpl");
				$data["title"]=$title;
				$data["fun"]=$fun;
				$tpl->where("id=%d",array($this->id))->save($data);
				echo "<script>alert('修改成功！');location.href='".__CONTROLLER__."/edit?id={$this->id}'</script>";
				exit;
			}
		}	
	}
	
	public function del(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$tpl=M("Tpl");
			$tpl->where("id in ({$del})")->delete();
			echo "<script>alert('删除成功！');location.href='".__CONTROLLER__."'</script>";
		}else{
			echo "<script>alert('请选择要删除的数据！');history.go(-1)</script>";	
		}
	}
	
}