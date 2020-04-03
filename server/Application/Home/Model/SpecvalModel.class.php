<?php
namespace Home\Model;
use Think\Model;
use Think\MyModel;
class SpecvalModel extends Model{
	protected $pdo;
	public function __construct(){
		parent::__construct();
		$this->pdo=MyModel::getPdo();
	}
	
    public function getSpec($gid){
        $sql="select s.attrid,a.name from __SPECVAL__ s inner join __ATTR__ a on s.attrid=a.id where s.isparam=? and s.gid=? order by a.num desc,a.id asc";
        $stmt=$this->pdo->prepare(MyModel::parseSql($sql));
        $stmt->execute(array(0,$gid));

        $vsql="select value,id from __SPECVAL__ where attrid=? and isparam=? and gid=? order by id asc";
        $vstmt=$this->pdo->prepare(MyModel::parseSql($vsql));
        while($row=$stmt->fetch()){
            $vstmt->execute(array($row["attrid"],1,$gid));
            while($vrow=$vstmt->fetch()){
                $values[]=array(
                    "vid"=>$vrow["id"],
                    "value"=>urlencode($vrow["value"])
                );
            }
            $data[]=array(
                "attrid"=>$row["attrid"],
                "title"=>urlencode($row["name"]),
                "values"=>$values
            );
            unset($values);
        }

        return $data;
    }
	
}