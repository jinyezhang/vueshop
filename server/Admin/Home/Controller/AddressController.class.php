<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class AddressController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(47);
	}
	
	public function index(){

		$adds=M("Address");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$adds->alias("adds")->field("adds.id")->join("inner join __USER__ u on adds.uid=u.qid")->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$adds->alias("adds")->field("adds.id,adds.name,adds.cellphone,adds.province,adds.city,adds.address,adds.isdefault,u.cellphone as ucellphone")->join("inner join __USER__ u on adds.uid=u.qid")->order("id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		
		$this->display();
	}
	
	public function del(){
		$this->delsql();
		$ids=@implode(",",$_POST["del"]);
        if($ids!=''){
            $adds=M("Address");
            $adds->where("id in ({$ids})")->delete();
            echo "<script>alert('删除成功！');location.href='".__CONTROLLER__."'</script>";
            exit;
        }else{
            echo "<script>alert('请选择要删除的数据');history.go(-1);</script>";
        }
	}
	
}