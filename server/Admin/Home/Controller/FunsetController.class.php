<?php
namespace Home\Controller;
use Think\Controller;
class FunsetController extends CommonController{
	
	public function index(){
		$funset=M("Funset");
		$arr=$funset->order("id asc")->field("id,title,webs,isclose,isshow")->select();
		$this->assign("arr",$arr);
		$this->display();
	}
	
	public function add(){
		$title=get_str($_POST["title"]);
		$webs=get_str($_POST["webs"]);
		if($title!="" && $webs!=""){
			$funset=M("Funset");
			$fdata["title"]=$title;
			$fdata["webs"]=$webs;
			$fdata["isclose"]='1';
			$lastid=$funset->add($fdata);
			
			$desktop=M("Desktop");
			//获取会员id
			$arr=$desktop->group("uid")->field("uid")->select();
			
			//添加到桌面表里
			for($i=0;$i<count($arr);$i++){
				$datalist[]=array(
					"title"=>$title,
					"pic_path"=>mt_rand(1,28).".png",
					"uid"=>$arr[$i]["uid"],
					"fid"=>$lastid
				);
			}
			if(count($datalist)>0){
				$desktop->addAll($datalist);
			}
			echo "<script>alert('添加成功！');location.href='".__CONTROLLER__."'</script>";
			exit;
		}
	}
	
	public function del(){
		$ids=@implode(",",$_POST["del"]);
		if($ids!=""){
			$m=M();
			$m->execute("delete from __FUNSET__ where id in ({$ids})");
			$m->execute("delete from __DESKTOP__ where fid in ({$ids})");
			echo "<script>alert('删除成功！');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择删除的数据！');history.go(-1)</script>";	
		}
	}
	
	public function close(){
		$funset=M("Funset");
		if($this->id>0){
			$funset->where("id=%d",array($this->id))->save(array("isclose"=>'0'));
			echo "<script>location.href='".__CONTROLLER__."';window.opener.location.reload();window.opener.top.frames['main'].document.location.reload();</script>";
			exit;
		}
	}
	
	public function open(){
		$funset=M("Funset");
		if($this->id>0){
			$funset->where("id=%d",array($this->id))->save(array("isclose"=>'1'));
			echo "<script>location.href='".__CONTROLLER__."';window.opener.location.reload();window.opener.top.frames['main'].document.location.reload();</script>";
			exit;
		}
	}
	
	public function isdesktop(){
		$val=get_int($_GET["val"]);
		$funset=M("Funset");
		if($this->id>0){
			$funset->where("id=%d",array($this->id))->save(array("isshow"=>$val));
			echo "<script>location.href='".__CONTROLLER__."';window.opener.location.reload();window.opener.top.frames['main'].document.location.reload();</script>";
			exit;
		}
	}
	
	public function edit(){
		if($this->id>0){
			$funset=M("Funset");
			$data=$funset->where("id=%d",array($this->id))->field("title,webs")->find();
			$this->assign("data",$data);
		}
		$this->display();	
	}
	
	public function mod(){
		if($this->id>0){
			$title=get_str($_POST["title"]);
			$webs=get_str($_POST["webs"]);
			if($title!="" && $webs!=""){
				$funset=M("Funset");
				$data["title"]=$title;
				$data["webs"]=$webs;
				$funset->where("id=%d",array($this->id))->save($data);
				echo "<script>alert('修改成功！');location.href='".__CONTROLLER__."/edit?id={$this->id}'</script>";
				exit;
			}
		}	
	}
	
}