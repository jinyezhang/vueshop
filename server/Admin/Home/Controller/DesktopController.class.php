<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
class DesktopController extends IsLoginController{
	
	public function index(){
		$desktop=M();
		$arr=$desktop->query("select d.id,d.num,d.title,d.cid,d.fid,d.pic_path,f.webs from __DESKTOP__ as d left join __FUNSET__ as f on d.fid=f.id where d.uid=%d and (f.isclose='%s' or d.cid<>'') and f.isshow=%d order by d.num asc,d.id desc",array($_SESSION["adminid"],'1',1));
		$this->assign("count",count($arr));
		$this->assign("arr",$arr);
		$this->display();
	}
	
	//桌面排序
	public function desktoporder(){
		$newid=get_str($_POST["newid"]);
		$idarr=@explode(",",$newid);
		$desktop=M("Desktop");
		for($i=0;$i<count($idarr);$i++){
			$desktop->where("id=%d",array($idarr[$i]))->save(array("num"=>$i));
		}
	}
	
	//编辑图片标题
	public function edittext(){
		$tid=get_int($_POST["tid"]);
		$text=get_str($_POST["text"]);
		if($tid>0 && $text!=""){
			$desktop=M("Desktop");
			$desktop->where("id=%d",array($tid))->save(array("title"=>$text));
		}
	}
	
	//更改图片
	public function modpic(){
		$pic=get_str($_GET["pic"]);
		$expic=@explode("/",$pic);
		$spic=$expic[count($expic)-1];
		if($this->id>0 && $spic!=""){
			$desktop=M("Desktop");
			$desktop->where("id=%d",array($this->id))->save(array("pic_path"=>$spic));
		}
	}
	
	//删除桌面图标
	public function delShort(){
		if($this->id>0){
			$desktop=M("Desktop");
			$desktop->where("id=%d",array($this->id))->delete();
		}
	}
	
}