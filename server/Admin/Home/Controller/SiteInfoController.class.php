<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\LogModel;
class SiteInfoController extends CommonController{
	
	public function index(){
		$setting=M("Setting");
		$getSet=$setting->find();
		$this->assign("getSet",$getSet);
		$this->display();
	}
	
	public function add(){
		$website=get_str($_POST["website"]);
		if($website!=""){
			$setting=M("Setting");
			$setting->create();
			$setting->isandlevel=get_int($_POST['isandlevel']);
			$setting->isioslevel=get_int($_POST['isioslevel']);
			$setting->up_price=floatval($_POST["up_price"])/100;
			$setting->perc_points=floatval($_POST["perc_points"])/100;
			$setting->iosshare=get_int($_POST['iosshare']);
			$setting->isunionpay=get_int($_POST['isunionpay']);
			$setting->isguide=get_int($_POST["isguide"]);
			$setting->where("id=%d",array(1))->save($data);
			//操作日志
			LogModel::setLog("设置app信息","修改");
			echo "<script>alert('设置成功！');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请填写必填项！');history.go(-1)</script>";
		}
	}
	
}