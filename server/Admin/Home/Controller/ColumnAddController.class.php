<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\LogModel;
class ColumnAddController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(9);
	}
	
	public function index(){
		$this->display();
	}
	
	public function add(){
		$this->addsql();
		$column=D("Columns");
		if(!$column->create()){
			echo "<script>alert('".$column->getError()."');history.go(-1)</script>";
		}else{
			$column->parent_id=0;
			$column->parentpath="|0|";
			$column->num=date("YmdHis");
			$column->tpl=get_str($_POST["tpl"]);
			$column->add();
			LogModel::setLog("添加栏目“".get_str(trim($_POST["c_names"]))."”","添加");
			echo "<script>alert('添加成功！');location.href='".__CONTROLLER__."'</script>";
			exit;	
		}
	}
	
	//显示模板
	public function showtpl(){
		$fun=get_str($_POST["fun"]);
			$tpl=M("Tpl");
			$data=$tpl->where("fun='%s'",array($fun))->field("id,title")->select();
			if($data){
				$html="[";
				foreach($data as $v){
					$rhtml.="{\"id\":\"".$v["id"]."\",\"title\":\"".$v["title"]."\"},";
				}
				$html.=rtrim($rhtml,",");
				$html.="]";
			}else{
				$html="[{\"id\":\"0\"}]";
			}
			echo $html;
		}
	
}