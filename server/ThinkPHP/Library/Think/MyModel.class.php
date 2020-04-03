<?php
namespace Think;
class MyModel{
	static $obj=null;
	static $pdo;
	// 实际数据表名（包含表前缀）
	static $trueTableName    =   '';
	// 数据表前缀
	static $tablePrefix      = null;
	// 模型名称
	static $name             =   '';
    // 数据库名称
    static $dbName           =   '';
	private function __construct(){}
	static function conn(){
		try{
			self::$pdo=new \pdo("mysql:host=".C("DB_HOST").";port=".C("DB_PORT").";dbname=".C("DB_NAME")."","".C("DB_USER")."","".C("DB_PWD")."");
			self::$pdo->query('set names '.C("DB_CHARSET").'');
			return self::$pdo;
		}catch(\PDOException $e){
			echo $e->getMessage();
			exit;
		}
	}
	
	static function getPdo(){
		if(is_null(self::$obj)){
			self::$obj=self::conn();
		}
		return self::$obj;
	}
	
	public function parseSql($sql) {
		self::$tablePrefix=C("DB_PREFIX");
		$sql  =   strtr($sql,array('__TABLE__'=>self::getTableName(),'__PREFIX__'=>self::$tablePrefix));
        $prefix =   self::$tablePrefix;
        $sql    =   preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $sql);
        return $sql;
    }
	
	public function getTableName() {
        if(empty(self::$trueTableName)) {
            $tableName  = !empty(self::$tablePrefix) ? self::$tablePrefix : '';
            if(!empty(self::$tableName)) {
                $tableName .= self::$tableName;
            }else{
                $tableName .= self::parse_name(self::$name);
            }
            self::$trueTableName    =   strtolower($tableName);
        }
        return (!empty(self::$dbName)?self::$dbName.'.':'').self::$trueTableName;
    }
	
	public function parse_name($name, $type=0) {
		if ($type) {
			return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
		} else {
			return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
		}
	}
	
	public function __destruct() {
        if (self::$pdo){
            self::$pdo=null;
        }
    }
	
}

