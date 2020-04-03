<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class ReviewsmanageController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(46);
	}
	
	public function index(){
		
		$reviserver=M("Reviserver");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$reviserver->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$reviserver->field("id,num,title")->order("num desc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		
		$this->display();
	}
	
	public function add(){
		$this->addsql();
		$title=get_str(trim($_POST["title"]));
		if($title!=""){
			$reviserver=M("Reviserver");
			$reviserver->create();
			$reviserver->add();
			echo "<script>alert ('添加成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
	//排序
	public function order(){
		$reviserver=M("Reviserver");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$reviserver->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."'</script>";
		exit;
	}
	
	//删除
	public function del(){
		$this->delsql();
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$reviserver=M("Reviserver");
			$reviserver->where("id in ({$del})")->delete();
			
			$reviresult=M("Reviresult");
			$reviresult->where("rsid in ({$del})")->delete();
			
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
	public function edit(){
		$reviserver=M("Reviserver");
		$data=$reviserver->where("id=%d",array($this->id))->find();
		$this->assign("data",$data);
		$this->display();	
	}
	
	public function mod(){
		$title=get_str(trim($_POST["title"]));
		if($title!=""){
			$reviserver=M("Reviserver");
			$reviserver->create();
			$reviserver->where("id=%d",array($this->id))->save();
			echo "<script>{alert ('修改成功');location.href='".__CONTROLLER__."'}</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
	//评价设置
	public function setting(){
		$rs=M("Reviewsetting");
		$data=$rs->where("id=%d",array(1))->find();
		$this->assign("data",$data);
		
		if($this->action=='add'){
			$isreview=get_int($_POST["isreview"]);
			$rs->where("id=%d",array(1))->save(array("isreviews"=>$isreview));
			echo "<script>{alert ('设置成功');location.href='".__ACTION__."'}</script>";
			exit;
		}
		
		$this->display();	
	}
	
}