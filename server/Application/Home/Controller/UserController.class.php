<?php
namespace Home\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class UserController extends IsTokenController {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function login(){
		$username=get_str(trim($_POST["cellphone"]));
		$vcode=get_str(trim($_POST["vcode"]));
		if($username==''){
			MsgLogic::error(302,urlencode("请输入您的手机号"));
		}
		if(!preg_match("/1[0-9][0-9]\d{8}$/",$username)){
			MsgLogic::error(302,urlencode("请输入正确的手机号"));
		}
		if($username!=""){
			$user=M("User");
			$udata=$user->field("cellphone,qid")->where("cellphone='%s'",array($username))->find();
			if($udata['cellphone']!=$username){
				$qid=uniqueId();
				$data["cellphone"]=$username;
				$data["times"]=date("Y-m-d H:i:s");
				$data["qid"]=$qid;
				$data["nickname"]="会员_".rand(1,9).rand(100,999);
				$data["auth_token"]=md5($qid.$username.rand(10000,99999).time());
				$data["at_expirytime"]=time();
				$user->add($data);
				$uinfo=$this->getUserInfo($qid);

				MsgLogic::success(200,$uinfo);
				
			}else {
				$udata=$user->field("cellphone,qid")->where("cellphone='%s'",array($username))->find();
				if($udata["cellphone"]==$username){
				    $user->where("qid='%s'",array($udata['qid']))->save(array("auth_token"=>md5($udata['qid'].$udata['cellphone'].rand(10000,99999).time()),"at_expirytime"=>time()));
					$uinfo=$this->getUserInfo($udata["qid"]);
					
					MsgLogic::success(200,$uinfo);
				}else{
					MsgLogic::error(303,urlencode("您输入的用户名不存在"));
				}
			}
			
		}else{
			MsgLogic::error(302,urlencode("请输入必填项"));
		}
	}
	
	//获取用户信息
	private function getUserInfo($id){
		$user=M("User");
		$uinfo=$user->where("qid='%s'",array($id))->find();
		$udata=array(
			"uid"=>$uinfo["qid"],
			"nickname"=>urlencode($uinfo["nickname"]),
			"utype"=>urlencode($uinfo["utype"]),
            "auth_token"=>$uinfo["auth_token"]
		);
		return $udata;
	}
	
	//是否注册会员
	public function isreg(){
		$username=get_str(trim($_POST["username"]));
		if($username==''){
			MsgLogic::error(302,urlencode("请输入您的手机号"));
		}
		if(!preg_match("/1[0-9][0-9]\d{8}$/",$username)){
			MsgLogic::error(302,urlencode("请输入正确的手机号"));
		}
		if($username!=""){
			$user=M("User");
			$udata=$user->field("cellphone,qid")->where("cellphone='%s'",array($username))->find();
			if($udata["cellphone"]==$username){
				$res=array("isreg"=>"1","uid"=>$udata["qid"]);
			}else{
				$res=array("isreg"=>"0","uid"=>0);	
			}
			MsgLogic::success(200,$res);
		}
	}
	
	//注册
	public function reg(){
		$username=get_str(trim($_POST["cellphone"]));
		$pwd=get_str(trim($_POST["password"]));
        $vcode=get_str(trim(strtoupper($_POST["vcode"])));
//        if($vcode==''){
//            MsgLogic::error(302,urlencode("请输入验证码"));
//        }
//        if($vcode!=$_SESSION["imgcode"]){
//            MsgLogic::error(302,urlencode("验证码输入错误"));
//        }
		if($username==''){
			MsgLogic::error(302,urlencode("请输入您的手机号"));
		}
		if(!preg_match("/1[0-9][0-9]\d{8}$/",$username)){
			MsgLogic::error(302,urlencode("请输入正确的手机号"));
		}
		if($pwd==''){
			MsgLogic::error(302,urlencode("请您输入密码"));
		}
		if(strlen($pwd)<6){
			MsgLogic::error(302,urlencode("密码必须大于等于6位"));
		}
		if($username!=""){
			$user=M("User");
			$udata=$user->where("cellphone='%s'",$username)->find();
			if($udata["cellphone"]==$username){
				MsgLogic::error(302,urlencode("此手机号已存在！"));
			}else{
				$qid=uniqueId();
				$data["cellphone"]=$username;
				$data["password"]=md5($pwd);
				$data["times"]=date("Y-m-d H:i:s");
				$data["qid"]=$qid;
				$data["nickname"]="会员_".rand(1,9).rand(100,999);
                $data["auth_token"]=md5($qid.$username.rand(10000,99999).time());
                $data["at_expirytime"]=time();
				$user->add($data);
				$uinfo=$this->getUserInfo($qid);
				MsgLogic::success(200,$uinfo);	
			}
		}
		
	}

    //检测验证码
    public function checkVcode(){
        $vcode=get_str(trim(strtoupper($_POST["vcode"])));
        if($vcode!=$_SESSION["imgcode"] || $vcode=='' || $_SESSION["imgcode"]==''){
            MsgLogic::error(201,urlencode("您输入的验证码不正确"));
        }else{
            MsgLogic::success(200,urlencode("您输入的验证码正确"));
        }
    }
	
	//输入密码会员登录
	public function pwdlogin(){
		$username=get_str(trim($_POST["cellphone"]));
		$pwd=get_str(trim($_POST["password"]));

		if($username==''){
			MsgLogic::error(302,urlencode("请输入您的手机号"));
		}
        if(!preg_match("/1[0-9][0-9]\d{8}$/",$username)){
            MsgLogic::error(302,urlencode("请输入正确的手机号"));
        }
		if($pwd==''){
			MsgLogic::error(302,urlencode("请您输入密码"));
		}

		if($username!="" && $pwd!=""){
			$user=M("User");
			$data=$user->field("cellphone")->where("cellphone='%s'",array($username,$username))->find();
			if($data["cellphone"]==$username){
				$udata=$user->field("qid,password,cellphone")->where("cellphone='%s' and password='%s' and password<>''",array($username,md5($pwd)))->find();
				if($udata["cellphone"]==$username && $udata['password']==md5($pwd)){
                    $user->where("qid='%s'",array($udata['qid']))->save(array("auth_token"=>md5($udata['qid'].$udata['cellphone'].rand(10000,99999).time()),"at_expirytime"=>time()));
					$uinfo=$this->getUserInfo($udata['qid']);
					MsgLogic::success(200,$uinfo);
				}else{
					MsgLogic::error(303,urlencode("您输入的用户名或密码不正确"));
				}
			}else{
				MsgLogic::error(303,urlencode("您输入的用户名不存在"));
			}
		}else{
			MsgLogic::error(302,urlencode("请输入必填项"));
		}
	}
	
	//修改密码
	public function modpwd(){
		$uid=get_str($_POST["uid"]);
		$pwd=get_str(trim($_POST["pwd"]));
		if($uid!=''){
			if($pwd==''){
				MsgLogic::error(302,urlencode("请您输入密码"));
			}
			if($uid!='' && $pwd!=""){
				$user=M("User");
				$user->where("qid='%s'",array($uid))->save(array("password"=>md5($pwd)));
				MsgLogic::success(200,urlencode("修改成功！"));
			}
		}else{
			MsgLogic::error(302,urlencode("获取会员ID失败"));	
		}
	}

    //会员页面安全验证
    public function safe(){
        $uid=get_str($_POST["uid"]);
        $auth_token=get_str($_POST["auth_token"]);
        if($uid!="" && $auth_token!=""){
            $user=M("User");
            $udata=$user->field("auth_token,at_expirytime")->where("qid='%s' and auth_token='%s'",array($uid,$auth_token))->find();
            $bExpirytime=time()-$udata['at_expirytime']>=60*30?true:false;
            if($udata['auth_token']==$auth_token && $bExpirytime==false && $udata['auth_token']!=""){
                MsgLogic::success(200,urlencode("有权限访问"));
            }else{
                MsgLogic::error(101,urlencode("没有权限访问"));
            }
        }else{
            MsgLogic::error(302,urlencode("参数传输错误"));
        }
    }

    //安全退出接口
    public function safeout(){
        $uid=get_str($_POST["uid"]);
        if($uid!=""){
            $user=M("User");
            $user->where("qid='%s'",array($uid))->save(array("auth_token"=>''));
            MsgLogic::success(200,urlencode("已安全退出"));
        }else{
            MsgLogic::error(302,urlencode("参数传输错误"));
        }
    }
	
}