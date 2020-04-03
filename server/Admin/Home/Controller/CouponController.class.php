<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class CouponController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(29);
	}
	
	public function index(){
		
		$this->display();
	}
	
	public function left(){
		$this->display();	
	}
	
	public function manage(){
		$coupon=M("Coupon");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$coupon->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$coupon->field("id,title,num,price,startdate,enddate")->order("num desc,id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__ACTION__."?"));
		}
		$this->display();	
	}
	
	public function add(){
		$this->addsql();
		$title=get_str(trim($_POST["title"]));
		$startdate=get_str(trim($_POST["startdate"]));
		$enddate=get_str(trim($_POST["enddate"]));
		if($title!="" && $startdate!="" && $enddate!=""){
			$coupon=M("Coupon");
			$coupon->create();
			$coupon->add();
			echo "<script>alert ('添加成功');location.href='".__CONTROLLER__."/manage'</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
	//排序
	public function order(){
		$coupon=M("Coupon");
		for ($i=0;$i<count($_POST["num"]);$i++){
			$coupon->where("id=%d",array($_POST["numid"][$i]))->save(array("num"=>$_POST["num"][$i]));
		}
		echo "<script>alert ('排序修改成功');location.href='".__CONTROLLER__."/manage'</script>";
		exit;
	}
	
	//删除
	public function del(){
		$this->delsql();
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$coupon=M("Coupon");
			$coupon->where("id in ({$del})")->delete();
			
			$gcp=M("Getcoupon");
			$gcp->where("cpid in ({$del})")->delete();
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."/manage'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
	public function edit(){
		$this->modsql();
		$coupon=M("Coupon");
		$data=$coupon->where("id=%d",array($this->id))->find();
		$this->assign("data",$data);
		$this->display();	
	}
	
	public function mod(){
		$this->modsql();
		$title=get_str(trim($_POST["title"]));
		$startdate=get_str(trim($_POST["startdate"]));
		$enddate=get_str(trim($_POST["enddate"]));
		if($title!="" && $startdate!="" && $enddate!=""){
			$coupon=M("Coupon");
			$coupon->create();
			$coupon->where("id=%d",array($this->id))->save();
			echo "<script>{alert ('修改成功');location.href='".__CONTROLLER__."/manage'}</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
}