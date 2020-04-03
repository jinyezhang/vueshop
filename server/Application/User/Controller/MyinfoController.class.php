<?php
namespace User\Controller;
use Think\Controller;
use Common\Controller\IsTokenController;
use Common\Logic\MsgLogic;
class MyinfoController extends IsTokenController {
	
	public function __construct(){
		parent::__construct();
	}
	
	//显示用户信息
	public function userInfo(){
		$uid=get_str($_GET["uid"]);
		$data=$this->getUserInfo($uid);
		if($data['uid']>0){
			MsgLogic::success(200,$data);
		}else{
			MsgLogic::error(201);
		}
	}
	
	//获取用户信息
	private function getUserInfo($id){
		$user=M("User");
		$uinfo=$user->where("qid='%s'",array($id))->find();
		if($uinfo["head"]!=""){
			$head=urlencode(getHost()."/userfiles/head/".$uinfo["head"]);
		}else{
			$head="";	
		}
		$udata=array(
			"uid"=>$uinfo["qid"],
			"cellphone"=>urlencode($uinfo["cellphone"]),
			"nickname"=>urlencode($uinfo["nickname"]),
			"utype"=>urlencode($uinfo["utype"]),
            "gender"=>$uinfo["gender"],
			"head"=>$head,
			"points"=>$uinfo["points"]
		);
		return $udata;
	}
	
	//获取表单元素
	private function getFeld($arr=array()){
		if(count($arr)>0){
			foreach($_POST as $k=>$v){
				if(in_array($k,$arr)){
					return $k;
					break;
				}
			}
		}
	}
	
	//修改会员信息
	public function updateuser(){
		$uid=get_str($_POST["uid"]);
		if($uid!=''){
			$nickname=get_str(trim($_POST["nickname"]));
			$gender=get_int(trim($_POST["gender"]));
			$head=get_str($_POST["head"]);
			if($nickname==""){
				MsgLogic::error(302,urlencode("请输入昵称"));
			}
            if($gender==0){
                MsgLogic::error(302,urlencode("请选择性别"));
            }
			$user=M("User");
			$data["nickname"]=$nickname;
			$data["gender"]=$gender;
			if($head!="") {
                $data["head"] = $head;
            }
			$user->where("qid='%s'",array($uid))->save($data);
			MsgLogic::success(200,urlencode("修改成功！"));
		}else{
			MsgLogic::error(302,urlencode("请登录会员"));
		}
	}
	
	//判定是否有重复的内容
	private function repeatField($tabname,$fieldname,$val){
		$tab=M("{$tabname}");
		$total=$tab->where("{$fieldname}='{$val}'")->count();
		if($total>0){
			return true;
		}else{
			return false;	
		}
	}
	
	//jquery.form上传头像
	public function uploadhead(){
		import("Common.Org.UploadFile");
		$uf=new \UploadFile();
        $head=get_str($_GET["UpFilePath"]);
        $refile=get_str($_GET["filename"]);
		$uf->upfileload($head,'./userfiles/head',array("jpg","gif","png","jpeg"),10*1024*1024,"head","./userfiles/head/{$refile}");
		$imgmsg=json_decode($uf->msg,true);
		if($imgmsg['msg']==1){
			MsgLogic::success(200,$imgmsg);
		}else{
			MsgLogic::error(305,$imgmsg);
		}
	}

    //formdata上传头像
    public function formdatahead(){
        import("Common.Org.UploadFile");
        $uf=new \UploadFile();
        $uf->upfileload("headfile",'./userfiles/head',array("jpg","gif","png","jpeg"),10*1024*1024,"head");
        $imgmsg=json_decode($uf->msg,true);
        if($imgmsg['msg']==1){
            MsgLogic::success(200,$imgmsg);
        }else{
            MsgLogic::error(305,$imgmsg);
        }
    }
	
	//上传系统头像
	public function uploadSystemHead(){
		$head=get_str($_POST["head"]);
		$filename=$head;
		$filepath="./images/head/".$filename;
		$loc=strrpos($filename,".")+1;
		$extfile=strtolower(substr($filename,$loc));
		$extarr=array("jpg","png","jpeg","gif");
		if(file_exists($filepath)){
			if(in_array($extfile,$extarr)){
				$newfilename=uniqueId().".".$extfile;
				$filecontent=file_get_contents($filepath);
				file_put_contents("./userfiles/head/".$newfilename."",$filecontent);
				$json_result='{"msg":"1","msbox":"'.$newfilename.'"}';
				$result=json_decode($json_result,true);
				MsgLogic::success(200,$result);
			}else{
				$json_result='{"msg":"0","msbox":"文件上传类型不正确"}';
				$result=json_decode($json_result,true);
				MsgLogic::success(200,$result);	
			}
		}else{
			$json_result='{"msg":"0","msbox":"头像不存在"}';
			$result=json_decode($json_result,true);
			MsgLogic::success(200,$result);
		}
	}
	
	//头像保存到数据库
	public function savehead(){
		$uid=get_int($_POST["uid"]);
		if($uid>0){
			$head=get_str($_POST["head"]);
			if($head!=""){
				$user=M("User");
				$data["head"]=$head;
				$user->where("qid=%d",array($uid))->save($data);
				MsgLogic::success(200,urlencode("上传成功"));
			}else{
				MsgLogic::error(302,urlencode("请上传头像"));
			}
		}else{
			MsgLogic::error(302,urlencode("没有会员ID"));	
		}
	}

	
	//修改手机号码
	public function updatecellphone(){
		$uid=get_str($_POST["uid"]);
		if($uid!=''){
			$cellphone=get_str(trim($_POST["cellphone"]));
			if($cellphone==''){
				MsgLogic::error(302,urlencode("请输入您的手机号"));
			}
			if(!preg_match("/^1[0-9][0-9]\d{8}$/",$cellphone)){
				MsgLogic::error(302,urlencode("请输入正确的手机号"));
			}
			$user=M("User");
			$udata=$user->field("cellphone,qid")->where("cellphone='%s'",array($cellphone))->find();
			if($udata['cellphone']==$cellphone){
				MsgLogic::error(302,urlencode("您输入的手机号已存在！"));
			}else{
				$user->where("qid='%s'",array($uid))->save(array("cellphone"=>$cellphone));
				MsgLogic::success(200,urlencode("修改成功！"));
			}
		}else{
			MsgLogic::error(302,urlencode("没有会员ID"));	
		}
	}
	
	//修改用户名
	public function updateUsername(){
		$uid=get_str($_POST["uid"]);
		if($uid != ''){
			$username=get_str(trim($_POST["username"]));
			if($username==''){
				MsgLogic::error(302,urlencode("请输入您的用户名"));
			}
			$user=M("User");
			$udata=$user->field("username,qid")->where("username='%s'",array($username))->find();
			if($udata['username']==$username){
				MsgLogic::error(302,urlencode("您输入的用户名已存在！"));
			}else{
				$user->where("qid='%s'",array($uid))->save(array("username"=>$username));
				MsgLogic::success(200,urlencode("修改成功！"));
			}
		}else{
			MsgLogic::error(302,urlencode("没有会员ID"));	
		}
	}

    //修改密码
    public function modPwd(){
        $uid=get_str($_POST["uid"]);
        if($uid != ''){
            $password=get_str($_POST["password"]);
            if($password!='') {
                $user = M("User");
                $data["password"] =md5($password);
                $user->where("qid='%s'", array($uid))->save($data);
                MsgLogic::success(200,urlencode("修改成功！"));
            }else{
                MsgLogic::error(302,urlencode("请输入密码"));
            }
        }else{
            MsgLogic::error(302,urlencode("请登录会员"));
        }
    }
	
}