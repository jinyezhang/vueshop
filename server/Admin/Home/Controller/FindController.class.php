<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class FindController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(14);
	}
	
	public function index(){
		
		$find=M("Find");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$find->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$find->field("id,email,times,username")->order("id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?",$strname));
		}
		
		$this->display();
	}
	
	//删除
	public function del(){
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$find=M("Find");
			$find->where("id in ({$del})")->delete();
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
	//查看详情
	public function desc(){
		$find=M("Find");
		$list=$find->join("__USER__ on __FIND__.userid=__USER__.qid")->field("__USER__.username,pwd,__FIND__.email")->find();
		$this->assign("list",$list);
		$this->display();	
	}
	
	public function setmail(){
		$setmail=M("Setmail");
		$data=$setmail->find();
		$this->assign("data",$data);
		$this->display();	
	}
	
	public function modmail(){
		disPost();
		$smtp=get_str($_POST["smtp"]);
		$port=get_int($_POST["port"]);
		$username=get_str($_POST["username"]);
		$pwd=get_str($_POST["pwd"]);
		$smtpuser=get_str($_POST["smtpuser"]);
		$title=get_str($_POST["title"]);
		$content=get_str($_POST["content"]);
		if($smtp!="" && $port!="" && $username!="" && $pwd!="" && $smtpuser!="" && $title!="" && $content!=""){
			$setmail=M("Setmail");
			$setmail->create();
			$setmail->where('id=%d',array(1))->save();
			echo "<script>alert('设置成功！');location.href='".__CONTROLLER__."/setmail'</script>";
			exit;
		}else{
			echo "<script>alert('请填写必填项！');history.go(-1)</script>";	
		}
	}
	
}