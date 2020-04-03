<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class AttrModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
    public function getParam($cid){
        $sql="select id,name from __ATTR__ where pid=? and cid=? and type=? order by num desc,id asc";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array(0,$cid,0));

        $psql="select id,name from __ATTR__ where pid=? and cid=? order by num desc,id asc";
        $pstmt=$this->pdo->prepare(MyModel::parseSql($psql));
        while($row=$stmt->fetch()){
            $pstmt->execute(array($row["id"],$cid));
            while($prow=$pstmt->fetch()){
                $param[]=array(
                    "pid"=>$prow["id"],
                    "title"=>urlencode($prow["name"])
                );
            }
            $data[]=array(
                "attrid"=>$row["id"],
                "title"=>urlencode($row["name"]),
                "param"=>$param
            );
            unset($param);
        }
        return $data;
    }
	
}