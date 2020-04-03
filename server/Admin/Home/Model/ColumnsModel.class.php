<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class ColumnsModel extends Model{
	public $total,$pdo,$prinfo;
	protected $_validate=array(
		array("c_names","require","栏目名称不能为空",0),
		array("fun","require","请选择功能",0)
	);
	
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
	//栏目管理无限级分类
	public function menu($id,$sname){
		//禁添加栏目
		$aid="";
		$exaid=explode(",",$aid);
		//禁删除栏目
		$did="";
		$exdid=explode(",",$did);
		$this->total=0;
		if($sname!=""){
			$arrsname=@explode(" ",$sname);
			$sql="select id,c_names,num,parent_id,fun,parentpath from __COLUMNS__ where parent_id={$id}";
			foreach($arrsname as $asname){
				$sql.=" and c_names like '%".$asname."%'";
			}
			$sql.=" order by num asc,id asc";
		}else {
			$sql="select id,c_names,num,parent_id,fun,parentpath from __COLUMNS__ where parent_id={$id} order by num asc,id asc";	
		}
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute();
		$total=$query->rowCount();
		$csql="select id from __COLUMNS__ where parent_id=?";
		$ChildCount=$this->pdo->prepare(MyModel::parseSql($csql));
			while($v=$query->fetch()){
				$ChildCount->execute(array($v["id"]));
				$counts=$ChildCount->rowCount();
				$pathcount=count(@explode(",",$v["parentpath"]));
				$this->prinfo.="<div class='outerdiv2' onmouseover='overColumn(this)' onmouseout='outColumn(this)' id='c".$v["id"]."'";
				if($counts>0){
					$this->prinfo.=" onclick='oncard(\"".$v["id"]."\")'";
				}
				$this->prinfo.=">
				<div class='movediv'>";
				if($v["parent_id"]==0){
					$this->prinfo.="<div style='width:48%;height:45px;float:left;margin-top:10px;position:relative;' class='back14'><img src='".__ROOT__."/Public/admin/images/minsign.jpg' />&nbsp;&nbsp;".$v["c_names"]."<a id='".$v["id"]."'></a>
					<div class='deskico'><a href='javascript:;' onclick='addDesk(event,\"".$v["id"]."\")'><img src='".__ROOT__."/Public/admin/images/adddesk.png' title='添加到桌面' border='0' /></a></div>
					</div>";
				}else{
					$this->prinfo.="<div style='width:48%;height:45px;float:left;margin-top:10px;position:relative;'>
					<div style='margin-left:".($pathcount*20)."px;'><img src='".__ROOT__."/Public/admin/images/minsign.jpg' />&nbsp;<span class='";
					if($pathcount==2){
						$this->prinfo.="twobg";
					}else if($pathcount==3){
						$this->prinfo.="thrbg";	
					}else{
						$this->prinfo.="fourbg";	
					}
					$this->prinfo.="'>".$pathcount."级</span>&nbsp;&nbsp;".$v["c_names"]."<a id='".$v["id"]."'></a></div>
					<div class='deskico'><a href='javascript:;' onclick='addDesk(event,\"".$v["id"]."\")'><img src='".__ROOT__."/Public/admin/images/adddesk.png' title='添加到桌面' border='0' /></a></div>
					</div>";
				}
				$this->prinfo.="<div style='width:23%;height:45px;float:left;margin-top:10px;'>";
				if ($counts<=0){
					if($v["fun"]=="sa"){
						$this->prinfo.="<a href='".__MODULE__."/Single?id=".$v["id"]."&pid=".$v["parent_id"]."'><img src='".__ROOT__."/Public/admin/images/addcon.png' title='内容管理' border='0' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}else if($v["fun"]=="pro" || $v["fun"]=="card"){
						$this->prinfo.="<a href='".__MODULE__."/GoodsList?id=".$v["id"]."&pid=".$v["parent_id"]."'><img src='".__ROOT__."/Public/admin/images/addcon.png' title='内容管理' border='0' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					}else{
						$this->prinfo.="<a href='".__MODULE__."/NewsList?id=".$v["id"]."&pid=".$v["parent_id"]."'><img src='".__ROOT__."/Public/admin/images/addcon.png' title='内容管理' border='0' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";	
					}
				}
				//禁用添加
				if(in_array($v["id"],$exaid)){
				}else if($pathcount==2){
				}else{
					$this->prinfo.="<a href='".__CONTROLLER__."/add?id=".$v["id"]."&pid=".$v["parent_id"]."'><img src='".__ROOT__."/Public/admin/images/addcol.png' title='栏目添加' border='0' /></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
				$this->prinfo.="<a href='".__CONTROLLER__."/edit?id=".$v["id"]."&pid=".$v["parent_id"]."'><img src='".__ROOT__."/Public/admin/images/editcol.png' title='栏目修改' border='0' /></a></div>
				<div style='width:20%;height:100%;float:left;margin-top:13px;'><a href='javascript:;' onclick=\"ajxorder('uppaixu','".$v["parent_id"]."','".$v["id"]."','".$v["num"]."')\"><img src='".__ROOT__."/Public/admin/images/uparrow.png' title='上移' border='0' /></a>&nbsp;<a href='javascript:;' onclick=\"ajxorder('downpaixu','".$v["parent_id"]."','".$v["id"]."','".$v["num"]."')\"><img src='".__ROOT__."/Public/admin/images/downarrow.png' title='下移' border='0' /></a></div>
				<div style='width:8%;height:45px;float:left;margin-top:10px;'>";
				//禁用删除
				if(in_array($v["id"],$exdid)){
				}else{
					$this->prinfo.="<a href='".__CONTROLLER__."/del?id=".$v["id"]."'' onclick='return confirm(\"确认要删除吗？\")'><img src='".__ROOT__."/Public/admin/images/delcol.png' title='栏目删除' border='0' /></a>";
				}
				$this->prinfo.="</div>
				</div>
				</div>";
				$this->prinfo.="<div class='pdiv' pvar='1'>";
				$this->menu($v["id"],'');
				$this->prinfo.="</div>";
				$this->total++;
		}
		return $this->prinfo;
	}
	
	//上移排序
	public function prevorder($pid,$id,$num){
		$sql="select id,num from __COLUMNS__ where parent_id=? and num<? order by num desc";
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute(array($pid,$num));
		$list=$query->fetch();
		if($list["num"]!=""){
			$sql="update __COLUMNS__ set num=? where parent_id=? and id=?";
			$query=$this->pdo->prepare(MyModel::parseSql($sql));
			$query->execute(array($list["num"],$pid,$id));
			
			$sql="update __COLUMNS__ set num=? where parent_id=? and id=?";
			$query=$this->pdo->prepare(MyModel::parseSql($sql));
			$query->execute(array($num,$pid,$list["id"]));
		}
	}
	
	//下移排序
	public function nextorder($pid,$id,$num){
		$sql="select num,id from __COLUMNS__ where parent_id=? and num>? order by num asc";
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute(array($pid,$num));
		$list=$query->fetch();
		if($list["num"]!=""){
			$sql="update __COLUMNS__ set num=? where parent_id=? and id=?";
			$query=$this->pdo->prepare(MyModel::parseSql($sql));
			$query->execute(array($list["num"],$pid,$id));
			
			$sql="update __COLUMNS__ set num=? where parent_id=? and id=?";
			$query=$this->pdo->prepare(MyModel::parseSql($sql));
			$query->execute(array($num,$pid,$list["id"]));
		}	
	}
	
	//删除栏目
	public function delcolumn($id){
		$ChildCount=$this->pdo->query(MyModel::parseSql("select id from __COLUMNS__ where parent_id=".$id.""));
		$count=$ChildCount->rowcount();
		if($count>0){
			echo "<script>alert ('不能删除,请先删除子目录');history.go(-1)</script>";
		}
		else
		{
			$sql="delete from __COLUMNS__ where id={$id};delete from __ARTICLE__ where parentid={$id};delete from __DESKTOP__ where cid={$id}";
			$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
			$stmt->execute();
			LogModel::setLog("删除栏目","删除");
			echo "<script>alert ('删除成功');location.href='".__CONTROLLER__."'</script>";
			exit;
		}	
	}
	
	//修改栏目无限级分类
	public function modselpcls($pid,$lvl,$cid,$idb,$cnb){
		if($pid==0){
			$this->prinfo= "<select name='columns' id='columns' class='ziti'>";
			$this->prinfo.= "<option value='{$idb}' selected>{$cnb}</option>";
		}
		$query=$this->pdo->query(MyModel::parseSql("select id,c_names,parent_id from __COLUMNS__ where parent_id={$pid} order by num asc,id asc"));
		while($rs=$query->fetch()){
			$this->prinfo.=  "<option value=".$rs[0]." ";
			if(trim(strval($cid))==strval($rs[0])){
				$this->prinfo.= "selected";
			}
			$this->prinfo.= ">";
			if ($lvl==0){
				$this->prinfo.= $rs[1];
			}
			else{
				for($i=0;$i<$lvl;$i++){
					$this->prinfo.= "&nbsp;&nbsp;";
				}
				$this->prinfo.= "∟".$rs[1];
			}
			$this->prinfo.= "</option>";
			$this->modselpcls ($rs[0],$lvl+1,$cid,$idb,$cnb);
		}
		if ($pid==0){
			$this->prinfo.= "</select>";	
		}
		return $this->prinfo;
	}
	
	//栏目修改
	public function modColumn($id,$pid){
		$c_names=get_str(trim($_POST["c_names"]),1);
		$fun=get_str($_POST["fun"]);
		$columns=get_int($_POST["columns"]);
		$tpl=get_str($_POST["tpl"]);
        $image=get_str($_POST["image"]);
		if($c_names!=""){
			if($pid==0 && $pid!=$columns){
				echo "<script>alert('一级栏目不能移动到子目录');history.go(-1)</script>";
			}else{
					//移动到的目录下是否有数据
					$sql="select id from __COLUMNS__ where parent_id=?";
					$query=$this->pdo->prepare(MyModel::parseSql($sql));
					$query->execute(array($columns));
					$total=$query->rowCount();
					if($total<=0){
						echo "<script>alert('请在这个目录下随便添加一个栏目在移动！');history.go(-1)</script>";
					}else{
						//获取新选择的parentpath
						$sql="select parentpath from __COLUMNS__ where id=?";
						$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
						$stmt->execute(array($columns));
						$list=$stmt->fetch();
						//新选择的path
						$newpath=$list["parentpath"];
						$expnewpath=@explode(",",$newpath);
						
						//当前的path
						$sql="select parentpath from __COLUMNS__ where id=?";
						$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
						$stmt->execute(array($id));
						$row=$stmt->fetch();
						$nowpath=$row["parentpath"];
						
						//不能将自己移动到自己的子分类中
						if(in_array($id,$expnewpath) || $id==$columns){
							echo "<script>alert('不能将“".$c_names."”移动到自己的子分类中');history.go(-1)</script>";
						}else{
							//修改parentpath
							if($pid==0){
								$pathval="|0|";
							}else{
								$pathval=$newpath.",|".$columns."|";
							}
							
							
							//将子分类也移动过去
							//原路径
							$srcpath=$nowpath.",|".$id."|";
							//新路径
							$cpath=$newpath.",|".$columns."|,|".$id."|";
							
							if($pid!=0){
								$sql="update __COLUMNS__ set parentpath=replace(parentpath,'".$srcpath."','".$cpath."') where parentpath like ?";
								
								$query=$this->pdo->prepare(MyModel::parseSql($sql));
								$query->execute(array("{$srcpath}%"));
							}
							$sql="update __COLUMNS__ set c_names=?,fun=?,parent_id=?,parentpath=?,tpl=?,image=? where id=?";
							$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
							$stmt->execute(array($c_names,$fun,$columns,$pathval,$tpl,$image,$id));
							LogModel::setLog("修改栏目“".get_str(trim($_POST["c_names"]))."”","修改");
							echo "<script>alert ('修改成功');location.href='".__CONTROLLER__."#id={$id}'</script>";
							exit;
						}
					}
				}
		}
	}
	
	//栏目添加
	public function addColumn($id,$fun,$bodys){
		$columns=get_int($_POST["columns"]);
		$c_names=get_str(trim($_POST["c_names"]),1);
		$fun=get_str($_POST["fun"]);
		$tpl=get_str($_POST["tpl"]);
		$sql="select parentpath from __COLUMNS__ where id={$columns}";
		$query=$this->pdo->query(MyModel::parseSql($sql));
		$rs=$query->fetch();
		$parentpath=$rs["parentpath"];
		$expath=@explode(",",$parentpath);
		if(is_array($expath)){
			foreach($expath as $v){
				$lnkpath.=$v.",";
			}
			$rlnkpath=rtrim($lnkpath,",");
		}
		$parentpath=$rlnkpath.",|".$columns."|";
		if($rs["parentpath"]=="")
		{
			$parentpath="|0|";
		}
		else
		{
			$parentpath=$parentpath;
		}
		if($c_names!=""){
			if($bodys=="" && $bodys==null){
				$sql="insert into __COLUMNS__ (c_names,parent_id,fun,parentpath,num,tpl) values (?,?,?,?,?,?)";
				$stmt=$this->pdo->prepare(MyModel::parseSql($sql));
				$stmt->execute(array($c_names,$columns,$fun,$parentpath,date("YmdHis"),$tpl));
				
				LogModel::setLog("添加栏目“".$c_names."”","添加");
				
				echo ("<script>alert('添加成功');location.href='".__CONTROLLER__."#{$id}'</script>");
				exit;
			}else{
				echo "<script>alert('请清空此栏目下的内容，再添加栏目');history.go(-1)</script>";	
			}
		}	
	}
	
	//栏目标题
	public function getTitle($id){
		$sql="select c_names,parentpath,id,fun from __COLUMNS__ where id={$id}";
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute();
		$rs=$query->fetch();
		$c_names=$rs["c_names"];
		$parentpath=$rs["parentpath"];
		$npart=str_replace("|","",$parentpath);
		$sama=$rs["fun"];
		$sql="select c_names from __COLUMNS__ where id=?";
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		if(strpos($npart,",")>0){
			$parent=@explode(",",$npart);	
			for($i=1;$i<count($parent);$i++){
				$query->execute(array($parent[$i]));
				$rs=$query->fetch();
				$allnames=$allnames.$rs["c_names"]." >> ";
			}
			$allnames=$allnames.$c_names;
		}else{
			$allnames=$c_names;	
		}
		return $allnames;
	}
	
	//文章移动无限级分类
	public function moveselpcls($pid,$lvl,$cid,$id){
		if($pid==0){
			$this->prinfo="<select name='columns' id='columns' disabled='disabled'  onchange=".chr(34)."moveOP('".__CONTROLLER__."/movedata?id=".$id."')".chr(34)." class='ziti'>";
			$this->prinfo.="<option value= selected>转移到...</option>";
		}
		$query=$this->pdo->prepare(MyModel::parseSql("select id,c_names,parent_id from __COLUMNS__ where parent_id={$pid} order by num asc,id asc"));
		$query->execute();
		while($rs=$query->fetch()){
			$this->prinfo.= "<option value=".$rs[0]." ";
			if(trim(strval($cid))==strval($rs[0])){
				$this->prinfo.= "selected";
			}
			$this->prinfo.= ">";
			if ($lvl==0){
				$this->prinfo.= $rs[1];
			}
			else{
				for($i=0;$i<$lvl;$i++){
					$this->prinfo.= "&nbsp;&nbsp;";
				}
				$this->prinfo.= "∟".$rs[1];
			}
			$this->prinfo.= "</option>";
			$this->moveselpcls ($rs[0],$lvl+1,$cid);
		}
		if ($pid==0){
			$this->prinfo.= "</select>";	
		}
		return $this->prinfo;
	}
	
	//防止移动错误数据
	public function moveColumn(){
		$colid=get_int($_POST["columns"]);
		$sql="select id from __COLUMNS__ where parent_id=?";
		$query=$this->pdo->prepare(MyModel::parseSql($sql));
		$query->execute(array($colid));
		if($query->rowcount()>0){
			echo "<script>alert('Sorry,只能在同级别栏目中移动!');history.go(-1)</script>";
			exit;
		}
	}

    //修改产品无限级分类
    public function modgoodslist($pid,$lvl,$cid,$idb,$cnb){
        if($pid==0){
            $this->prinfo= "<select name='columns' id='columns' class='ziti'>";
            $this->prinfo.= "<option value='{$idb}' selected>{$cnb}</option>";
        }
        $query=$this->pdo->query(MyModel::parseSql("select id,c_names,parent_id from __COLUMNS__ where parent_id={$pid} and (fun='pro' or fun='vo') order by num asc,id asc"));
        while($rs=$query->fetch()){
            $this->prinfo.=  "<option value=".$rs[0]." ";
            if(trim(strval($cid))==strval($rs[0])){
                $this->prinfo.= "selected";
            }
            $this->prinfo.= ">";
            if ($lvl==0){
                $this->prinfo.= $rs[1];
            }
            else{
                for($i=0;$i<$lvl;$i++){
                    $this->prinfo.= "&nbsp;&nbsp;";
                }
                $this->prinfo.= "∟".$rs[1];
            }
            $this->prinfo.= "</option>";
            $this->modgoodslist($rs[0],$lvl+1,$cid,$idb,$cnb);
        }
        if ($pid==0){
            $this->prinfo.= "</select>";
        }
        return $this->prinfo;
    }

    //产品移动无限级分类
    public function movegoodslist($pid,$lvl,$cid,$id){
        if($pid==0){
            $this->prinfo="<select name='columns' id='columns' disabled='disabled'  onchange=".chr(34)."moveOP('".__CONTROLLER__."/movedata?id=".$id."')".chr(34)." class='ziti'>";
            $this->prinfo.="<option value= selected>转移到...</option>";
        }
        $query=$this->pdo->prepare(MyModel::parseSql("select id,c_names,parent_id from __COLUMNS__ where parent_id={$pid} and (fun='pro' or fun='vo') order by num asc,id asc"));
        $query->execute();
        while($rs=$query->fetch()){
            $this->prinfo.= "<option value=".$rs[0]." ";
            if(trim(strval($cid))==strval($rs[0])){
                $this->prinfo.= "selected";
            }
            $this->prinfo.= ">";
            if ($lvl==0){
                $this->prinfo.= $rs[1];
            }
            else{
                for($i=0;$i<$lvl;$i++){
                    $this->prinfo.= "&nbsp;&nbsp;";
                }
                $this->prinfo.= "∟".$rs[1];
            }
            $this->prinfo.= "</option>";
            $this->movegoodslist($rs[0],$lvl+1,$cid,$id);
        }
        if ($pid==0){
            $this->prinfo.= "</select>";
        }
        return $this->prinfo;
    }
	
}