<?php
namespace Home\Controller;
use Think\Controller;
use Home\Model\LogModel;
use Common\Org\ValidationCodeOrg;
class IndexController extends Controller{
    public function index(){
		$action=$_GET["action"];
		if($action=='login'){
			$admin=D("Admin");
			$username=trim($_POST["username"]);
			$password=md5(trim($_POST["password"]));
			$vdcode=trim(strtoupper($_POST["vdcode"]));
			if($username!="" && $password!="" && $vdcode!=""){
				if($vdcode!=$_SESSION["imgcode"]){
					echo "<script>alert('您输入的验证码不正确!');history.go(-1)</script>";
				}else{
					$arr=$admin->query("select a.qid,a.adminname,a.shell,a.gid,a.logintime,a.password,g.groupname from __ADMIN__ as a,__ADMIN_GROUP__ as g where a.gid=g.id and a.adminname='%s' and a.password='%s'",array($username,$password));
					if($arr[0]["adminname"]==$username && $arr[0]["password"]==$password){
						$_SESSION["adminname"]=$arr[0]["adminname"];
						$_SESSION["loginjk"]=1;
						$_SESSION["shell"]=$arr[0]["shell"];
						$_SESSION["adminid"]=$arr[0]["qid"];
						$_SESSION["gid"]=$arr[0]["gid"];
						$_SESSION["logintime"]=$arr[0]["logintime"];
						$_SESSION["groupname"]=$arr[0]["groupname"];
						
						//修改登录时间
						$data["logintime"]=date("Y-m-d H:i:s");
						$admin->where("id=%d",array($arr[0]["qid"]))->save($data);
						
						//操作日志
						LogModel::setLog("登陆成功","登陆");
						
						header("location: ".__MODULE__."/Default/");
						exit;
					}else{
						echo "<script>alert('您输入的用户名或密码不正确');location.href='".__CONTROLLER__."'</script>";
						exit;	
					}
				}
			}
			
		}
		
        $this->display();
    }
	
	//验证码
	public function chkcode(){
		$image = new ValidationCodeOrg(60,26,4);    
		$image->showImage();
		$_SESSION["imgcode"] =strtoupper($image->getCheckCode());
	}
}