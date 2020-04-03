<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
class PushmsgController extends IsLoginController{
	
	public function __construct(){
		parent::__construct();
		$this->setOtherAllot(43);
	}
	
	public function index(){
		
		$pushmsg=M("Pushmsg");
		$current_page=isset($_REQUEST["page"])?intval($_REQUEST["page"]):1;
		$this->assign("page",$current_page);
		$total=$pushmsg->count();
		$this->assign("total",$total);
		$fpage=new PageOrg($total,$current_page,12);
		$pageInfo=$fpage->getPageInfo();
		$datalist=$pushmsg->field("id,content,times")->order("id desc")->limit($pageInfo["row_offset"],$pageInfo["row_num"])->select();
		if($datalist){
			$this->assign("datalist",$datalist);
			$this->assign("getpage",$fpage->getpage($current_page,__CONTROLLER__."?"));
		}
		
		$this->display();
	}
	
	public function add(){
		$this->addsql();
		$content=get_str(trim($_POST["content"]));
		if($content!=""){
			$pushmsg=M("Pushmsg");
			$pushmsg->create();
			$pushmsg->times=date("Y-m-d H:i:s");
			$pushmsg->add();
			
			import('@.Org.Jpush_send'); 
			$fetion = new \Jpush_send();
			$receive = 'all';//全部 
			//$receive = array('tag'=>array('中国'));//标签 
			//$receive = array('alias'=>array($targetid));//别名 
			$content = strip_tags($content);
			$m_type = 'pm'; 
			$m_time = '86400';        //离线保留时间 
			$res=$fetion->send_pub($receive,$content,$m_type,$m_time);
			echo "<script>alert ('发布成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请填写完整信息！');history.go(-1)</script>";	
		}
	}
	
	//删除
	public function del(){
		$this->delsql();
		$del=@implode(",",$_POST["del"]); 
		if($del!=""){
			$pushmsg=M("Pushmsg");
			$pushmsg->where("id in ({$del})")->delete();
			echo "<script>alert('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请选择要删除的数据');history.go(-1)</script>";		
		}
	}
	
}