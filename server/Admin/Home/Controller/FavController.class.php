<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class FavController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(40);
	}
	
	public function index(){

		$fav=D("Fav");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$fav->getFavTotal();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$fav->getFavPage($pageInfo["row_offset"],$pageInfo["row_num"]);
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		
		$this->display();
	}
	
	public function del(){
		$this->delsql();
		$fav=D("Fav");
		$fav->delFav();
	}
	
}