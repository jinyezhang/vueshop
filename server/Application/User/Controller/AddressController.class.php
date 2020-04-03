<?php
namespace User\Controller;
use Think\Controller;
use Common\Logic\MsgLogic;
class AddressController extends Controller {
    public function index(){
        $uid=get_str($_GET["uid"]);
        if($uid!=''){
            $adds=M("Address");
            $data=$adds->where("uid='%s'",array($uid))->order("id desc")->select();
            if(count($data)>0){
                foreach($data as $row) {
                    $datalist[] = array(
                        "aid" => $row["id"],
                        "name" => urlencode($row["name"]),
                        "cellphone" => $row["cellphone"],
                        "province" => urlencode($row["province"]),
                        "city" => urlencode($row["city"]),
                        "area"=>urlencode($row["area"]),
                        "address" => urlencode($row["address"]),
                        "isdefault" => $row["isdefault"]
                    );
                }
                MsgLogic::success(200,$datalist);
            }else{
                MsgLogic::error(201);
            }
        }else{
            MsgLogic::error(303,urlencode("请登录会员"));
        }
    }

    public function add(){
        $uid=get_str($_POST["uid"]);
        if($uid!=''){
            $name=get_str($_POST["name"]);
            $cellphone=get_str($_POST["cellphone"]);
            $province=get_str($_POST["province"]);
            $city=get_str($_POST["city"]);
            $area=get_str($_POST["area"]);
            $address=get_str($_POST["address"]);
            $isdefault=get_int($_POST["isdefault"]);
            if($name==''){
                MsgLogic::error(303,urlencode("请输入收货人姓名"));
            }
            if($cellphone==''){
                MsgLogic::error(303,urlencode("请输入收货人手机号"));
            }
            if(!preg_match("/1[3458]{1}\d{9}$/",$cellphone)){
                MsgLogic::error(303,urlencode("请输入正确的手机号"));
            }
            if($province=='' || $city==''){
                MsgLogic::error(303,urlencode("请输入所在地区"));
            }
            if($address==''){
                MsgLogic::error(303,urlencode("请输入详细地址"));
            }
            $adds=M("Address");

            if($isdefault==1){
                $adds->where("uid='%s'",array($uid))->save(array("isdefault"=>0));
            }

            $data["uid"]=$uid;
            $data["name"]=$name;
            $data["cellphone"]=$cellphone;
            $data["province"]=$province;
            $data["city"]=$city;
            $data["area"]=$area;
            $data["address"]=$address;
            $data["isdefault"]=$isdefault;
            $lastid=$adds->add($data);

            $row=$adds->where("uid='%s' and id=%d",array($uid,$lastid))->find();
            $ndata=array(
                "aid"=>$row["id"],
                "name"=>urlencode($row["name"]),
                "province"=>urlencode($row["province"]),
                "city"=>urlencode($row["city"]),
                "area"=>urlencode($row["area"]),
                "address"=>urlencode($row["address"]),
                "cellphone"=>$row["cellphone"]
            );

            MsgLogic::success(200,$ndata);
        }else{
            MsgLogic::error(303,urlencode("请登录会员"));
        }
    }

    //删除
    public function del(){
        $uid=get_str($_GET["uid"]);
        $aid=get_int($_GET["aid"]);
        if($uid!='' && $aid>0) {
            $adds=M("Address");
            $adds->where("uid='%s' and id=%d",array($uid,$aid))->delete();
            MsgLogic::success(200,urlencode("删除成功！"));
        }else{
            MsgLogic::error(303,urlencode("获取失败"));
        }
    }

    //修改
    public function mod(){
        $uid=get_str($_POST["uid"]);
        $aid=get_int($_POST["aid"]);
        if($uid!='' && $aid>0) {
            $name=get_str($_POST["name"]);
            $cellphone=get_str($_POST["cellphone"]);
            $province=get_str($_POST["province"]);
            $city=get_str($_POST["city"]);
            $area=get_str($_POST["area"]);
            $address=get_str($_POST["address"]);
            $isdefault=get_int($_POST["isdefault"]);
            if($name==''){
                MsgLogic::error(303,urlencode("请输入收货人姓名"));
            }
            if($cellphone==''){
                MsgLogic::error(303,urlencode("请输入收货人手机号"));
            }
            if(!preg_match("/1[3458]{1}\d{9}$/",$cellphone)){
                MsgLogic::error(303,urlencode("请输入正确的手机号"));
            }
            if($province=='' || $city==''){
                MsgLogic::error(303,urlencode("请输入所在地区"));
            }
            if($address==''){
                MsgLogic::error(303,urlencode("请输入详细地址"));
            }
            $adds=M("Address");

            if($isdefault==1){
                $adds->where("uid='%s'",array($uid))->save(array("isdefault"=>0));
            }

            $data["uid"]=$uid;
            $data["name"]=$name;
            $data["cellphone"]=$cellphone;
            $data["province"]=$province;
            $data["city"]=$city;
            $data["area"]=$area;
            $data["address"]=$address;
            $data["isdefault"]=$isdefault;
            $adds->where("uid='%s' and id=%d",array($uid,$aid))->save($data);
            MsgLogic::success(200,urlencode("修改成功！"));
        }else{
            MsgLogic::error(303,urlencode("获取失败"));
        }
    }

    //默认地址
    public function defaultAddress(){
        $uid=get_str($_GET["uid"]);
        if($uid!=''){
            $adds=M("Address");
            $row=$adds->where("uid='%s' and isdefault=%d",array($uid,1))->find();
            if(count($row)>0){
                $data=array(
                    "aid"=>$row["id"],
                    "name"=>urlencode($row["name"]),
                    "cellphone"=>$row["cellphone"],
                    "province"=>urlencode($row["province"]),
                    "city"=>urlencode($row["city"]),
                    "area"=>urlencode($row["area"]),
                    "address"=>urlencode($row["address"])
                );
                MsgLogic::success(200,$data);
            }else{
                MsgLogic::success(201);
            }
        }else{
            MsgLogic::error(303,urlencode("请登录会员"));
        }
    }

    //获取收货地址详情
    public function info(){
        $uid=get_str($_GET["uid"]);
        $aid=get_int($_GET["aid"]);
        if($uid!='' && $aid>0){
            $adds=M("Address");
            $row=$adds->where("uid='%s' and id=%d",array($uid,$aid))->find();
            if(count($row)>0){
                $data=array(
                    "aid"=>$row["id"],
                    "name"=>urlencode($row["name"]),
                    "cellphone"=>$row["cellphone"],
                    "province"=>urlencode($row["province"]),
                    "city"=>urlencode($row["city"]),
                    "area"=>urlencode($row["area"]),
                    "address"=>urlencode($row["address"]),
                    "isdefault"=>$row["isdefault"]
                );
                MsgLogic::success(200,$data);
            }else{
                MsgLogic::success(201);
            }
        }else{
            MsgLogic::error(303,urlencode("获取失败"));
        }
    }

}