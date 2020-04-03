<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
/**
 * ThinkPHP Model妯″瀷绫�
 * 瀹炵幇浜哋RM鍜孉ctiveRecords妯″紡
 */
class Model {
    // 鎿嶄綔鐘舵��
    const MODEL_INSERT          =   1;      //  鎻掑叆妯″瀷鏁版嵁
    const MODEL_UPDATE          =   2;      //  鏇存柊妯″瀷鏁版嵁
    const MODEL_BOTH            =   3;      //  鍖呭惈涓婇潰涓ょ鏂瑰紡
    const MUST_VALIDATE         =   1;      // 蹇呴』楠岃瘉
    const EXISTS_VALIDATE       =   0;      // 琛ㄥ崟瀛樺湪瀛楁鍒欓獙璇�
    const VALUE_VALIDATE        =   2;      // 琛ㄥ崟鍊间笉涓虹┖鍒欓獙璇�

    // 褰撳墠鏁版嵁搴撴搷浣滃璞�
    protected $db               =   null;
	// 鏁版嵁搴撳璞℃睜
	private   $_db				=	array();
    // 涓婚敭鍚嶇О
    protected $pk               =   'id';
    // 涓婚敭鏄惁鑷姩澧為暱
    protected $autoinc          =   false;    
    // 鏁版嵁琛ㄥ墠缂�
    protected $tablePrefix      =   null;
    // 妯″瀷鍚嶇О
    protected $name             =   '';
    // 鏁版嵁搴撳悕绉�
    protected $dbName           =   '';
    //鏁版嵁搴撻厤缃�
    protected $connection       =   '';
    // 鏁版嵁琛ㄥ悕锛堜笉鍖呭惈琛ㄥ墠缂�锛�
    protected $tableName        =   '';
    // 瀹為檯鏁版嵁琛ㄥ悕锛堝寘鍚〃鍓嶇紑锛�
    protected $trueTableName    =   '';
    // 鏈�杩戦敊璇俊鎭�
    protected $error            =   '';
    // 瀛楁淇℃伅
    protected $fields           =   array();
    // 鏁版嵁淇℃伅
    protected $data             =   array();
    // 鏌ヨ琛ㄨ揪寮忓弬鏁�
    protected $options          =   array();
    protected $_validate        =   array();  // 鑷姩楠岃瘉瀹氫箟
    protected $_auto            =   array();  // 鑷姩瀹屾垚瀹氫箟
    protected $_map             =   array();  // 瀛楁鏄犲皠瀹氫箟
    protected $_scope           =   array();  // 鍛藉悕鑼冨洿瀹氫箟
    // 鏄惁鑷姩妫�娴嬫暟鎹〃瀛楁淇℃伅
    protected $autoCheckFields  =   true;
    // 鏄惁鎵瑰鐞嗛獙璇�
    protected $patchValidate    =   false;
    // 閾炬搷浣滄柟娉曞垪琛�
    protected $methods          =   array('strict','order','alias','having','group','lock','distinct','auto','filter','validate','result','token','index','force');

    /**
     * 鏋舵瀯鍑芥暟
     * 鍙栧緱DB绫荤殑瀹炰緥瀵硅薄 瀛楁妫�鏌�
     * @access public
     * @param string $name 妯″瀷鍚嶇О
     * @param string $tablePrefix 琛ㄥ墠缂�
     * @param mixed $connection 鏁版嵁搴撹繛鎺ヤ俊鎭�
     */
    public function __construct($name='',$tablePrefix='',$connection='') {
        // 妯″瀷鍒濆鍖�
        $this->_initialize();
        // 鑾峰彇妯″瀷鍚嶇О
        if(!empty($name)) {
            if(strpos($name,'.')) { // 鏀寔 鏁版嵁搴撳悕.妯″瀷鍚嶇殑 瀹氫箟
                list($this->dbName,$this->name) = explode('.',$name);
            }else{
                $this->name   =  $name;
            }
        }elseif(empty($this->name)){
            $this->name =   $this->getModelName();
        }
        // 璁剧疆琛ㄥ墠缂�
        if(is_null($tablePrefix)) {// 鍓嶇紑涓篘ull琛ㄧず娌℃湁鍓嶇紑
            $this->tablePrefix = '';
        }elseif('' != $tablePrefix) {
            $this->tablePrefix = $tablePrefix;
        }elseif(!isset($this->tablePrefix)){
            $this->tablePrefix = C('DB_PREFIX');
        }

        // 鏁版嵁搴撳垵濮嬪寲鎿嶄綔
        // 鑾峰彇鏁版嵁搴撴搷浣滃璞�
        // 褰撳墠妯″瀷鏈夌嫭绔嬬殑鏁版嵁搴撹繛鎺ヤ俊鎭�
        $this->db(0,empty($this->connection)?$connection:$this->connection,true);
    }

    /**
     * 鑷姩妫�娴嬫暟鎹〃淇℃伅
     * @access protected
     * @return void
     */
    protected function _checkTableInfo() {
        // 濡傛灉涓嶆槸Model绫� 鑷姩璁板綍鏁版嵁琛ㄤ俊鎭�
        // 鍙湪绗竴娆℃墽琛岃褰�
        if(empty($this->fields)) {
            // 濡傛灉鏁版嵁琛ㄥ瓧娈垫病鏈夊畾涔夊垯鑷姩鑾峰彇
            if(C('DB_FIELDS_CACHE')) {
                $db   =  $this->dbName?:C('DB_NAME');
                $fields = F('_fields/'.strtolower($db.'.'.$this->tablePrefix.$this->name));
                if($fields) {
                    $this->fields   =   $fields;
                    if(!empty($fields['_pk'])){
                        $this->pk       =   $fields['_pk'];
                    }
                    return ;
                }
            }
            // 姣忔閮戒細璇诲彇鏁版嵁琛ㄤ俊鎭�
            $this->flush();
        }
    }

    /**
     * 鑾峰彇瀛楁淇℃伅骞剁紦瀛�
     * @access public
     * @return void
     */
    public function flush() {
        // 缂撳瓨涓嶅瓨鍦ㄥ垯鏌ヨ鏁版嵁琛ㄤ俊鎭�
        $this->db->setModel($this->name);
        $fields =   $this->db->getFields($this->getTableName());
        if(!$fields) { // 鏃犳硶鑾峰彇瀛楁淇℃伅
            return false;
        }
        $this->fields   =   array_keys($fields);
        unset($this->fields['_pk']);
        foreach ($fields as $key=>$val){
            // 璁板綍瀛楁绫诲瀷
            $type[$key]     =   $val['type'];
            if($val['primary']) {
                  // 澧炲姞澶嶅悎涓婚敭鏀寔
                if (isset($this->fields['_pk']) && $this->fields['_pk'] != null) {
                    if (is_string($this->fields['_pk'])) {
                        $this->pk   =   array($this->fields['_pk']);
                        $this->fields['_pk']   =   $this->pk;
                    }
                    $this->pk[]   =   $key;
                    $this->fields['_pk'][]   =   $key;
                } else {
                    $this->pk   =   $key;
                    $this->fields['_pk']   =   $key;
                }
                if($val['autoinc']) $this->autoinc   =   true;
            }
        }
        // 璁板綍瀛楁绫诲瀷淇℃伅
        $this->fields['_type'] =  $type;

        // 2008-3-7 澧炲姞缂撳瓨寮�鍏虫帶鍒�
        if(C('DB_FIELDS_CACHE')){
            // 姘镐箙缂撳瓨鏁版嵁琛ㄤ俊鎭�
            $db   =  $this->dbName?:C('DB_NAME');
            F('_fields/'.strtolower($db.'.'.$this->tablePrefix.$this->name),$this->fields);
        }
    }

    /**
     * 璁剧疆鏁版嵁瀵硅薄鐨勫��
     * @access public
     * @param string $name 鍚嶇О
     * @param mixed $value 鍊�
     * @return void
     */
    public function __set($name,$value) {
        // 璁剧疆鏁版嵁瀵硅薄灞炴��
        $this->data[$name]  =   $value;
    }

    /**
     * 鑾峰彇鏁版嵁瀵硅薄鐨勫��
     * @access public
     * @param string $name 鍚嶇О
     * @return mixed
     */
    public function __get($name) {
        return isset($this->data[$name])?$this->data[$name]:null;
    }

    /**
     * 妫�娴嬫暟鎹璞＄殑鍊�
     * @access public
     * @param string $name 鍚嶇О
     * @return boolean
     */
    public function __isset($name) {
        return isset($this->data[$name]);
    }

    /**
     * 閿�姣佹暟鎹璞＄殑鍊�
     * @access public
     * @param string $name 鍚嶇О
     * @return void
     */
    public function __unset($name) {
        unset($this->data[$name]);
    }

    /**
     * 鍒╃敤__call鏂规硶瀹炵幇涓�浜涚壒娈婄殑Model鏂规硶
     * @access public
     * @param string $method 鏂规硶鍚嶇О
     * @param array $args 璋冪敤鍙傛暟
     * @return mixed
     */
    public function __call($method,$args) {
        if(in_array(strtolower($method),$this->methods,true)) {
            // 杩炶疮鎿嶄綔鐨勫疄鐜�
            $this->options[strtolower($method)] =   $args[0];
            return $this;
        }elseif(in_array(strtolower($method),array('count','sum','min','max','avg'),true)){
            // 缁熻鏌ヨ鐨勫疄鐜�
            $field =  isset($args[0])?$args[0]:'*';
            return $this->getField(strtoupper($method).'('.$field.') AS tp_'.$method);
        }elseif(strtolower(substr($method,0,5))=='getby') {
            // 鏍规嵁鏌愪釜瀛楁鑾峰彇璁板綍
            $field   =   parse_name(substr($method,5));
            $where[$field] =  $args[0];
            return $this->where($where)->find();
        }elseif(strtolower(substr($method,0,10))=='getfieldby') {
            // 鏍规嵁鏌愪釜瀛楁鑾峰彇璁板綍鐨勬煇涓��
            $name   =   parse_name(substr($method,10));
            $where[$name] =$args[0];
            return $this->where($where)->getField($args[1]);
        }elseif(isset($this->_scope[$method])){// 鍛藉悕鑼冨洿鐨勫崟鐙皟鐢ㄦ敮鎸�
            return $this->scope($method,$args[0]);
        }else{
            E(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
            return;
        }
    }
    // 鍥炶皟鏂规硶 鍒濆鍖栨ā鍨�
    protected function _initialize() {}

    /**
     * 瀵逛繚瀛樺埌鏁版嵁搴撶殑鏁版嵁杩涜澶勭悊
     * @access protected
     * @param mixed $data 瑕佹搷浣滅殑鏁版嵁
     * @return boolean
     */
     protected function _facade($data) {

        // 妫�鏌ユ暟鎹瓧娈靛悎娉曟��
        if(!empty($this->fields)) {
            if(!empty($this->options['field'])) {
                $fields =   $this->options['field'];
                unset($this->options['field']);
                if(is_string($fields)) {
                    $fields =   explode(',',$fields);
                }    
            }else{
                $fields =   $this->fields;
            }        
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields,true)){
                    if(!empty($this->options['strict'])){
                        E(L('_DATA_TYPE_INVALID_').':['.$key.'=>'.$val.']');
                    }
                    unset($data[$key]);
                }elseif(is_scalar($val)) {
                    // 瀛楁绫诲瀷妫�鏌� 鍜� 寮哄埗杞崲
                    $this->_parseType($data,$key);
                }
            }
        }
       
        // 瀹夊叏杩囨护
        if(!empty($this->options['filter'])) {
            $data = array_map($this->options['filter'],$data);
            unset($this->options['filter']);
        }
        $this->_before_write($data);
        return $data;
     }

    // 鍐欏叆鏁版嵁鍓嶇殑鍥炶皟鏂规硶 鍖呮嫭鏂板鍜屾洿鏂�
    protected function _before_write(&$data) {}

    /**
     * 鏂板鏁版嵁
     * @access public
     * @param mixed $data 鏁版嵁
     * @param array $options 琛ㄨ揪寮�
     * @param boolean $replace 鏄惁replace
     * @return mixed
     */
    public function add($data='',$options=array(),$replace=false) {
        if(empty($data)) {
            // 娌℃湁浼犻�掓暟鎹紝鑾峰彇褰撳墠鏁版嵁瀵硅薄鐨勫��
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 閲嶇疆鏁版嵁
                $this->data     = array();
            }else{
                $this->error    = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 鏁版嵁澶勭悊
        $data       =   $this->_facade($data);
        // 鍒嗘瀽琛ㄨ揪寮�
        $options    =   $this->_parseOptions($options);
        if(false === $this->_before_insert($data,$options)) {
            return false;
        }
        // 鍐欏叆鏁版嵁鍒版暟鎹簱
        $result = $this->db->insert($data,$options,$replace);
        if(false !== $result && is_numeric($result)) {
            $pk     =   $this->getPk();
              // 澧炲姞澶嶅悎涓婚敭鏀寔
            if (is_array($pk)) return $result;
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                // 鑷涓婚敭杩斿洖鎻掑叆ID
                $data[$pk]  = $insertId;
                if(false === $this->_after_insert($data,$options)) {
                    return false;
                }
                return $insertId;
            }
            if(false === $this->_after_insert($data,$options)) {
                return false;
            }
        }
        return $result;
    }
    // 鎻掑叆鏁版嵁鍓嶇殑鍥炶皟鏂规硶
    protected function _before_insert(&$data,$options) {}
    // 鎻掑叆鎴愬姛鍚庣殑鍥炶皟鏂规硶
    protected function _after_insert($data,$options) {}

    public function addAll($dataList,$options=array(),$replace=false){
        if(empty($dataList)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 鏁版嵁澶勭悊
        foreach ($dataList as $key=>$data){
            $dataList[$key] = $this->_facade($data);
        }
        // 鍒嗘瀽琛ㄨ揪寮�
        $options =  $this->_parseOptions($options);
        // 鍐欏叆鏁版嵁鍒版暟鎹簱
        $result = $this->db->insertAll($dataList,$options,$replace);
        if(false !== $result ) {
            $insertId   =   $this->getLastInsID();
            if($insertId) {
                return $insertId;
            }
        }
        return $result;
    }

    /**
     * 閫氳繃Select鏂瑰紡娣诲姞璁板綍
     * @access public
     * @param string $fields 瑕佹彃鍏ョ殑鏁版嵁琛ㄥ瓧娈靛悕
     * @param string $table 瑕佹彃鍏ョ殑鏁版嵁琛ㄥ悕
     * @param array $options 琛ㄨ揪寮�
     * @return boolean
     */
    public function selectAdd($fields='',$table='',$options=array()) {
        // 鍒嗘瀽琛ㄨ揪寮�
        $options =  $this->_parseOptions($options);
        // 鍐欏叆鏁版嵁鍒版暟鎹簱
        if(false === $result = $this->db->selectInsert($fields?:$options['field'],$table?:$this->getTableName(),$options)){
            // 鏁版嵁搴撴彃鍏ユ搷浣滃け璐�
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            // 鎻掑叆鎴愬姛
            return $result;
        }
    }

    /**
     * 淇濆瓨鏁版嵁
     * @access public
     * @param mixed $data 鏁版嵁
     * @param array $options 琛ㄨ揪寮�
     * @return boolean
     */
    public function save($data='',$options=array()) {
        if(empty($data)) {
            // 娌℃湁浼犻�掓暟鎹紝鑾峰彇褰撳墠鏁版嵁瀵硅薄鐨勫��
            if(!empty($this->data)) {
                $data           =   $this->data;
                // 閲嶇疆鏁版嵁
                $this->data     =   array();
            }else{
                $this->error    =   L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        // 鏁版嵁澶勭悊
        $data       =   $this->_facade($data);
        if(empty($data)){
            // 娌℃湁鏁版嵁鍒欎笉鎵ц
            $this->error    =   L('_DATA_TYPE_INVALID_');
            return false;
        }
        // 鍒嗘瀽琛ㄨ揪寮�
        $options    =   $this->_parseOptions($options);
        $pk         =   $this->getPk();
        if(!isset($options['where']) ) {
            // 濡傛灉瀛樺湪涓婚敭鏁版嵁 鍒欒嚜鍔ㄤ綔涓烘洿鏂版潯浠�
            if (is_string($pk) && isset($data[$pk])) {
                $where[$pk]     =   $data[$pk];
                unset($data[$pk]);
            } elseif (is_array($pk)) {
                // 澧炲姞澶嶅悎涓婚敭鏀寔
                foreach ($pk as $field) {
                    if(isset($data[$field])) {
                        $where[$field]      =   $data[$field];
                    } else {
                           // 濡傛灉缂哄皯澶嶅悎涓婚敭鏁版嵁鍒欎笉鎵ц
                        $this->error        =   L('_OPERATION_WRONG_');
                        return false;
                    }
                    unset($data[$field]);
                }
            }
            if(!isset($where)){
                // 濡傛灉娌℃湁浠讳綍鏇存柊鏉′欢鍒欎笉鎵ц
                $this->error        =   L('_OPERATION_WRONG_');
                return false;
            }else{
                $options['where']   =   $where;
            }
        }

        if(is_array($options['where']) && isset($options['where'][$pk])){
            $pkValue    =   $options['where'][$pk];
        }
        if(false === $this->_before_update($data,$options)) {
            return false;
        }
        $result     =   $this->db->update($data,$options);
        if(false !== $result && is_numeric($result)) {
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_update($data,$options);
        }
        return $result;
    }
    // 鏇存柊鏁版嵁鍓嶇殑鍥炶皟鏂规硶
    protected function _before_update(&$data,$options) {}
    // 鏇存柊鎴愬姛鍚庣殑鍥炶皟鏂规硶
    protected function _after_update($data,$options) {}

    /**
     * 鍒犻櫎鏁版嵁
     * @access public
     * @param mixed $options 琛ㄨ揪寮�
     * @return mixed
     */
    public function delete($options=array()) {
        $pk   =  $this->getPk();
        if(empty($options) && empty($this->options['where'])) {
            // 濡傛灉鍒犻櫎鏉′欢涓虹┖ 鍒欏垹闄ゅ綋鍓嶆暟鎹璞℃墍瀵瑰簲鐨勮褰�
            if(!empty($this->data) && isset($this->data[$pk]))
                return $this->delete($this->data[$pk]);
            else
                return false;
        }
        if(is_numeric($options)  || is_string($options)) {
            // 鏍规嵁涓婚敭鍒犻櫎璁板綍
            if(strpos($options,',')) {
                $where[$pk]     =  array('IN', $options);
            }else{
                $where[$pk]     =  $options;
            }
            $options            =  array();
            $options['where']   =  $where;
        }
        // 鏍规嵁澶嶅悎涓婚敭鍒犻櫎璁板綍
        if (is_array($options) && (count($options) > 0) && is_array($pk)) {
            $count = 0;
            foreach (array_keys($options) as $key) {
                if (is_int($key)) $count++; 
            } 
            if ($count == count($pk)) {
                $i = 0;
                foreach ($pk as $field) {
                    $where[$field] = $options[$i];
                    unset($options[$i++]);
                }
                $options['where']  =  $where;
            } else {
                return false;
            }
        }
        // 鍒嗘瀽琛ㄨ揪寮�
        $options =  $this->_parseOptions($options);
        if(empty($options['where'])){
            // 濡傛灉鏉′欢涓虹┖ 涓嶈繘琛屽垹闄ゆ搷浣� 闄ら潪璁剧疆 1=1
            return false;
        }        
        if(is_array($options['where']) && isset($options['where'][$pk])){
            $pkValue            =  $options['where'][$pk];
        }

        if(false === $this->_before_delete($options)) {
            return false;
        }        
        $result  =    $this->db->delete($options);
        if(false !== $result && is_numeric($result)) {
            $data = array();
            if(isset($pkValue)) $data[$pk]   =  $pkValue;
            $this->_after_delete($data,$options);
        }
        // 杩斿洖鍒犻櫎璁板綍涓暟
        return $result;
    }
    // 鍒犻櫎鏁版嵁鍓嶇殑鍥炶皟鏂规硶
    protected function _before_delete($options) {}    
    // 鍒犻櫎鎴愬姛鍚庣殑鍥炶皟鏂规硶
    protected function _after_delete($data,$options) {}

    /**
     * 鏌ヨ鏁版嵁闆�
     * @access public
     * @param array $options 琛ㄨ揪寮忓弬鏁�
     * @return mixed
     */
    public function select($options=array()) {
        $pk   =  $this->getPk();
        if(is_string($options) || is_numeric($options)) {
            // 鏍规嵁涓婚敭鏌ヨ
            if(strpos($options,',')) {
                $where[$pk]     =  array('IN',$options);
            }else{
                $where[$pk]     =  $options;
            }
            $options            =  array();
            $options['where']   =  $where;
        }elseif (is_array($options) && (count($options) > 0) && is_array($pk)) {
            // 鏍规嵁澶嶅悎涓婚敭鏌ヨ
            $count = 0;
            foreach (array_keys($options) as $key) {
                if (is_int($key)) $count++; 
            } 
            if ($count == count($pk)) {
                $i = 0;
                foreach ($pk as $field) {
                    $where[$field] = $options[$i];
                    unset($options[$i++]);
                }
                $options['where']  =  $where;
            } else {
                return false;
            }
        } elseif(false === $options){ // 鐢ㄤ簬瀛愭煡璇� 涓嶆煡璇㈠彧杩斿洖SQL
        	$options['fetch_sql'] = true;
        }
        // 鍒嗘瀽琛ㄨ揪寮�
        $options    =  $this->_parseOptions($options);
        // 鍒ゆ柇鏌ヨ缂撳瓨
        if(isset($options['cache'])){
            $cache  =   $options['cache'];
            $key    =   is_string($cache['key'])?$cache['key']:md5(serialize($options));
            $data   =   S($key,'',$cache);
            if(false !== $data){
                return $data;
            }
        }        
        $resultSet  = $this->db->select($options);
        if(false === $resultSet) {
            return false;
        }
        if(!empty($resultSet)) { // 鏈夋煡璇㈢粨鏋�
            if(is_string($resultSet)){
                return $resultSet;
            }

            $resultSet  =   array_map(array($this,'_read_data'),$resultSet);
            $this->_after_select($resultSet,$options);
            if(isset($options['index'])){ // 瀵规暟鎹泦杩涜绱㈠紩
                $index  =   explode(',',$options['index']);
                foreach ($resultSet as $result){
                    $_key   =  $result[$index[0]];
                    if(isset($index[1]) && isset($result[$index[1]])){
                        $cols[$_key] =  $result[$index[1]];
                    }else{
                        $cols[$_key] =  $result;
                    }
                }
                $resultSet  =   $cols;
            }
        }

        if(isset($cache)){
            S($key,$resultSet,$cache);
        }
        return $resultSet;
    }
    // 鏌ヨ鎴愬姛鍚庣殑鍥炶皟鏂规硶
    protected function _after_select(&$resultSet,$options) {}

    /**
     * 鐢熸垚鏌ヨSQL 鍙敤浜庡瓙鏌ヨ
     * @access public
     * @return string
     */
    public function buildSql() {
        return  '( '.$this->fetchSql(true)->select().' )';
    }

    /**
     * 鍒嗘瀽琛ㄨ揪寮�
     * @access protected
     * @param array $options 琛ㄨ揪寮忓弬鏁�
     * @return array
     */
    protected function _parseOptions($options=array()) {
        if(is_array($options))
            $options =  array_merge($this->options,$options);

        if(!isset($options['table'])){
            // 鑷姩鑾峰彇琛ㄥ悕
            $options['table']   =   $this->getTableName();
            $fields             =   $this->fields;
        }else{
            // 鎸囧畾鏁版嵁琛� 鍒欓噸鏂拌幏鍙栧瓧娈靛垪琛� 浣嗕笉鏀寔绫诲瀷妫�娴�
            $fields             =   $this->getDbFields();
        }

        // 鏁版嵁琛ㄥ埆鍚�
        if(!empty($options['alias'])) {
            $options['table']  .=   ' '.$options['alias'];
        }
        // 璁板綍鎿嶄綔鐨勬ā鍨嬪悕绉�
        $options['model']       =   $this->name;

        // 瀛楁绫诲瀷楠岃瘉
        if(isset($options['where']) && is_array($options['where']) && !empty($fields) && !isset($options['join'])) {
            // 瀵规暟缁勬煡璇㈡潯浠惰繘琛屽瓧娈电被鍨嬫鏌�
            foreach ($options['where'] as $key=>$val){
                $key            =   trim($key);
                if(in_array($key,$fields,true)){
                    if(is_scalar($val)) {
                        $this->_parseType($options['where'],$key);
                    }
                }elseif(!is_numeric($key) && '_' != substr($key,0,1) && false === strpos($key,'.') && false === strpos($key,'(') && false === strpos($key,'|') && false === strpos($key,'&')){
                    if(!empty($this->options['strict'])){
                        E(L('_ERROR_QUERY_EXPRESS_').':['.$key.'=>'.$val.']');
                    } 
                    unset($options['where'][$key]);
                }
            }
        }
        // 鏌ヨ杩囧悗娓呯┖sql琛ㄨ揪寮忕粍瑁� 閬垮厤褰卞搷涓嬫鏌ヨ
        $this->options  =   array();
        // 琛ㄨ揪寮忚繃婊�
        $this->_options_filter($options);
        return $options;
    }
    // 琛ㄨ揪寮忚繃婊ゅ洖璋冩柟娉�
    protected function _options_filter(&$options) {}

    /**
     * 鏁版嵁绫诲瀷妫�娴�
     * @access protected
     * @param mixed $data 鏁版嵁
     * @param string $key 瀛楁鍚�
     * @return void
     */
    protected function _parseType(&$data,$key) {
        if(!isset($this->options['bind'][':'.$key]) && isset($this->fields['_type'][$key])){
            $fieldType = strtolower($this->fields['_type'][$key]);
            if(false !== strpos($fieldType,'enum')){
                // 鏀寔ENUM绫诲瀷浼樺厛妫�娴�
            }elseif(false === strpos($fieldType,'bigint') && false !== strpos($fieldType,'int')) {
                $data[$key]   =  intval($data[$key]);
            }elseif(false !== strpos($fieldType,'float') || false !== strpos($fieldType,'double')){
                $data[$key]   =  floatval($data[$key]);
            }elseif(false !== strpos($fieldType,'bool')){
                $data[$key]   =  (bool)$data[$key];
            }
        }
    }

    /**
     * 鏁版嵁璇诲彇鍚庣殑澶勭悊
     * @access protected
     * @param array $data 褰撳墠鏁版嵁
     * @return array
     */
    protected function _read_data($data) {
        // 妫�鏌ュ瓧娈垫槧灏�
        if(!empty($this->_map) && C('READ_DATA_MAP')) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$val])) {
                    $data[$key] =   $data[$val];
                    unset($data[$val]);
                }
            }
        }
        return $data;
    }

    /**
     * 鏌ヨ鏁版嵁
     * @access public
     * @param mixed $options 琛ㄨ揪寮忓弬鏁�
     * @return mixed
     */
    public function find($options=array()) {
        if(is_numeric($options) || is_string($options)) {
            $where[$this->getPk()]  =   $options;
            $options                =   array();
            $options['where']       =   $where;
        }
        // 鏍规嵁澶嶅悎涓婚敭鏌ユ壘璁板綍
        $pk  =  $this->getPk();
        if (is_array($options) && (count($options) > 0) && is_array($pk)) {
            // 鏍规嵁澶嶅悎涓婚敭鏌ヨ
            $count = 0;
            foreach (array_keys($options) as $key) {
                if (is_int($key)) $count++; 
            } 
            if ($count == count($pk)) {
                $i = 0;
                foreach ($pk as $field) {
                    $where[$field] = $options[$i];
                    unset($options[$i++]);
                }
                $options['where']  =  $where;
            } else {
                return false;
            }
        }
        // 鎬绘槸鏌ユ壘涓�鏉¤褰�
        $options['limit']   =   1;
        // 鍒嗘瀽琛ㄨ揪寮�
        $options            =   $this->_parseOptions($options);
        // 鍒ゆ柇鏌ヨ缂撳瓨
        if(isset($options['cache'])){
            $cache  =   $options['cache'];
            $key    =   is_string($cache['key'])?$cache['key']:md5(serialize($options));
            $data   =   S($key,'',$cache);
            if(false !== $data){
                $this->data     =   $data;
                return $data;
            }
        }
        $resultSet          =   $this->db->select($options);
        if(false === $resultSet) {
            return false;
        }
        if(empty($resultSet)) {// 鏌ヨ缁撴灉涓虹┖
            return null;
        }
        if(is_string($resultSet)){
            return $resultSet;
        }

        // 璇诲彇鏁版嵁鍚庣殑澶勭悊
        $data   =   $this->_read_data($resultSet[0]);
        $this->_after_find($data,$options);
        if(!empty($this->options['result'])) {
            return $this->returnResult($data,$this->options['result']);
        }
        $this->data     =   $data;
        if(isset($cache)){
            S($key,$data,$cache);
        }
        return $this->data;
    }
    // 鏌ヨ鎴愬姛鐨勫洖璋冩柟娉�
    protected function _after_find(&$result,$options) {}

    protected function returnResult($data,$type=''){
        if ($type){
            if(is_callable($type)){
                return call_user_func($type,$data);
            }
            switch (strtolower($type)){
                case 'json':
                    return json_encode($data);
                case 'xml':
                    return xml_encode($data);
            }
        }
        return $data;
    }

    /**
     * 澶勭悊瀛楁鏄犲皠
     * @access public
     * @param array $data 褰撳墠鏁版嵁
     * @param integer $type 绫诲瀷 0 鍐欏叆 1 璇诲彇
     * @return array
     */
    public function parseFieldsMap($data,$type=1) {
        // 妫�鏌ュ瓧娈垫槧灏�
        if(!empty($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if($type==1) { // 璇诲彇
                    if(isset($data[$val])) {
                        $data[$key] =   $data[$val];
                        unset($data[$val]);
                    }
                }else{
                    if(isset($data[$key])) {
                        $data[$val] =   $data[$key];
                        unset($data[$key]);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 璁剧疆璁板綍鐨勬煇涓瓧娈靛��
     * 鏀寔浣跨敤鏁版嵁搴撳瓧娈靛拰鏂规硶
     * @access public
     * @param string|array $field  瀛楁鍚�
     * @param string $value  瀛楁鍊�
     * @return boolean
     */
    public function setField($field,$value='') {
        if(is_array($field)) {
            $data           =   $field;
        }else{
            $data[$field]   =   $value;
        }
        return $this->save($data);
    }

    /**
     * 瀛楁鍊煎闀�
     * @access public
     * @param string $field  瀛楁鍚�
     * @param integer $step  澧為暱鍊�
     * @param integer $lazyTime  寤舵椂鏃堕棿(s)
     * @return boolean
     */
    public function setInc($field,$step=1,$lazyTime=0) {
        if($lazyTime>0) {// 寤惰繜鍐欏叆
            $condition   =  $this->options['where'];
            $guid =  md5($this->name.'_'.$field.'_'.serialize($condition));
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step ) return true; // 绛夊緟涓嬫鍐欏叆
        }
        return $this->setField($field,array('exp',$field.'+'.$step));
    }

    /**
     * 瀛楁鍊煎噺灏�
     * @access public
     * @param string $field  瀛楁鍚�
     * @param integer $step  鍑忓皯鍊�
     * @param integer $lazyTime  寤舵椂鏃堕棿(s)
     * @return boolean
     */
    public function setDec($field,$step=1,$lazyTime=0) {
        if($lazyTime>0) {// 寤惰繜鍐欏叆
            $condition   =  $this->options['where'];
            $guid =  md5($this->name.'_'.$field.'_'.serialize($condition));
            $step = $this->lazyWrite($guid,$step,$lazyTime);
            if(false === $step ) return true; // 绛夊緟涓嬫鍐欏叆
        }
        return $this->setField($field,array('exp',$field.'-'.$step));
    }

    /**
     * 寤舵椂鏇存柊妫�鏌� 杩斿洖false琛ㄧず闇�瑕佸欢鏃�
     * 鍚﹀垯杩斿洖瀹為檯鍐欏叆鐨勬暟鍊�
     * @access public
     * @param string $guid  鍐欏叆鏍囪瘑
     * @param integer $step  鍐欏叆姝ヨ繘鍊�
     * @param integer $lazyTime  寤舵椂鏃堕棿(s)
     * @return false|integer
     */
    protected function lazyWrite($guid,$step,$lazyTime) {
        if(false !== ($value = S($guid))) { // 瀛樺湪缂撳瓨鍐欏叆鏁版嵁
            if(NOW_TIME > S($guid.'_time')+$lazyTime) {
                // 寤舵椂鏇存柊鏃堕棿鍒颁簡锛屽垹闄ょ紦瀛樻暟鎹� 骞跺疄闄呭啓鍏ユ暟鎹簱
                S($guid,NULL);
                S($guid.'_time',NULL);
                return $value+$step;
            }else{
                // 杩藉姞鏁版嵁鍒扮紦瀛�
                S($guid,$value+$step);
                return false;
            }
        }else{ // 娌℃湁缂撳瓨鏁版嵁
            S($guid,$step);
            // 璁℃椂寮�濮�
            S($guid.'_time',NOW_TIME);
            return false;
        }
    }

    /**
     * 鑾峰彇涓�鏉¤褰曠殑鏌愪釜瀛楁鍊�
     * @access public
     * @param string $field  瀛楁鍚�
     * @param string $spea  瀛楁鏁版嵁闂撮殧绗﹀彿 NULL杩斿洖鏁扮粍
     * @return mixed
     */
    public function getField($field,$sepa=null) {
        $options['field']       =   $field;
        $options                =   $this->_parseOptions($options);
        // 鍒ゆ柇鏌ヨ缂撳瓨
        if(isset($options['cache'])){
            $cache  =   $options['cache'];
            $key    =   is_string($cache['key'])?$cache['key']:md5($sepa.serialize($options));
            $data   =   S($key,'',$cache);
            if(false !== $data){
                return $data;
            }
        }        
        $field                  =   trim($field);
        if(strpos($field,',') && false !== $sepa) { // 澶氬瓧娈�
            if(!isset($options['limit'])){
                $options['limit']   =   is_numeric($sepa)?$sepa:'';
            }
            $resultSet          =   $this->db->select($options);
            if(!empty($resultSet)) {
		        if(is_string($resultSet)){
		            return $resultSet;
		        }            	
                $_field         =   explode(',', $field);
                $field          =   array_keys($resultSet[0]);
                $key1           =   array_shift($field);
                $key2           =   array_shift($field);
                $cols           =   array();
                $count          =   count($_field);
                foreach ($resultSet as $result){
                    $name   =  $result[$key1];
                    if(2==$count) {
                        $cols[$name]   =  $result[$key2];
                    }else{
                        $cols[$name]   =  is_string($sepa)?implode($sepa,array_slice($result,1)):$result;
                    }
                }
                if(isset($cache)){
                    S($key,$cols,$cache);
                }
                return $cols;
            }
        }else{   // 鏌ユ壘涓�鏉¤褰�
            // 杩斿洖鏁版嵁涓暟
            if(true !== $sepa) {// 褰搒epa鎸囧畾涓簍rue鐨勬椂鍊� 杩斿洖鎵�鏈夋暟鎹�
                $options['limit']   =   is_numeric($sepa)?$sepa:1;
            }
            $result = $this->db->select($options);
            if(!empty($result)) {
		        if(is_string($result)){
		            return $result;
		        }            	
                if(true !== $sepa && 1==$options['limit']) {
                    $data   =   reset($result[0]);
                    if(isset($cache)){
                        S($key,$data,$cache);
                    }            
                    return $data;
                }
                foreach ($result as $val){
                    $array[]    =   $val[$field];
                }
                if(isset($cache)){
                    S($key,$array,$cache);
                }                
                return $array;
            }
        }
        return null;
    }

    /**
     * 鍒涘缓鏁版嵁瀵硅薄 浣嗕笉淇濆瓨鍒版暟鎹簱
     * @access public
     * @param mixed $data 鍒涘缓鏁版嵁
     * @param string $type 鐘舵��
     * @return mixed
     */
     public function create($data='',$type='') {
        // 濡傛灉娌℃湁浼犲�奸粯璁ゅ彇POST鏁版嵁
        if(empty($data)) {
            $data   =   I('post.');
        }elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }
        // 楠岃瘉鏁版嵁
        if(empty($data) || !is_array($data)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }

        // 鐘舵��
        $type = $type?:(!empty($data[$this->getPk()])?self::MODEL_UPDATE:self::MODEL_INSERT);

        // 妫�鏌ュ瓧娈垫槧灏�
        if(!empty($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$key])) {
                    $data[$val] =   $data[$key];
                    unset($data[$key]);
                }
            }
        }

        // 妫�娴嬫彁浜ゅ瓧娈电殑鍚堟硶鎬�
        if(isset($this->options['field'])) { // $this->field('field1,field2...')->create()
            $fields =   $this->options['field'];
            unset($this->options['field']);
        }elseif($type == self::MODEL_INSERT && isset($this->insertFields)) {
            $fields =   $this->insertFields;
        }elseif($type == self::MODEL_UPDATE && isset($this->updateFields)) {
            $fields =   $this->updateFields;
        }
        if(isset($fields)) {
            if(is_string($fields)) {
                $fields =   explode(',',$fields);
            }
            // 鍒ゆ柇浠ょ墝楠岃瘉瀛楁
            if(C('TOKEN_ON'))   $fields[] = C('TOKEN_NAME', null, '__hash__');
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }
            }
        }

        // 鏁版嵁鑷姩楠岃瘉
        if(!$this->autoValidation($data,$type)) return false;

        // 琛ㄥ崟浠ょ墝楠岃瘉
        if(!$this->autoCheckToken($data)) {
            $this->error = L('_TOKEN_ERROR_');
            return false;
        }

        // 楠岃瘉瀹屾垚鐢熸垚鏁版嵁瀵硅薄
        if($this->autoCheckFields) { // 寮�鍚瓧娈垫娴� 鍒欒繃婊ら潪娉曞瓧娈垫暟鎹�
            $fields =   $this->getDbFields();
            foreach ($data as $key=>$val){
                if(!in_array($key,$fields)) {
                    unset($data[$key]);
                }elseif(MAGIC_QUOTES_GPC && is_string($val)){
                    $data[$key] =   stripslashes($val);
                }
            }
        }

        // 鍒涘缓瀹屾垚瀵规暟鎹繘琛岃嚜鍔ㄥ鐞�
        $this->autoOperation($data,$type);
        // 璧嬪�煎綋鍓嶆暟鎹璞�
        $this->data =   $data;
        // 杩斿洖鍒涘缓鐨勬暟鎹互渚涘叾浠栬皟鐢�
        return $data;
     }

    // 鑷姩琛ㄥ崟浠ょ墝楠岃瘉
    // TODO  ajax鏃犲埛鏂板娆℃彁浜ゆ殏涓嶈兘婊¤冻
    public function autoCheckToken($data) {
        // 鏀寔浣跨敤token(false) 鍏抽棴浠ょ墝楠岃瘉
        if(isset($this->options['token']) && !$this->options['token']) return true;
        if(C('TOKEN_ON')){
            $name   = C('TOKEN_NAME', null, '__hash__');
            if(!isset($data[$name]) || !isset($_SESSION[$name])) { // 浠ょ墝鏁版嵁鏃犳晥
                return false;
            }

            // 浠ょ墝楠岃瘉
            list($key,$value)  =  explode('_',$data[$name]);
            if(isset($_SESSION[$name][$key]) && $value && $_SESSION[$name][$key] === $value) { // 闃叉閲嶅鎻愪氦
                unset($_SESSION[$name][$key]); // 楠岃瘉瀹屾垚閿�姣乻ession
                return true;
            }
            // 寮�鍚疶OKEN閲嶇疆
            if(C('TOKEN_RESET')) unset($_SESSION[$name][$key]);
            return false;
        }
        return true;
    }

    /**
     * 浣跨敤姝ｅ垯楠岃瘉鏁版嵁
     * @access public
     * @param string $value  瑕侀獙璇佺殑鏁版嵁
     * @param string $rule 楠岃瘉瑙勫垯
     * @return boolean
     */
    public function regex($value,$rule) {
        $validate = array(
            'require'   =>  '/\S+/',
            'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
            'currency'  =>  '/^\d+(\.\d+)?$/',
            'number'    =>  '/^\d+$/',
            'zip'       =>  '/^\d{6}$/',
            'integer'   =>  '/^[-\+]?\d+$/',
            'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
            'english'   =>  '/^[A-Za-z]+$/',
        );
        // 妫�鏌ユ槸鍚︽湁鍐呯疆鐨勬鍒欒〃杈惧紡
        if(isset($validate[strtolower($rule)]))
            $rule       =   $validate[strtolower($rule)];
        return preg_match($rule,$value)===1;
    }

    /**
     * 鑷姩琛ㄥ崟澶勭悊
     * @access public
     * @param array $data 鍒涘缓鏁版嵁
     * @param string $type 鍒涘缓绫诲瀷
     * @return mixed
     */
    private function autoOperation(&$data,$type) {
        if(!empty($this->options['auto'])) {
            $_auto   =   $this->options['auto'];
            unset($this->options['auto']);
        }elseif(!empty($this->_auto)){
            $_auto   =   $this->_auto;
        }
        // 鑷姩濉厖
        if(isset($_auto)) {
            foreach ($_auto as $auto){
                // 濉厖鍥犲瓙瀹氫箟鏍煎紡
                // array('field','濉厖鍐呭','濉厖鏉′欢','闄勫姞瑙勫垯',[棰濆鍙傛暟])
                if(empty($auto[2])) $auto[2] =  self::MODEL_INSERT; // 榛樿涓烘柊澧炵殑鏃跺�欒嚜鍔ㄥ～鍏�
                if( $type == $auto[2] || $auto[2] == self::MODEL_BOTH) {
                    if(empty($auto[3])) $auto[3] =  'string';
                    switch(trim($auto[3])) {
                        case 'function':    //  浣跨敤鍑芥暟杩涜濉厖 瀛楁鐨勫�间綔涓哄弬鏁�
                        case 'callback': // 浣跨敤鍥炶皟鏂规硶
                            $args = isset($auto[4])?(array)$auto[4]:array();
                            if(isset($data[$auto[0]])) {
                                array_unshift($args,$data[$auto[0]]);
                            }
                            if('function'==$auto[3]) {
                                $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                            }else{
                                $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                            }
                            break;
                        case 'field':    // 鐢ㄥ叾瀹冨瓧娈电殑鍊艰繘琛屽～鍏�
                            $data[$auto[0]] = $data[$auto[1]];
                            break;
                        case 'ignore': // 涓虹┖蹇界暐
                            if($auto[1]===$data[$auto[0]])
                                unset($data[$auto[0]]);
                            break;
                        case 'string':
                        default: // 榛樿浣滀负瀛楃涓插～鍏�
                            $data[$auto[0]] = $auto[1];
                    }
                    if(isset($data[$auto[0]]) && false === $data[$auto[0]] )   unset($data[$auto[0]]);
                }
            }
        }
        return $data;
    }

    /**
     * 鑷姩琛ㄥ崟楠岃瘉
     * @access protected
     * @param array $data 鍒涘缓鏁版嵁
     * @param string $type 鍒涘缓绫诲瀷
     * @return boolean
     */
    protected function autoValidation($data,$type) {
        if(!empty($this->options['validate'])) {
            $_validate   =   $this->options['validate'];
            unset($this->options['validate']);
        }elseif(!empty($this->_validate)){
            $_validate   =   $this->_validate;
        }
        // 灞炴�ч獙璇�
        if(isset($_validate)) { // 濡傛灉璁剧疆浜嗘暟鎹嚜鍔ㄩ獙璇佸垯杩涜鏁版嵁楠岃瘉
            if($this->patchValidate) { // 閲嶇疆楠岃瘉閿欒淇℃伅
                $this->error = array();
            }
            foreach($_validate as $key=>$val) {
                // 楠岃瘉鍥犲瓙瀹氫箟鏍煎紡
                // array(field,rule,message,condition,type,when,params)
                // 鍒ゆ柇鏄惁闇�瑕佹墽琛岄獙璇�
                if(empty($val[5]) || ( $val[5]== self::MODEL_BOTH && $type < 3 ) || $val[5]== $type ) {
                    if(0==strpos($val[2],'{%') && strpos($val[2],'}'))
                        // 鏀寔鎻愮ず淇℃伅鐨勫璇█ 浣跨敤 {%璇█瀹氫箟} 鏂瑰紡
                        $val[2]  =  L(substr($val[2],2,-1));
                    $val[3]  =  isset($val[3])?$val[3]:self::EXISTS_VALIDATE;
                    $val[4]  =  isset($val[4])?$val[4]:'regex';
                    // 鍒ゆ柇楠岃瘉鏉′欢
                    switch($val[3]) {
                        case self::MUST_VALIDATE:   // 蹇呴』楠岃瘉 涓嶇琛ㄥ崟鏄惁鏈夎缃瀛楁
                            if(false === $this->_validationField($data,$val)) 
                                return false;
                            break;
                        case self::VALUE_VALIDATE:    // 鍊间笉涓虹┖鐨勬椂鍊欐墠楠岃瘉
                            if('' != trim($data[$val[0]]))
                                if(false === $this->_validationField($data,$val)) 
                                    return false;
                            break;
                        default:    // 榛樿琛ㄥ崟瀛樺湪璇ュ瓧娈靛氨楠岃瘉
                            if(isset($data[$val[0]]))
                                if(false === $this->_validationField($data,$val)) 
                                    return false;
                    }
                }
            }
            // 鎵归噺楠岃瘉鐨勬椂鍊欐渶鍚庤繑鍥為敊璇�
            if(!empty($this->error)) return false;
        }
        return true;
    }

    /**
     * 楠岃瘉琛ㄥ崟瀛楁 鏀寔鎵归噺楠岃瘉
     * 濡傛灉鎵归噺楠岃瘉杩斿洖閿欒鐨勬暟缁勪俊鎭�
     * @access protected
     * @param array $data 鍒涘缓鏁版嵁
     * @param array $val 楠岃瘉鍥犲瓙
     * @return boolean
     */
    protected function _validationField($data,$val) {
        if($this->patchValidate && isset($this->error[$val[0]]))
            return ; //褰撳墠瀛楁宸茬粡鏈夎鍒欓獙璇佹病鏈夐�氳繃
        if(false === $this->_validationFieldItem($data,$val)){
            if($this->patchValidate) {
                $this->error[$val[0]]   =   $val[2];
            }else{
                $this->error            =   $val[2];
                return false;
            }
        }
        return ;
    }

    /**
     * 鏍规嵁楠岃瘉鍥犲瓙楠岃瘉瀛楁
     * @access protected
     * @param array $data 鍒涘缓鏁版嵁
     * @param array $val 楠岃瘉鍥犲瓙
     * @return boolean
     */
    protected function _validationFieldItem($data,$val) {
        switch(strtolower(trim($val[4]))) {
            case 'function':// 浣跨敤鍑芥暟杩涜楠岃瘉
            case 'callback':// 璋冪敤鏂规硶杩涜楠岃瘉
                $args = isset($val[6])?(array)$val[6]:array();
                if(is_string($val[0]) && strpos($val[0], ','))
                    $val[0] = explode(',', $val[0]);
                if(is_array($val[0])){
                    // 鏀寔澶氫釜瀛楁楠岃瘉
                    foreach($val[0] as $field)
                        $_data[$field] = $data[$field];
                    array_unshift($args, $_data);
                }else{
                    array_unshift($args, $data[$val[0]]);
                }
                if('function'==$val[4]) {
                    return call_user_func_array($val[1], $args);
                }else{
                    return call_user_func_array(array(&$this, $val[1]), $args);
                }
            case 'confirm': // 楠岃瘉涓や釜瀛楁鏄惁鐩稿悓
                return $data[$val[0]] == $data[$val[1]];
            case 'unique': // 楠岃瘉鏌愪釜鍊兼槸鍚﹀敮涓�
                if(is_string($val[0]) && strpos($val[0],','))
                    $val[0]  =  explode(',',$val[0]);
                $map = array();
                if(is_array($val[0])) {
                    // 鏀寔澶氫釜瀛楁楠岃瘉
                    foreach ($val[0] as $field)
                        $map[$field]   =  $data[$field];
                }else{
                    $map[$val[0]] = $data[$val[0]];
                }
                $pk =   $this->getPk();
                if(!empty($data[$pk]) && is_string($pk)) { // 瀹屽杽缂栬緫鐨勬椂鍊欓獙璇佸敮涓�
                    $map[$pk] = array('neq',$data[$pk]);
                }
                if($this->where($map)->find())   return false;
                return true;
            default:  // 妫�鏌ラ檮鍔犺鍒�
                return $this->check($data[$val[0]],$val[1],$val[4]);
        }
    }

    /**
     * 楠岃瘉鏁版嵁 鏀寔 in between equal length regex expire ip_allow ip_deny
     * @access public
     * @param string $value 楠岃瘉鏁版嵁
     * @param mixed $rule 楠岃瘉琛ㄨ揪寮�
     * @param string $type 楠岃瘉鏂瑰紡 榛樿涓烘鍒欓獙璇�
     * @return boolean
     */
    public function check($value,$rule,$type='regex'){
        $type   =   strtolower(trim($type));
        switch($type) {
            case 'in': // 楠岃瘉鏄惁鍦ㄦ煇涓寚瀹氳寖鍥翠箣鍐� 閫楀彿鍒嗛殧瀛楃涓叉垨鑰呮暟缁�
            case 'notin':
                $range   = is_array($rule)? $rule : explode(',',$rule);
                return $type == 'in' ? in_array($value ,$range) : !in_array($value ,$range);
            case 'between': // 楠岃瘉鏄惁鍦ㄦ煇涓寖鍥�
            case 'notbetween': // 楠岃瘉鏄惁涓嶅湪鏌愪釜鑼冨洿            
                if (is_array($rule)){
                    $min    =    $rule[0];
                    $max    =    $rule[1];
                }else{
                    list($min,$max)   =  explode(',',$rule);
                }
                return $type == 'between' ? $value>=$min && $value<=$max : $value<$min || $value>$max;
            case 'equal': // 楠岃瘉鏄惁绛変簬鏌愪釜鍊�
            case 'notequal': // 楠岃瘉鏄惁绛変簬鏌愪釜鍊�            
                return $type == 'equal' ? $value == $rule : $value != $rule;
            case 'length': // 楠岃瘉闀垮害
                $length  =  mb_strlen($value,'utf-8'); // 褰撳墠鏁版嵁闀垮害
                if(strpos($rule,',')) { // 闀垮害鍖洪棿
                    list($min,$max)   =  explode(',',$rule);
                    return $length >= $min && $length <= $max;
                }else{// 鎸囧畾闀垮害
                    return $length == $rule;
                }
            case 'expire':
                list($start,$end)   =  explode(',',$rule);
                if(!is_numeric($start)) $start   =  strtotime($start);
                if(!is_numeric($end)) $end   =  strtotime($end);
                return NOW_TIME >= $start && NOW_TIME <= $end;
            case 'ip_allow': // IP 鎿嶄綔璁稿彲楠岃瘉
                return in_array(get_client_ip(),explode(',',$rule));
            case 'ip_deny': // IP 鎿嶄綔绂佹楠岃瘉
                return !in_array(get_client_ip(),explode(',',$rule));
            case 'regex':
            default:    // 榛樿浣跨敤姝ｅ垯楠岃瘉 鍙互浣跨敤楠岃瘉绫讳腑瀹氫箟鐨勯獙璇佸悕绉�
                // 妫�鏌ラ檮鍔犺鍒�
                return $this->regex($value,$rule);
        }
    }

    /**
     * SQL鏌ヨ
     * @access public
     * @param string $sql  SQL鎸囦护
     * @param mixed $parse  鏄惁闇�瑕佽В鏋怱QL
     * @return mixed
     */
    public function query($sql,$parse=false) {
        if(!is_bool($parse) && !is_array($parse)) {
            $parse = func_get_args();
            array_shift($parse);
        }
        $sql  =   $this->parseSql($sql,$parse);
        return $this->db->query($sql);
    }

    /**
     * 鎵цSQL璇彞
     * @access public
     * @param string $sql  SQL鎸囦护
     * @param mixed $parse  鏄惁闇�瑕佽В鏋怱QL
     * @return false | integer
     */
    public function execute($sql,$parse=false) {
        if(!is_bool($parse) && !is_array($parse)) {
            $parse = func_get_args();
            array_shift($parse);
        }
        $sql  =   $this->parseSql($sql,$parse);
        return $this->db->execute($sql);
    }

    /**
     * 瑙ｆ瀽SQL璇彞
     * @access public
     * @param string $sql  SQL鎸囦护
     * @param boolean $parse  鏄惁闇�瑕佽В鏋怱QL
     * @return string
     */
    protected function parseSql($sql,$parse) {
        // 鍒嗘瀽琛ㄨ揪寮�
        if(true === $parse) {
            $options =  $this->_parseOptions();
            $sql    =   $this->db->parseSql($sql,$options);
        }elseif(is_array($parse)){ // SQL棰勫鐞�
            $parse  =   array_map(array($this->db,'escapeString'),$parse);
            $sql    =   vsprintf($sql,$parse);
            $sql    =   strtr($sql,array('__TABLE__'=>$this->getTableName(),'__PREFIX__'=>$this->tablePrefix));
            $prefix =   $this->tablePrefix;
            $sql    =   preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $sql);
        }else{
            $sql    =   strtr($sql,array('__TABLE__'=>$this->getTableName(),'__PREFIX__'=>$this->tablePrefix));
            $prefix =   $this->tablePrefix;
            $sql    =   preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $sql);
        }
        $this->db->setModel($this->name);
        return $sql;
    }

    /**
     * 鍒囨崲褰撳墠鐨勬暟鎹簱杩炴帴
     * @access public
     * @param integer $linkNum  杩炴帴搴忓彿
     * @param mixed $config  鏁版嵁搴撹繛鎺ヤ俊鎭�
     * @param boolean $force 寮哄埗閲嶆柊杩炴帴
     * @return Model
     */
    public function db($linkNum='',$config='',$force=false) {
        if('' === $linkNum && $this->db) {
            return $this->db;
        }

        if(!isset($this->_db[$linkNum]) || $force ) {
            // 鍒涘缓涓�涓柊鐨勫疄渚�
            if(!empty($config) && is_string($config) && false === strpos($config,'/')) { // 鏀寔璇诲彇閰嶇疆鍙傛暟
                $config  =  C($config);
            }
            $this->_db[$linkNum]            =    Db::getInstance($config);
        }elseif(NULL === $config){
            $this->_db[$linkNum]->close(); // 鍏抽棴鏁版嵁搴撹繛鎺�
            unset($this->_db[$linkNum]);
            return ;
        }

        // 鍒囨崲鏁版嵁搴撹繛鎺�
        $this->db   =    $this->_db[$linkNum];
        $this->_after_db();
        // 瀛楁妫�娴�
        if(!empty($this->name) && $this->autoCheckFields)    $this->_checkTableInfo();
        return $this;
    }
    // 鏁版嵁搴撳垏鎹㈠悗鍥炶皟鏂规硶
    protected function _after_db() {}

    /**
     * 寰楀埌褰撳墠鐨勬暟鎹璞″悕绉�
     * @access public
     * @return string
     */
    public function getModelName() {
        if(empty($this->name)){
            $name = substr(get_class($this),0,-strlen(C('DEFAULT_M_LAYER')));
            if ( $pos = strrpos($name,'\\') ) {//鏈夊懡鍚嶇┖闂�
                $this->name = substr($name,$pos+1);
            }else{
                $this->name = $name;
            }
        }
        return $this->name;
    }

    /**
     * 寰楀埌瀹屾暣鐨勬暟鎹〃鍚�
     * @access public
     * @return string
     */
    public function getTableName() {
        if(empty($this->trueTableName)) {
            $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
            if(!empty($this->tableName)) {
                $tableName .= $this->tableName;
            }else{
                $tableName .= parse_name($this->name);
            }
            $this->trueTableName    =   strtolower($tableName);
        }
        return (!empty($this->dbName)?$this->dbName.'.':'').$this->trueTableName;
    }

    /**
     * 鍚姩浜嬪姟
     * @access public
     * @return void
     */
    public function startTrans() {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    /**
     * 鎻愪氦浜嬪姟
     * @access public
     * @return boolean
     */
    public function commit() {
        return $this->db->commit();
    }

    /**
     * 浜嬪姟鍥炴粴
     * @access public
     * @return boolean
     */
    public function rollback() {
        return $this->db->rollback();
    }

    /**
     * 杩斿洖妯″瀷鐨勯敊璇俊鎭�
     * @access public
     * @return string
     */
    public function getError(){
        return $this->error;
    }

    /**
     * 杩斿洖鏁版嵁搴撶殑閿欒淇℃伅
     * @access public
     * @return string
     */
    public function getDbError() {
        return $this->db->getError();
    }

    /**
     * 杩斿洖鏈�鍚庢彃鍏ョ殑ID
     * @access public
     * @return string
     */
    public function getLastInsID() {
        return $this->db->getLastInsID();
    }

    /**
     * 杩斿洖鏈�鍚庢墽琛岀殑sql璇彞
     * @access public
     * @return string
     */
    public function getLastSql() {
        return $this->db->getLastSql($this->name);
    }
    // 閴翠簬getLastSql姣旇緝甯哥敤 澧炲姞_sql 鍒悕
    public function _sql(){
        return $this->getLastSql();
    }

    /**
     * 鑾峰彇涓婚敭鍚嶇О
     * @access public
     * @return string
     */
    public function getPk() {
        return $this->pk;
    }

    /**
     * 鑾峰彇鏁版嵁琛ㄥ瓧娈典俊鎭�
     * @access public
     * @return array
     */
    public function getDbFields(){
        if(isset($this->options['table'])) {// 鍔ㄦ�佹寚瀹氳〃鍚�
            if(is_array($this->options['table'])){
                $table  =   key($this->options['table']);
            }else{
                $table  =   $this->options['table'];
                if(strpos($table,')')){
                    // 瀛愭煡璇�
                    return false;
                }
            }
            $fields     =   $this->db->getFields($table);
            return  $fields ? array_keys($fields) : false;
        }
        if($this->fields) {
            $fields     =  $this->fields;
            unset($fields['_type'],$fields['_pk']);
            return $fields;
        }
        return false;
    }

    /**
     * 璁剧疆鏁版嵁瀵硅薄鍊�
     * @access public
     * @param mixed $data 鏁版嵁
     * @return Model
     */
    public function data($data=''){
        if('' === $data && !empty($this->data)) {
            return $this->data;
        }
        if(is_object($data)){
            $data   =   get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            E(L('_DATA_TYPE_INVALID_'));
        }
        $this->data = $data;
        return $this;
    }

    /**
     * 鎸囧畾褰撳墠鐨勬暟鎹〃
     * @access public
     * @param mixed $table
     * @return Model
     */
    public function table($table) {
        $prefix =   $this->tablePrefix;
        if(is_array($table)) {
            $this->options['table'] =   $table;
        }elseif(!empty($table)) {
            //灏哶_TABLE_NAME__鏇挎崲鎴愬甫鍓嶇紑鐨勮〃鍚�
            $table  = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $table);
            $this->options['table'] =   $table;
        }
        return $this;
    }

    /**
     * USING鏀寔 鐢ㄤ簬澶氳〃鍒犻櫎
     * @access public
     * @param mixed $using
     * @return Model
     */
    public function using($using){
        $prefix =   $this->tablePrefix;
        if(is_array($using)) {
            $this->options['using'] =   $using;
        }elseif(!empty($using)) {
            //灏哶_TABLE_NAME__鏇挎崲鎴愬甫鍓嶇紑鐨勮〃鍚�
            $using  = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $using);
            $this->options['using'] =   $using;
        }
        return $this;
    }

    /**
     * 鏌ヨSQL缁勮 join
     * @access public
     * @param mixed $join
     * @param string $type JOIN绫诲瀷
     * @return Model
     */
    public function join($join,$type='INNER') {
        $prefix =   $this->tablePrefix;
        if(is_array($join)) {
            foreach ($join as $key=>&$_join){
                $_join  =   preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $_join);
                $_join  =   false !== stripos($_join,'JOIN')? $_join : $type.' JOIN ' .$_join;
            }
            $this->options['join']      =   $join;
        }elseif(!empty($join)) {
            //灏哶_TABLE_NAME__瀛楃涓叉浛鎹㈡垚甯﹀墠缂�鐨勮〃鍚�
            $join  = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $join);
            $this->options['join'][]    =   false !== stripos($join,'JOIN')? $join : $type.' JOIN '.$join;
        }
        return $this;
    }

    /**
     * 鏌ヨSQL缁勮 union
     * @access public
     * @param mixed $union
     * @param boolean $all
     * @return Model
     */
    public function union($union,$all=false) {
        if(empty($union)) return $this;
        if($all) {
            $this->options['union']['_all']  =   true;
        }
        if(is_object($union)) {
            $union   =  get_object_vars($union);
        }
        // 杞崲union琛ㄨ揪寮�
        if(is_string($union) ) {
            $prefix =   $this->tablePrefix;
            //灏哶_TABLE_NAME__瀛楃涓叉浛鎹㈡垚甯﹀墠缂�鐨勮〃鍚�
            $options  = preg_replace_callback("/__([A-Z0-9_-]+)__/sU", function($match) use($prefix){ return $prefix.strtolower($match[1]);}, $union);
        }elseif(is_array($union)){
            if(isset($union[0])) {
                $this->options['union']  =  array_merge($this->options['union'],$union);
                return $this;
            }else{
                $options =  $union;
            }
        }else{
            E(L('_DATA_TYPE_INVALID_'));
        }
        $this->options['union'][]  =   $options;
        return $this;
    }

    /**
     * 鏌ヨ缂撳瓨
     * @access public
     * @param mixed $key
     * @param integer $expire
     * @param string $type
     * @return Model
     */
    public function cache($key=true,$expire=null,$type=''){
        // 澧炲姞蹇嵎璋冪敤鏂瑰紡 cache(10) 绛夊悓浜� cache(true, 10)
        if(is_numeric($key) && is_null($expire)){
            $expire = $key;
            $key    = true;
        }
        if(false !== $key)
            $this->options['cache']  =  array('key'=>$key,'expire'=>$expire,'type'=>$type);
        return $this;
    }

    /**
     * 鎸囧畾鏌ヨ瀛楁 鏀寔瀛楁鎺掗櫎
     * @access public
     * @param mixed $field
     * @param boolean $except 鏄惁鎺掗櫎
     * @return Model
     */
    public function field($field,$except=false){
        if(true === $field) {// 鑾峰彇鍏ㄩ儴瀛楁
            $fields     =  $this->getDbFields();
            $field      =  $fields?:'*';
        }elseif($except) {// 瀛楁鎺掗櫎
            if(is_string($field)) {
                $field  =  explode(',',$field);
            }
            $fields     =  $this->getDbFields();
            $field      =  $fields?array_diff($fields,$field):$field;
        }
        $this->options['field']   =   $field;
        return $this;
    }

    /**
     * 璋冪敤鍛藉悕鑼冨洿
     * @access public
     * @param mixed $scope 鍛藉悕鑼冨洿鍚嶇О 鏀寔澶氫釜 鍜岀洿鎺ュ畾涔�
     * @param array $args 鍙傛暟
     * @return Model
     */
    public function scope($scope='',$args=NULL){
        if('' === $scope) {
            if(isset($this->_scope['default'])) {
                // 榛樿鐨勫懡鍚嶈寖鍥�
                $options    =   $this->_scope['default'];
            }else{
                return $this;
            }
        }elseif(is_string($scope)){ // 鏀寔澶氫釜鍛藉悕鑼冨洿璋冪敤 鐢ㄩ�楀彿鍒嗗壊
            $scopes         =   explode(',',$scope);
            $options        =   array();
            foreach ($scopes as $name){
                if(!isset($this->_scope[$name])) continue;
                $options    =   array_merge($options,$this->_scope[$name]);
            }
            if(!empty($args) && is_array($args)) {
                $options    =   array_merge($options,$args);
            }
        }elseif(is_array($scope)){ // 鐩存帴浼犲叆鍛藉悕鑼冨洿瀹氫箟
            $options        =   $scope;
        }
        
        if(is_array($options) && !empty($options)){
            $this->options  =   array_merge($this->options,array_change_key_case($options));
        }
        return $this;
    }

    /**
     * 鎸囧畾鏌ヨ鏉′欢 鏀寔瀹夊叏杩囨护
     * @access public
     * @param mixed $where 鏉′欢琛ㄨ揪寮�
     * @param mixed $parse 棰勫鐞嗗弬鏁�
     * @return Model
     */
    public function where($where,$parse=null){
        if(!is_null($parse) && is_string($where)) {
            if(!is_array($parse)) {
                $parse = func_get_args();
                array_shift($parse);
            }
            $parse = array_map(array($this->db,'escapeString'),$parse);
            $where =   vsprintf($where,$parse);
        }elseif(is_object($where)){
            $where  =   get_object_vars($where);
        }
        if(is_string($where) && '' != $where){
            $map    =   array();
            $map['_string']   =   $where;
            $where  =   $map;
        }        
        if(isset($this->options['where'])){
            $this->options['where'] =   array_merge($this->options['where'],$where);
        }else{
            $this->options['where'] =   $where;
        }
        
        return $this;
    }

    /**
     * 鎸囧畾鏌ヨ鏁伴噺
     * @access public
     * @param mixed $offset 璧峰浣嶇疆
     * @param mixed $length 鏌ヨ鏁伴噺
     * @return Model
     */
    public function limit($offset,$length=null){
        if(is_null($length) && strpos($offset,',')){
            list($offset,$length)   =   explode(',',$offset);
        }
        $this->options['limit']     =   intval($offset).( $length? ','.intval($length) : '' );
        return $this;
    }

    /**
     * 鎸囧畾鍒嗛〉
     * @access public
     * @param mixed $page 椤垫暟
     * @param mixed $listRows 姣忛〉鏁伴噺
     * @return Model
     */
    public function page($page,$listRows=null){
        if(is_null($listRows) && strpos($page,',')){
            list($page,$listRows)   =   explode(',',$page);
        }
        $this->options['page']      =   array(intval($page),intval($listRows));
        return $this;
    }

    /**
     * 鏌ヨ娉ㄩ噴
     * @access public
     * @param string $comment 娉ㄩ噴
     * @return Model
     */
    public function comment($comment){
        $this->options['comment'] =   $comment;
        return $this;
    }

    /**
     * 鑾峰彇鎵ц鐨凷QL璇彞
     * @access public
     * @param boolean $fetch 鏄惁杩斿洖sql
     * @return Model
     */
    public function fetchSql($fetch=true){
        $this->options['fetch_sql'] =   $fetch;
        return $this;
    }

    /**
     * 鍙傛暟缁戝畾
     * @access public
     * @param string $key  鍙傛暟鍚�
     * @param mixed $value  缁戝畾鐨勫彉閲忓強缁戝畾鍙傛暟
     * @return Model
     */
    public function bind($key,$value=false) {
        if(is_array($key)){
            $this->options['bind'] =    $key;
        }else{
            $num =  func_num_args();
            if($num>2){
                $params =   func_get_args();
                array_shift($params);
                $this->options['bind'][$key] =  $params;
            }else{
                $this->options['bind'][$key] =  $value;
            }        
        }
        return $this;
    }

    /**
     * 璁剧疆妯″瀷鐨勫睘鎬у��
     * @access public
     * @param string $name 鍚嶇О
     * @param mixed $value 鍊�
     * @return Model
     */
    public function setProperty($name,$value) {
        if(property_exists($this,$name))
            $this->$name = $value;
        return $this;
    }

}
