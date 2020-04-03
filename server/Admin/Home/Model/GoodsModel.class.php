<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;

class GoodsModel extends Model{
    public $pdo;
    public function __construct(){
        parent::__construct();
        $this->pdo=MyModel::getPdo();
    }

    public function getGoodsTotal($id,$kwords){
        $sql="select title,id,num,qid from __GOODS__ where parentid=? and title like ?";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($id,"%{$kwords}%"));
        $total=$stmt->rowCount();
        return $total;
    }

    public function getGoodsPage($offset,$num,$id,$kwords){
        $sql="select title,id,num,qid,dates from __GOODS__ where parentid=? and title like ?";
        $sql.=" order by num desc,id desc limit {$offset},{$num}";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array($id,"%{$kwords}%"));

        $imgsql="select photo from __GOODSIMGS__ where gid=? order by id asc limit 0,1";
        $imgstmt=$this->pdo->prepare(MyModel::parseSql($imgsql));
        while($row=$stmt->fetch()){
            $imgstmt->execute(array($row['qid']));
            $imgrow=$imgstmt->fetch();
            $row['photo']=$imgrow["photo"];
            $data[]=$row;
        }
        return $data;
    }

    public function addGoodsData($id){
        $title=get_str($_POST["title"]);
        $price=floatval($_POST["price"]);
        $freight=floatval($_POST["freight"]);
        $bodys=get_str($_POST["bodys"],1);
        if($title!=""){
            $qid=uniqueId();

            //添加规格
            if(count($_POST["spec_attrid"])>0){
                //添加属性
                $sql="insert into __SPECVAL__ (gid,attrid) values (?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));

                //添加值
                $sqlval="insert into __SPECVAL__ (gid,attrid,value,isparam) values (?,?,?,?)";
                $valstmt=$this->pdo->prepare(MyModel::parseSql($sqlval));

                for($i=0;$i<count($_POST["spec_attrid"]);$i++){
                    $stmt->execute(array($qid,get_int($_POST["spec_attrid"][$i])));

                    //添加值
                    if(count($_POST["spec_val_".$_POST["spec_attrid"][$i].""])>0){
                        for($j=0;$j<count($_POST["spec_val_".$_POST["spec_attrid"][$i].""]);$j++){
                            $specVal=$_POST["spec_val_".$_POST["spec_attrid"][$i].""][$j];
                            if($specVal!='') {
                                $valstmt->execute(array($qid, get_int($_POST["spec_attrid"][$i]), $specVal, 1));
                            }

                        }
                    }

                }

            }

            //单选添加
            if(count($_POST["sattrid"])>0){
                //添加属性
                $asql="insert into __ATTRVAL__ (gid,attrid) values (?,?)";
                $astmt=$this->pdo->prepare(MyModel::parseSql($asql));

                //添加值
                $sql="insert into __ATTRVAL__ (gid,attrid,value,isparam) values (?,?,?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=0;$i<count($_POST["sattrid"]);$i++){
                    if(get_int($_POST["sval"][$i])>0){
                        //添加属性
                        $astmt->execute(array($qid,get_int($_POST["sattrid"][$i])));

                        //添加值
                        $stmt->execute(array($qid,get_int($_POST["sattrid"][$i]),get_int($_POST["sval"][$i]),1));
                        $attrval1.="|".$_POST["sval"][$i]."|,";
                    }
                }
                $rattrval1=rtrim($attrval1,",");
            }

            //多选添加
            if(count($_POST["cattrid"])>0){
                //添加属性
                $asql="insert into __ATTRVAL__ (gid,attrid) values (?,?)";
                $astmt=$this->pdo->prepare(MyModel::parseSql($asql));

                //添加值
                $sql="insert into __ATTRVAL__ (gid,attrid,value,isparam) values (?,?,?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=0;$i<count($_POST["cattrid"]);$i++){
                    //添加属性
                    $astmt->execute(array($qid,get_int($_POST["cattrid"][$i])));

                    //添加值
                    if(count($_POST["cval".$_POST["cattrid"][$i].""])>0){
                        for($j=0;$j<count($_POST["cval".$_POST["cattrid"][$i].""]);$j++){
                            if(get_int($_POST["cval".$_POST["cattrid"][$i].""][$j])>0){
                                $stmt->execute(array($qid,get_int($_POST["cattrid"][$i]),get_int($_POST["cval".$_POST["cattrid"][$i].""][$j]),1));
                            }
                            $cvals.="|".get_int($_POST["cval".$_POST["cattrid"][$i].""][$j])."|,";
                        }
                    }
                }
                $rcvals=rtrim($cvals,",");
            }
            if($rcvals!=""){
                $split=",";
            }else{
                $split="";
            }
            $rattrvals=$rattrval1.$split.$rcvals;

            //添加图片
            $imgnum=get_int($_POST["imgnum"]);

            if($imgnum>=1){
                $sql="insert into __GOODSIMGS__ (gid,photo) values (?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=1;$i<=$imgnum;$i++){
                    if(get_str($_POST["photo{$i}"])!=""){
                        $stmt->execute(array($qid,get_str($_POST["photo{$i}"])));
                    }
                }
            }

            $sql="insert into __GOODS__ (qid,parentid,title,price,freight,goodspara,bodys,dates) values (?,?,?,?,?,?,?,?)";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $stmt->execute(array($qid,$id,$title,$price,$freight,$rattrvals,$bodys,date("Y-m-d")));

            echo "<script>alert('添加成功！');location.href='".__ACTION__."?id={$id}'</script>";
            exit;
        }else{
            echo "<script>alert('请填写必填项！');history.go(-1)</script>";
        }
    }

    //修改
    public function modGoods($cid,$id,$page){
        $title=get_str($_POST["title"]);
        $price=floatval($_POST["price"]);
        $freight=floatval($_POST["freight"]);
        $bodys=get_str($_POST["bodys"],1);
        if($title!=""){
            $spec_attrid=@implode(",",$_POST["spec_attrid"]);
            //删除规格
            $sql="delete from __SPECVAL__ where gid=? and attrid in ({$spec_attrid})";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $stmt->execute(array($id));

            //添加规格
            if(count($_POST["spec_attrid"])>0){
                //添加属性
                $sql="insert into __SPECVAL__ (gid,attrid) values (?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));

                //添加值
                $sqlval="insert into __SPECVAL__ (gid,attrid,value,isparam) values (?,?,?,?)";
                $valstmt=$this->pdo->prepare(MyModel::parseSql($sqlval));

                for($i=0;$i<count($_POST["spec_attrid"]);$i++){
                    $stmt->execute(array($id,get_int($_POST["spec_attrid"][$i])));

                    //添加值
                    if(count($_POST["spec_val_".$_POST["spec_attrid"][$i].""])>0){
                        for($j=0;$j<count($_POST["spec_val_".$_POST["spec_attrid"][$i].""]);$j++){
                            $specVal=$_POST["spec_val_".$_POST["spec_attrid"][$i].""][$j];
                            if($specVal!='') {
                                $valstmt->execute(array($id, get_int($_POST["spec_attrid"][$i]), $specVal, 1));
                            }

                        }
                    }

                }

            }

            //修改属性搜索
            $attrid=@implode(",",$_POST["attrid"]);
            $sql="delete from __ATTRVAL__ where gid=? and attrid in ({$attrid})";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $stmt->execute(array($id));
            //单选修改
            if(count($_POST["sattrid"])>0){

                //添加属性
                $asql="insert into __ATTRVAL__ (gid,attrid) values (?,?)";
                $astmt=$this->pdo->prepare(MyModel::parseSql($asql));


                $sql="insert into __ATTRVAL__ (gid,attrid,value,isparam) values (?,?,?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=0;$i<count($_POST["sattrid"]);$i++){
                    if(get_int($_POST["sval"][$i])>0){
                        //添加属性
                        $astmt->execute(array($id,get_int($_POST["sattrid"][$i])));

                       $stmt->execute(array($id,get_int($_POST["sattrid"][$i]),get_int($_POST["sval"][$i]),1));
                        $attrval1.="|".$_POST["sval"][$i]."|,";
                    }
                }
                $rattrval1=rtrim($attrval1,",");
            }

            //多选修改
            if(count($_POST["cattrid"])>0){

                //添加属性
                $asql="insert into __ATTRVAL__ (gid,attrid) values (?,?)";
                $astmt=$this->pdo->prepare(MyModel::parseSql($asql));

                $sql="insert into __ATTRVAL__ (gid,attrid,value,isparam) values (?,?,?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=0;$i<count($_POST["cattrid"]);$i++){
                    //添加属性
                    $astmt->execute(array($id,get_int($_POST["cattrid"][$i])));


                    if(count($_POST["cval".$_POST["cattrid"][$i].""])>0){
                        for($j=0;$j<count($_POST["cval".$_POST["cattrid"][$i].""]);$j++){
                            if(get_int($_POST["cval".$_POST["cattrid"][$i].""][$j])>0){
                                $stmt->execute(array($id,get_int($_POST["cattrid"][$i]),get_int($_POST["cval".$_POST["cattrid"][$i].""][$j]),1));
                            }
                            $cvals.="|".get_int($_POST["cval".$_POST["cattrid"][$i].""][$j])."|,";
                        }
                    }
                }
                $rcvals=rtrim($cvals,",");
            }
            if($rcvals!=""){
                $split=",";
            }else{
                $split="";
            }
            $rattrvals=$rattrval1.$split.$rcvals;

            //修改图片
            if(count($_POST["oldimgid"])>0){
                $sql="update __GOODSIMGS__ set photo=? where id=?";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=0;$i<count($_POST["oldimgid"]);$i++){
                    $stmt->execute(array(get_str($_POST["oldimage".($i+1).""]),get_int($_POST["oldimgid"][$i])));
                }
            }

            //添加图片
            $imgnum=get_int($_POST["imgnum"]);
            if($imgnum>=1){
                $sql="insert into __GOODSIMGS__ (gid,photo) values (?,?)";
                $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
                for($i=1;$i<=$imgnum;$i++){
                    if(get_str($_POST["photo{$i}"])!=""){
                        $stmt->execute(array($id,get_str($_POST["photo{$i}"])));
                    }
                }
            }

            $sql="update __GOODS__ set title=?,price=? ,freight=?,goodspara=?,bodys=? where qid=?";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $stmt->execute(array($title,$price,$freight,$rattrvals,$bodys,$id));
            echo "<script>alert ('修改成功');location.href='".__ACTION__."?id={$id}&page={$page}&cid={$cid}'</script>";
            exit;
        }else{
            echo "<script>alert('请填写必填项！');history.go(-1)</script>";
        }
    }

    public function delgoods($id){
        $ids=implode(",",$_POST["del"]);
        if($ids!=""){
            $sql="delete from __GOODS__ where qid in ({$ids});delete from __ATTRVAL__ where gid in ({$ids});delete from __GOODSIMGS__ where gid in ({$ids});delete from __SPECVAL__ where gid in ({$ids});delete from __REVIEWS__ where gid in ({$ids});delete from __REVIEWSULT__ where gid in ({$ids});delete from __FAV__ where gid in ({$ids});";
            $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
            $stmt->execute();
            echo "<script>alert('删除成功！');location.href='".__CONTROLLER__."/manage?id={$id}'</script>";
        }else{
            echo "<script>alert('请选择数据');history.go(-1)</script>";
        }
    }

}