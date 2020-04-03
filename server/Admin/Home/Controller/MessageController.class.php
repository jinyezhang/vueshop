<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class MessageController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(10);
	}
	
	public function index(){
		
		$msg=M("Message");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$msg->alias("msg")->field("msg.id")->join("inner join __USER__ u on msg.uid=u.qid")->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$msg->alias("msg")->field("msg.id,msg.actname,msg.times,u.username,u.name,msg.content")->join("inner join __USER__ u on msg.uid=u.qid")->order("msg.id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			foreach($datalist as $v){
				$dataarr[]=array(
					"id"=>$v["id"],
					"actname"=>$v["actname"],
					"times"=>$v["times"],
					"username"=>$v["username"],
					"name"=>$v["name"],
					"content"=>faceDecode($v["content"])
				);
			}
			$this->assign("datalist",$dataarr);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		
		$this->display();
	}
	
	public function del(){
		$this->delsql();
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$msg=M("Message");
			$msg->where("id in ({$del})")->delete();
			
			$rmsg=M("Msgreply");
			$rmsg->where("msgid in ({$del})")->delete();
			echo "<script>alert ('删除成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中要删除的留言'); history.go(-1);</script>";	
		}
		
	}
	
	public function check(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$msg=M("Message");
			$msg->where("id in ({$del})")->save(array("qx"=>'1'));
			echo "<script>alert ('审核成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中数据'); history.go(-1);</script>";	
		}
	}
	
	public function uncheck(){
		$del=@implode(",",$_POST["del"]);
		if($del!=""){
			$msg=M("Message");
			$msg->where("id in ({$del})")->save(array("qx"=>'0'));
			echo "<script>alert ('审核成功'); location.href='".__CONTROLLER__."';</script>";
			exit;
		}else{
			echo "<script>alert ('请选中数据'); history.go(-1);</script>";	
		}
	}
	
	//查看留言
	public function msginfo(){
		$msg=M("Message");
		$msgdata=$msg->alias("msg")->field("msg.id,msg.actname,u.username,u.name,msg.times,msg.content")->join("inner join __USER__ u on msg.uid=u.qid")->where("msg.id=%d",array($this->id))->find();
		$this->assign("msgdata",$msgdata);
		
		//回复的信息
		$rmsg=M("Msgreply");
		if($_SESSION["gid"]==1){
			$rmsgdata=$rmsg->alias("rmsg")->field("rmsg.id,rmsg.content,rmsg.times,a.adminname")->join("inner join __ADMIN__ a on rmsg.adminid=a.qid")->where("rmsg.msgid=%d",array($this->id))->select();
		}else{
			$rmsgdata=$rmsg->alias("rmsg")->field("rmsg.id,rmsg.content,rmsg.times,a.adminname")->join("inner join __ADMIN__ a on rmsg.adminid=a.qid")->where("rmsg.msgid=%d and rmsg.adminid=%d",array($this->id,$_SESSION["adminid"]))->select();
		}
		$this->assign("rmsgdata",$rmsgdata);
		
		if($this->action=='reply'){
			$content=get_str(delwrap($_POST["content"]));
			$rmsg=M("Msgreply");
			$rmsg->create();
			$rmsg->msgid=$this->id;
			$rmsg->adminid=$_SESSION["adminid"];
			$rmsg->content=$content;
			$rmsg->times=date("Y-m-d H:i:s");
			$rmsg->add();
			echo "<script>location.href='".__ACTION__."?id={$this->id}&page={$this->page}'</script>";	
			exit;
		}
		
		$this->display();	
	}
	
}