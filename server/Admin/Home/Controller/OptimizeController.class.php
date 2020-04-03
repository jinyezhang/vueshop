<?php
namespace Home\Controller;
use Think\Controller;
use Think\MyModel;
class OptimizeController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(16);
	}
	
	public function index(){
		if($this->action=='start'){
			$pdo=MyModel::getPdo();
			$stmt=$pdo->prepare("show tables");
			$stmt->execute();
			while($list=$stmt->fetch()){
				$optimization=$pdo->query("OPTIMIZE TABLE `".$list[0]."`");
				if($optimization){
					$printf.="".$list[0]."-------><font color='00ff00'>优化成功</font><br />";
					
				}
			}
			$this->assign("printf",$printf);
		}
		$this->display();
	}
	
}