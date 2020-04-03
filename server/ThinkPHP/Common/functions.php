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

/**
 * Think 绯荤粺鍑芥暟搴�
 */

/**
 * 鑾峰彇鍜岃缃厤缃弬鏁� 鏀寔鎵归噺瀹氫箟
 * @param string|array $name 閰嶇疆鍙橀噺
 * @param mixed $value 閰嶇疆鍊�
 * @param mixed $default 榛樿鍊�
 * @return mixed
 */
function C($name=null, $value=null,$default=null) {
    static $_config = array();
    // 鏃犲弬鏁版椂鑾峰彇鎵�鏈�
    if (empty($name)) {
        return $_config;
    }
    // 浼樺厛鎵ц璁剧疆鑾峰彇鎴栬祴鍊�
    if (is_string($name)) {
        if (!strpos($name, '.')) {
            $name = strtoupper($name);
            if (is_null($value))
                return isset($_config[$name]) ? $_config[$name] : $default;
            $_config[$name] = $value;
            return null;
        }
        // 浜岀淮鏁扮粍璁剧疆鍜岃幏鍙栨敮鎸�
        $name = explode('.', $name);
        $name[0]   =  strtoupper($name[0]);
        if (is_null($value))
            return isset($_config[$name[0]][$name[1]]) ? $_config[$name[0]][$name[1]] : $default;
        $_config[$name[0]][$name[1]] = $value;
        return null;
    }
    // 鎵归噺璁剧疆
    if (is_array($name)){
        $_config = array_merge($_config, array_change_key_case($name,CASE_UPPER));
        return null;
    }
    return null; // 閬垮厤闈炴硶鍙傛暟
}

/**
 * 鍔犺浇閰嶇疆鏂囦欢 鏀寔鏍煎紡杞崲 浠呮敮鎸佷竴绾ч厤缃�
 * @param string $file 閰嶇疆鏂囦欢鍚�
 * @param string $parse 閰嶇疆瑙ｆ瀽鏂规硶 鏈変簺鏍煎紡闇�瑕佺敤鎴疯嚜宸辫В鏋�
 * @return array
 */
function load_config($file,$parse=CONF_PARSE){
    $ext  = pathinfo($file,PATHINFO_EXTENSION);
    switch($ext){
        case 'php':
            return include $file;
        case 'ini':
            return parse_ini_file($file);
        case 'yaml':
            return yaml_parse_file($file);
        case 'xml': 
            return (array)simplexml_load_file($file);
        case 'json':
            return json_decode(file_get_contents($file), true);
        default:
            if(function_exists($parse)){
                return $parse($file);
            }else{
                E(L('_NOT_SUPPORT_').':'.$ext);
            }
    }
}

/**
 * 瑙ｆ瀽yaml鏂囦欢杩斿洖涓�涓暟缁�
 * @param string $file 閰嶇疆鏂囦欢鍚�
 * @return array
 */
if (!function_exists('yaml_parse_file')) {
    function yaml_parse_file($file) {
        vendor('spyc.Spyc');
        return Spyc::YAMLLoad($file);
    }
}

/**
 * 鎶涘嚭寮傚父澶勭悊
 * @param string $msg 寮傚父娑堟伅
 * @param integer $code 寮傚父浠ｇ爜 榛樿涓�0
 * @throws Think\Exception
 * @return void
 */
function E($msg, $code=0) {
    throw new Think\Exception($msg, $code);
}

/**
 * 璁板綍鍜岀粺璁℃椂闂达紙寰锛夊拰鍐呭瓨浣跨敤鎯呭喌
 * 浣跨敤鏂规硶:
 * <code>
 * G('begin'); // 璁板綍寮�濮嬫爣璁颁綅
 * // ... 鍖洪棿杩愯浠ｇ爜
 * G('end'); // 璁板綍缁撴潫鏍囩浣�
 * echo G('begin','end',6); // 缁熻鍖洪棿杩愯鏃堕棿 绮剧‘鍒板皬鏁板悗6浣�
 * echo G('begin','end','m'); // 缁熻鍖洪棿鍐呭瓨浣跨敤鎯呭喌
 * 濡傛灉end鏍囪浣嶆病鏈夊畾涔夛紝鍒欎細鑷姩浠ュ綋鍓嶄綔涓烘爣璁颁綅
 * 鍏朵腑缁熻鍐呭瓨浣跨敤闇�瑕� MEMORY_LIMIT_ON 甯搁噺涓簍rue鎵嶆湁鏁�
 * </code>
 * @param string $start 寮�濮嬫爣绛�
 * @param string $end 缁撴潫鏍囩
 * @param integer|string $dec 灏忔暟浣嶆垨鑰卪
 * @return mixed
 */
function G($start,$end='',$dec=4) {
    static $_info       =   array();
    static $_mem        =   array();
    if(is_float($end)) { // 璁板綍鏃堕棿
        $_info[$start]  =   $end;
    }elseif(!empty($end)){ // 缁熻鏃堕棿鍜屽唴瀛樹娇鐢�
        if(!isset($_info[$end])) $_info[$end]       =  microtime(TRUE);
        if(MEMORY_LIMIT_ON && $dec=='m'){
            if(!isset($_mem[$end])) $_mem[$end]     =  memory_get_usage();
            return number_format(($_mem[$end]-$_mem[$start])/1024);
        }else{
            return number_format(($_info[$end]-$_info[$start]),$dec);
        }

    }else{ // 璁板綍鏃堕棿鍜屽唴瀛樹娇鐢�
        $_info[$start]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) $_mem[$start]           =  memory_get_usage();
    }
    return null;
}

/**
 * 鑾峰彇鍜岃缃瑷�瀹氫箟(涓嶅尯鍒嗗ぇ灏忓啓)
 * @param string|array $name 璇█鍙橀噺
 * @param mixed $value 璇█鍊兼垨鑰呭彉閲�
 * @return mixed
 */
function L($name=null, $value=null) {
    static $_lang = array();
    // 绌哄弬鏁拌繑鍥炴墍鏈夊畾涔�
    if (empty($name))
        return $_lang;
    // 鍒ゆ柇璇█鑾峰彇(鎴栬缃�)
    // 鑻ヤ笉瀛樺湪,鐩存帴杩斿洖鍏ㄥぇ鍐�$name
    if (is_string($name)) {
        $name   =   strtoupper($name);
        if (is_null($value)){
            return isset($_lang[$name]) ? $_lang[$name] : $name;
        }elseif(is_array($value)){
            // 鏀寔鍙橀噺
            $replace = array_keys($value);
            foreach($replace as &$v){
                $v = '{$'.$v.'}';
            }
            return str_replace($replace,$value,isset($_lang[$name]) ? $_lang[$name] : $name);        
        }
        $_lang[$name] = $value; // 璇█瀹氫箟
        return null;
    }
    // 鎵归噺瀹氫箟
    if (is_array($name))
        $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
    return null;
}

/**
 * 娣诲姞鍜岃幏鍙栭〉闈race璁板綍
 * @param string $value 鍙橀噺
 * @param string $label 鏍囩
 * @param string $level 鏃ュ織绾у埆
 * @param boolean $record 鏄惁璁板綍鏃ュ織
 * @return void|array
 */
function trace($value='[think]',$label='',$level='DEBUG',$record=false) {
    return Think\Think::trace($value,$label,$level,$record);
}

/**
 * 缂栬瘧鏂囦欢
 * @param string $filename 鏂囦欢鍚�
 * @return string
 */
function compile($filename) {
    $content    =   php_strip_whitespace($filename);
    $content    =   trim(substr($content, 5));
    // 鏇挎崲棰勭紪璇戞寚浠�
    $content    =   preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s', '', $content);
    if(0===strpos($content,'namespace')){
        $content    =   preg_replace('/namespace\s(.*?);/','namespace \\1{',$content,1);
    }else{
        $content    =   'namespace {'.$content;
    }
    if ('?>' == substr($content, -2))
        $content    = substr($content, 0, -2);
    return $content.'}';
}

/**
 * 鑾峰彇妯＄増鏂囦欢 鏍煎紡 璧勬簮://妯″潡@涓婚/鎺у埗鍣�/鎿嶄綔
 * @param string $template 妯＄増璧勬簮鍦板潃
 * @param string $layer 瑙嗗浘灞傦紙鐩綍锛夊悕绉�
 * @return string
 */
function T($template='',$layer=''){

    // 瑙ｆ瀽妯＄増璧勬簮鍦板潃
    if(false === strpos($template,'://')){
        $template   =   'http://'.str_replace(':', '/',$template);
    }
    $info   =   parse_url($template);
    $file   =   $info['host'].(isset($info['path'])?$info['path']:'');
    $module =   isset($info['user'])?$info['user'].'/':MODULE_NAME.'/';
    $extend =   $info['scheme'];
    $layer  =   $layer?$layer:C('DEFAULT_V_LAYER');

    // 鑾峰彇褰撳墠涓婚鐨勬ā鐗堣矾寰�
    $auto   =   C('AUTOLOAD_NAMESPACE');
    if($auto && isset($auto[$extend])){ // 鎵╁睍璧勬簮
        $baseUrl    =   $auto[$extend].$module.$layer.'/';
    }elseif(C('VIEW_PATH')){ 
        // 鏀瑰彉妯″潡瑙嗗浘鐩綍
        $baseUrl    =   C('VIEW_PATH');
    }elseif(defined('TMPL_PATH')){ 
        // 鎸囧畾鍏ㄥ眬瑙嗗浘鐩綍
        $baseUrl    =   TMPL_PATH.$module;
    }else{
        $baseUrl    =   APP_PATH.$module.$layer.'/';
    }

    // 鑾峰彇涓婚
    $theme  =   substr_count($file,'/')<2 ? C('DEFAULT_THEME') : '';

    // 鍒嗘瀽妯℃澘鏂囦欢瑙勫垯
    $depr   =   C('TMPL_FILE_DEPR');
    if('' == $file) {
        // 濡傛灉妯℃澘鏂囦欢鍚嶄负绌� 鎸夌収榛樿瑙勫垯瀹氫綅
        $file = CONTROLLER_NAME . $depr . ACTION_NAME;
    }elseif(false === strpos($file, '/')){
        $file = CONTROLLER_NAME . $depr . $file;
    }elseif('/' != $depr){
        $file   =   substr_count($file,'/')>1 ? substr_replace($file,$depr,strrpos($file,'/'),1) : str_replace('/', $depr, $file);
    }
    return $baseUrl.($theme?$theme.'/':'').$file.C('TMPL_TEMPLATE_SUFFIX');
}

/**
 * 鑾峰彇杈撳叆鍙傛暟 鏀寔杩囨护鍜岄粯璁ゅ��
 * 浣跨敤鏂规硶:
 * <code>
 * I('id',0); 鑾峰彇id鍙傛暟 鑷姩鍒ゆ柇get鎴栬�卲ost
 * I('post.name','','htmlspecialchars'); 鑾峰彇$_POST['name']
 * I('get.'); 鑾峰彇$_GET
 * </code>
 * @param string $name 鍙橀噺鐨勫悕绉� 鏀寔鎸囧畾绫诲瀷
 * @param mixed $default 涓嶅瓨鍦ㄧ殑鏃跺�欓粯璁ゅ��
 * @param mixed $filter 鍙傛暟杩囨护鏂规硶
 * @param mixed $datas 瑕佽幏鍙栫殑棰濆鏁版嵁婧�
 * @return mixed
 */
function I($name,$default='',$filter=null,$datas=null) {
	static $_PUT	=	null;
	if(strpos($name,'/')){ // 鎸囧畾淇グ绗�
		list($name,$type) 	=	explode('/',$name,2);
	}elseif(C('VAR_AUTO_STRING')){ // 榛樿寮哄埗杞崲涓哄瓧绗︿覆
        $type   =   's';
    }
    if(strpos($name,'.')) { // 鎸囧畾鍙傛暟鏉ユ簮
        list($method,$name) =   explode('.',$name,2);
    }else{ // 榛樿涓鸿嚜鍔ㄥ垽鏂�
        $method =   'param';
    }
    switch(strtolower($method)) {
        case 'get'     :   
        	$input =& $_GET;
        	break;
        case 'post'    :   
        	$input =& $_POST;
        	break;
        case 'put'     :   
        	if(is_null($_PUT)){
            	parse_str(file_get_contents('php://input'), $_PUT);
        	}
        	$input 	=	$_PUT;        
        	break;
        case 'param'   :
            switch($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $input  =  $_POST;
                    break;
                case 'PUT':
                	if(is_null($_PUT)){
                    	parse_str(file_get_contents('php://input'), $_PUT);
                	}
                	$input 	=	$_PUT;
                    break;
                default:
                    $input  =  $_GET;
            }
            break;
        case 'path'    :   
            $input  =   array();
            if(!empty($_SERVER['PATH_INFO'])){
                $depr   =   C('URL_PATHINFO_DEPR');
                $input  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));            
            }
            break;
        case 'request' :   
        	$input =& $_REQUEST;   
        	break;
        case 'session' :   
        	$input =& $_SESSION;   
        	break;
        case 'cookie'  :   
        	$input =& $_COOKIE;    
        	break;
        case 'server'  :   
        	$input =& $_SERVER;    
        	break;
        case 'globals' :   
        	$input =& $GLOBALS;    
        	break;
        case 'data'    :   
        	$input =& $datas;      
        	break;
        default:
            return null;
    }
    if(''==$name) { // 鑾峰彇鍏ㄩ儴鍙橀噺
        $data       =   $input;
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                $filters    =   explode(',',$filters);
            }
            foreach($filters as $filter){
                $data   =   array_map_recursive($filter,$data); // 鍙傛暟杩囨护
            }
        }
    }elseif(isset($input[$name])) { // 鍙栧�兼搷浣�
        $data       =   $input[$name];
        $filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
        if($filters) {
            if(is_string($filters)){
                if(0 === strpos($filters,'/')){
                    if(1 !== preg_match($filters,(string)$data)){
                        // 鏀寔姝ｅ垯楠岃瘉
                        return   isset($default) ? $default : null;
                    }
                }else{
                    $filters    =   explode(',',$filters);                    
                }
            }elseif(is_int($filters)){
                $filters    =   array($filters);
            }
            
            if(is_array($filters)){
                foreach($filters as $filter){
                    if(function_exists($filter)) {
                        $data   =   is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 鍙傛暟杩囨护
                    }else{
                        $data   =   filter_var($data,is_int($filter) ? $filter : filter_id($filter));
                        if(false === $data) {
                            return   isset($default) ? $default : null;
                        }
                    }
                }
            }
        }
        if(!empty($type)){
        	switch(strtolower($type)){
        		case 'a':	// 鏁扮粍
        			$data 	=	(array)$data;
        			break;
        		case 'd':	// 鏁板瓧
        			$data 	=	(int)$data;
        			break;
        		case 'f':	// 娴偣
        			$data 	=	(float)$data;
        			break;
        		case 'b':	// 甯冨皵
        			$data 	=	(boolean)$data;
        			break;
                case 's':   // 瀛楃涓�
                default:
                    $data   =   (string)$data;
        	}
        }
    }else{ // 鍙橀噺榛樿鍊�
        $data       =    isset($default)?$default:null;
    }
    is_array($data) && array_walk_recursive($data,'think_filter');
    return $data;
}

function array_map_recursive($filter, $data) {
    $result = array();
    foreach ($data as $key => $val) {
        $result[$key] = is_array($val)
         ? array_map_recursive($filter, $val)
         : call_user_func($filter, $val);
    }
    return $result;
 }

/**
 * 璁剧疆鍜岃幏鍙栫粺璁℃暟鎹�
 * 浣跨敤鏂规硶:
 * <code>
 * N('db',1); // 璁板綍鏁版嵁搴撴搷浣滄鏁�
 * N('read',1); // 璁板綍璇诲彇娆℃暟
 * echo N('db'); // 鑾峰彇褰撳墠椤甸潰鏁版嵁搴撶殑鎵�鏈夋搷浣滄鏁�
 * echo N('read'); // 鑾峰彇褰撳墠椤甸潰璇诲彇娆℃暟
 * </code>
 * @param string $key 鏍囪瘑浣嶇疆
 * @param integer $step 姝ヨ繘鍊�
 * @param boolean $save 鏄惁淇濆瓨缁撴灉
 * @return mixed
 */
function N($key, $step=0,$save=false) {
    static $_num    = array();
    if (!isset($_num[$key])) {
        $_num[$key] = (false !== $save)? S('N_'.$key) :  0;
    }
    if (empty($step)){
        return $_num[$key];
    }else{
        $_num[$key] = $_num[$key] + (int)$step;
    }
    if(false !== $save){ // 淇濆瓨缁撴灉
        S('N_'.$key,$_num[$key],$save);
    }
    return null;
}

/**
 * 瀛楃涓插懡鍚嶉鏍艰浆鎹�
 * type 0 灏咼ava椋庢牸杞崲涓篊鐨勯鏍� 1 灏咰椋庢牸杞崲涓篔ava鐨勯鏍�
 * @param string $name 瀛楃涓�
 * @param integer $type 杞崲绫诲瀷
 * @return string
 */
function parse_name($name, $type=0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function($match){return strtoupper($match[1]);}, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

/**
 * 浼樺寲鐨剅equire_once
 * @param string $filename 鏂囦欢鍦板潃
 * @return boolean
 */
function require_cache($filename) {
    static $_importFiles = array();
    if (!isset($_importFiles[$filename])) {
        if (file_exists_case($filename)) {
            require $filename;
            $_importFiles[$filename] = true;
        } else {
            $_importFiles[$filename] = false;
        }
    }
    return $_importFiles[$filename];
}

/**
 * 鍖哄垎澶у皬鍐欑殑鏂囦欢瀛樺湪鍒ゆ柇
 * @param string $filename 鏂囦欢鍦板潃
 * @return boolean
 */
function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN && APP_DEBUG) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

/**
 * 瀵煎叆鎵�闇�鐨勭被搴� 鍚宩ava鐨処mport 鏈嚱鏁版湁缂撳瓨鍔熻兘
 * @param string $class 绫诲簱鍛藉悕绌洪棿瀛楃涓�
 * @param string $baseUrl 璧峰璺緞
 * @param string $ext 瀵煎叆鐨勬枃浠舵墿灞曞悕
 * @return boolean
 */
function import($class, $baseUrl = '', $ext=EXT) {
    static $_file = array();
    $class = str_replace(array('.', '#'), array('/', '.'), $class);
    if (isset($_file[$class . $baseUrl]))
        return true;
    else
        $_file[$class . $baseUrl] = true;
    $class_strut     = explode('/', $class);
    if (empty($baseUrl)) {
        if ('@' == $class_strut[0] || MODULE_NAME == $class_strut[0]) {
            //鍔犺浇褰撳墠妯″潡鐨勭被搴�
            $baseUrl = MODULE_PATH;
            $class   = substr_replace($class, '', 0, strlen($class_strut[0]) + 1);
        }elseif ('Common' == $class_strut[0]) {
            //鍔犺浇鍏叡妯″潡鐨勭被搴�
            $baseUrl = COMMON_PATH;
            $class   = substr($class, 7);
        }elseif (in_array($class_strut[0],array('Think','Org','Behavior','Com','Vendor')) || is_dir(LIB_PATH.$class_strut[0])) {
            // 绯荤粺绫诲簱鍖呭拰绗笁鏂圭被搴撳寘
            $baseUrl = LIB_PATH;
        }else { // 鍔犺浇鍏朵粬妯″潡鐨勭被搴�
            $baseUrl = APP_PATH;
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl    .= '/';
    $classfile       = $baseUrl . $class . $ext;
    if (!class_exists(basename($class),false)) {
        // 濡傛灉绫讳笉瀛樺湪 鍒欏鍏ョ被搴撴枃浠�
        return require_cache($classfile);
    }
    return null;
}

/**
 * 鍩轰簬鍛藉悕绌洪棿鏂瑰紡瀵煎叆鍑芥暟搴�
 * load('@.Util.Array')
 * @param string $name 鍑芥暟搴撳懡鍚嶇┖闂村瓧绗︿覆
 * @param string $baseUrl 璧峰璺緞
 * @param string $ext 瀵煎叆鐨勬枃浠舵墿灞曞悕
 * @return void
 */
function load($name, $baseUrl='', $ext='.php') {
    $name = str_replace(array('.', '#'), array('/', '.'), $name);
    if (empty($baseUrl)) {
        if (0 === strpos($name, '@/')) {//鍔犺浇褰撳墠妯″潡鍑芥暟搴�
            $baseUrl    =   MODULE_PATH.'Common/';
            $name       =   substr($name, 2);
        } else { //鍔犺浇鍏朵粬妯″潡鍑芥暟搴�
            $array      =   explode('/', $name);
            $baseUrl    =   APP_PATH . array_shift($array).'/Common/';
            $name       =   implode('/',$array);
        }
    }
    if (substr($baseUrl, -1) != '/')
        $baseUrl       .= '/';
    require_cache($baseUrl . $name . $ext);
}

/**
 * 蹇�熷鍏ョ涓夋柟妗嗘灦绫诲簱 鎵�鏈夌涓夋柟妗嗘灦鐨勭被搴撴枃浠剁粺涓�鏀惧埌 绯荤粺鐨刅endor鐩綍涓嬮潰
 * @param string $class 绫诲簱
 * @param string $baseUrl 鍩虹鐩綍
 * @param string $ext 绫诲簱鍚庣紑
 * @return boolean
 */
function vendor($class, $baseUrl = '', $ext='.php') {
    if (empty($baseUrl))
        $baseUrl = VENDOR_PATH;
    return import($class, $baseUrl, $ext);
}

/**
 * 瀹炰緥鍖栨ā鍨嬬被 鏍煎紡 [璧勬簮://][妯″潡/]妯″瀷
 * @param string $name 璧勬簮鍦板潃
 * @param string $layer 妯″瀷灞傚悕绉�
 * @return Think\Model
 */
function D($name='',$layer='') {
    if(empty($name)) return new Think\Model;
    static $_model  =   array();
    $layer          =   $layer? : C('DEFAULT_M_LAYER');
    if(isset($_model[$name.$layer]))
        return $_model[$name.$layer];
    $class          =   parse_res_name($name,$layer);
    if(class_exists($class)) {
        $model      =   new $class(basename($name));
    }elseif(false === strpos($name,'/')){
        // 鑷姩鍔犺浇鍏叡妯″潡涓嬮潰鐨勬ā鍨�
        if(!C('APP_USE_NAMESPACE')){
            import('Common/'.$layer.'/'.$class);
        }else{
            $class      =   '\\Common\\'.$layer.'\\'.$name.$layer;
        }
        $model      =   class_exists($class)? new $class($name) : new Think\Model($name);
    }else {
        Think\Log::record('D鏂规硶瀹炰緥鍖栨病鎵惧埌妯″瀷绫�'.$class,Think\Log::NOTICE);
        $model      =   new Think\Model(basename($name));
    }
    $_model[$name.$layer]  =  $model;
    return $model;
}

/**
 * 瀹炰緥鍖栦竴涓病鏈夋ā鍨嬫枃浠剁殑Model
 * @param string $name Model鍚嶇О 鏀寔鎸囧畾鍩虹妯″瀷 渚嬪 MongoModel:User
 * @param string $tablePrefix 琛ㄥ墠缂�
 * @param mixed $connection 鏁版嵁搴撹繛鎺ヤ俊鎭�
 * @return Think\Model
 */
function M($name='', $tablePrefix='',$connection='') {
    static $_model  = array();
    if(strpos($name,':')) {
        list($class,$name)    =  explode(':',$name);
    }else{
        $class      =   'Think\\Model';
    }
    $guid           =   (is_array($connection)?implode('',$connection):$connection).$tablePrefix . $name . '_' . $class;
    if (!isset($_model[$guid]))
        $_model[$guid] = new $class($name,$tablePrefix,$connection);
    return $_model[$guid];
}

/**
 * 瑙ｆ瀽璧勬簮鍦板潃骞跺鍏ョ被搴撴枃浠�
 * 渚嬪 module/controller addon://module/behavior
 * @param string $name 璧勬簮鍦板潃 鏍煎紡锛歔鎵╁睍://][妯″潡/]璧勬簮鍚�
 * @param string $layer 鍒嗗眰鍚嶇О
 * @param integer $level 鎺у埗鍣ㄥ眰娆�
 * @return string
 */
function parse_res_name($name,$layer,$level=1){
    if(strpos($name,'://')) {// 鎸囧畾鎵╁睍璧勬簮
        list($extend,$name)  =   explode('://',$name);
    }else{
        $extend  =   '';
    }
    if(strpos($name,'/') && substr_count($name, '/')>=$level){ // 鎸囧畾妯″潡
        list($module,$name) =  explode('/',$name,2);
    }else{
        $module =   defined('MODULE_NAME') ? MODULE_NAME : '' ;
    }
    $array  =   explode('/',$name);
    if(!C('APP_USE_NAMESPACE')){
        $class  =   parse_name($name, 1);
        import($module.'/'.$layer.'/'.$class.$layer);
    }else{
        $class  =   $module.'\\'.$layer;
        foreach($array as $name){
            $class  .=   '\\'.parse_name($name, 1);
        }
        // 瀵煎叆璧勬簮绫诲簱
        if($extend){ // 鎵╁睍璧勬簮
            $class      =   $extend.'\\'.$class;
        }
    }
    return $class.$layer;
}

/**
 * 鐢ㄤ簬瀹炰緥鍖栬闂帶鍒跺櫒
 * @param string $name 鎺у埗鍣ㄥ悕
 * @param string $path 鎺у埗鍣ㄥ懡鍚嶇┖闂达紙璺緞锛�
 * @return Think\Controller|false
 */
function controller($name,$path=''){
    $layer  =   C('DEFAULT_C_LAYER');
    if(!C('APP_USE_NAMESPACE')){
        $class  =   parse_name($name, 1).$layer;
        import(MODULE_NAME.'/'.$layer.'/'.$class);
    }else{
        $class  =   ( $path ? basename(ADDON_PATH).'\\'.$path : MODULE_NAME ).'\\'.$layer;
        $array  =   explode('/',$name);
        foreach($array as $name){
            $class  .=   '\\'.parse_name($name, 1);
        }
        $class .=   $layer;
    }
    if(class_exists($class)) {
        return new $class();
    }else {
        return false;
    }
}

/**
 * 瀹炰緥鍖栧灞傛帶鍒跺櫒 鏍煎紡锛歔璧勬簮://][妯″潡/]鎺у埗鍣�
 * @param string $name 璧勬簮鍦板潃
 * @param string $layer 鎺у埗灞傚悕绉�
 * @param integer $level 鎺у埗鍣ㄥ眰娆�
 * @return Think\Controller|false
 */
function A($name,$layer='',$level=0) {
    static $_action = array();
    $layer  =   $layer? : C('DEFAULT_C_LAYER');
    $level  =   $level? : ($layer == C('DEFAULT_C_LAYER')?C('CONTROLLER_LEVEL'):1);
    if(isset($_action[$name.$layer]))
        return $_action[$name.$layer];
    
    $class  =   parse_res_name($name,$layer,$level);
    if(class_exists($class)) {
        $action             =   new $class();
        $_action[$name.$layer]     =   $action;
        return $action;
    }else {
        return false;
    }
}


/**
 * 杩滅▼璋冪敤鎺у埗鍣ㄧ殑鎿嶄綔鏂规硶 URL 鍙傛暟鏍煎紡 [璧勬簮://][妯″潡/]鎺у埗鍣�/鎿嶄綔
 * @param string $url 璋冪敤鍦板潃
 * @param string|array $vars 璋冪敤鍙傛暟 鏀寔瀛楃涓插拰鏁扮粍
 * @param string $layer 瑕佽皟鐢ㄧ殑鎺у埗灞傚悕绉�
 * @return mixed
 */
function R($url,$vars=array(),$layer='') {
    $info   =   pathinfo($url);
    $action =   $info['basename'];
    $module =   $info['dirname'];
    $class  =   A($module,$layer);
    if($class){
        if(is_string($vars)) {
            parse_str($vars,$vars);
        }
        return call_user_func_array(array(&$class,$action.C('ACTION_SUFFIX')),$vars);
    }else{
        return false;
    }
}

/**
 * 澶勭悊鏍囩鎵╁睍
 * @param string $tag 鏍囩鍚嶇О
 * @param mixed $params 浼犲叆鍙傛暟
 * @return void
 */
function tag($tag, &$params=NULL) {
    \Think\Hook::listen($tag,$params);
}

/**
 * 鎵ц鏌愪釜琛屼负
 * @param string $name 琛屼负鍚嶇О
 * @param string $tag 鏍囩鍚嶇О锛堣涓虹被鏃犻渶浼犲叆锛� 
 * @param Mixed $params 浼犲叆鐨勫弬鏁�
 * @return void
 */
function B($name, $tag='',&$params=NULL) {
    if(''==$tag){
        $name   .=  'Behavior';
    }
    return \Think\Hook::exec($name,$tag,$params);
}

/**
 * 鍘婚櫎浠ｇ爜涓殑绌虹櫧鍜屾敞閲�
 * @param string $content 浠ｇ爜鍐呭
 * @return string
 */
function strip_whitespace($content) {
    $stripStr   = '';
    //鍒嗘瀽php婧愮爜
    $tokens     = token_get_all($content);
    $last_space = false;
    for ($i = 0, $j = count($tokens); $i < $j; $i++) {
        if (is_string($tokens[$i])) {
            $last_space = false;
            $stripStr  .= $tokens[$i];
        } else {
            switch ($tokens[$i][0]) {
                //杩囨护鍚勭PHP娉ㄩ噴
                case T_COMMENT:
                case T_DOC_COMMENT:
                    break;
                //杩囨护绌烘牸
                case T_WHITESPACE:
                    if (!$last_space) {
                        $stripStr  .= ' ';
                        $last_space = true;
                    }
                    break;
                case T_START_HEREDOC:
                    $stripStr .= "<<<THINK\n";
                    break;
                case T_END_HEREDOC:
                    $stripStr .= "THINK;\n";
                    for($k = $i+1; $k < $j; $k++) {
                        if(is_string($tokens[$k]) && $tokens[$k] == ';') {
                            $i = $k;
                            break;
                        } else if($tokens[$k][0] == T_CLOSE_TAG) {
                            break;
                        }
                    }
                    break;
                default:
                    $last_space = false;
                    $stripStr  .= $tokens[$i][1];
            }
        }
    }
    return $stripStr;
}

/**
 * 鑷畾涔夊紓甯稿鐞�
 * @param string $msg 寮傚父娑堟伅
 * @param string $type 寮傚父绫诲瀷 榛樿涓篢hink\Exception
 * @param integer $code 寮傚父浠ｇ爜 榛樿涓�0
 * @return void
 */
function throw_exception($msg, $type='Think\\Exception', $code=0) {
    Think\Log::record('寤鸿浣跨敤E鏂规硶鏇夸唬throw_exception',Think\Log::NOTICE);
    if (class_exists($type, false))
        throw new $type($msg, $code);
    else
        Think\Think::halt($msg);        // 寮傚父绫诲瀷涓嶅瓨鍦ㄥ垯杈撳嚭閿欒淇℃伅瀛椾覆
}

/**
 * 娴忚鍣ㄥ弸濂界殑鍙橀噺杈撳嚭
 * @param mixed $var 鍙橀噺
 * @param boolean $echo 鏄惁杈撳嚭 榛樿涓篢rue 濡傛灉涓篺alse 鍒欒繑鍥炶緭鍑哄瓧绗︿覆
 * @param string $label 鏍囩 榛樿涓虹┖
 * @param boolean $strict 鏄惁涓ヨ皑 榛樿涓簍rue
 * @return void|string
 */
function dump($var, $echo=true, $label=null, $strict=true) {
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}

/**
 * 璁剧疆褰撳墠椤甸潰鐨勫竷灞�
 * @param string|false $layout 甯冨眬鍚嶇О 涓篺alse鐨勬椂鍊欒〃绀哄叧闂竷灞�
 * @return void
 */
function layout($layout) {
    if(false !== $layout) {
        // 寮�鍚竷灞�
        C('LAYOUT_ON',true);
        if(is_string($layout)) { // 璁剧疆鏂扮殑甯冨眬妯℃澘
            C('LAYOUT_NAME',$layout);
        }
    }else{// 涓存椂鍏抽棴甯冨眬
        C('LAYOUT_ON',false);
    }
}

/**
 * URL缁勮 鏀寔涓嶅悓URL妯″紡
 * @param string $url URL琛ㄨ揪寮忥紝鏍煎紡锛�'[妯″潡/鎺у埗鍣�/鎿嶄綔#閿氱偣@鍩熷悕]?鍙傛暟1=鍊�1&鍙傛暟2=鍊�2...'
 * @param string|array $vars 浼犲叆鐨勫弬鏁帮紝鏀寔鏁扮粍鍜屽瓧绗︿覆
 * @param string|boolean $suffix 浼潤鎬佸悗缂�锛岄粯璁や负true琛ㄧず鑾峰彇閰嶇疆鍊�
 * @param boolean $domain 鏄惁鏄剧ず鍩熷悕
 * @return string
 */
function U($url='',$vars='',$suffix=true,$domain=false) {
    // 瑙ｆ瀽URL
    $info   =  parse_url($url);
    $url    =  !empty($info['path'])?$info['path']:ACTION_NAME;
    if(isset($info['fragment'])) { // 瑙ｆ瀽閿氱偣
        $anchor =   $info['fragment'];
        if(false !== strpos($anchor,'?')) { // 瑙ｆ瀽鍙傛暟
            list($anchor,$info['query']) = explode('?',$anchor,2);
        }        
        if(false !== strpos($anchor,'@')) { // 瑙ｆ瀽鍩熷悕
            list($anchor,$host)    =   explode('@',$anchor, 2);
        }
    }elseif(false !== strpos($url,'@')) { // 瑙ｆ瀽鍩熷悕
        list($url,$host)    =   explode('@',$info['path'], 2);
    }
    // 瑙ｆ瀽瀛愬煙鍚�
    if(isset($host)) {
        $domain = $host.(strpos($host,'.')?'':strstr($_SERVER['HTTP_HOST'],'.'));
    }elseif($domain===true){
        $domain = $_SERVER['HTTP_HOST'];
        if(C('APP_SUB_DOMAIN_DEPLOY') ) { // 寮�鍚瓙鍩熷悕閮ㄧ讲
            $domain = $domain=='localhost'?'localhost':'www'.strstr($_SERVER['HTTP_HOST'],'.');
            // '瀛愬煙鍚�'=>array('妯″潡[/鎺у埗鍣╙');
            foreach (C('APP_SUB_DOMAIN_RULES') as $key => $rule) {
                $rule   =   is_array($rule)?$rule[0]:$rule;
                if(false === strpos($key,'*') && 0=== strpos($url,$rule)) {
                    $domain = $key.strstr($domain,'.'); // 鐢熸垚瀵瑰簲瀛愬煙鍚�
                    $url    =  substr_replace($url,'',0,strlen($rule));
                    break;
                }
            }
        }
    }

    // 瑙ｆ瀽鍙傛暟
    if(is_string($vars)) { // aaa=1&bbb=2 杞崲鎴愭暟缁�
        parse_str($vars,$vars);
    }elseif(!is_array($vars)){
        $vars = array();
    }
    if(isset($info['query'])) { // 瑙ｆ瀽鍦板潃閲岄潰鍙傛暟 鍚堝苟鍒皏ars
        parse_str($info['query'],$params);
        $vars = array_merge($params,$vars);
    }
    
    // URL缁勮
    $depr       =   C('URL_PATHINFO_DEPR');
    $urlCase    =   C('URL_CASE_INSENSITIVE');
    if($url) {
        if(0=== strpos($url,'/')) {// 瀹氫箟璺敱
            $route      =   true;
            $url        =   substr($url,1);
            if('/' != $depr) {
                $url    =   str_replace('/',$depr,$url);
            }
        }else{
            if('/' != $depr) { // 瀹夊叏鏇挎崲
                $url    =   str_replace('/',$depr,$url);
            }
            // 瑙ｆ瀽妯″潡銆佹帶鍒跺櫒鍜屾搷浣�
            $url        =   trim($url,$depr);
            $path       =   explode($depr,$url);
            $var        =   array();
            $varModule      =   C('VAR_MODULE');
            $varController  =   C('VAR_CONTROLLER');
            $varAction      =   C('VAR_ACTION');
            $var[$varAction]       =   !empty($path)?array_pop($path):ACTION_NAME;
            $var[$varController]   =   !empty($path)?array_pop($path):CONTROLLER_NAME;
            if($maps = C('URL_ACTION_MAP')) {
                if(isset($maps[strtolower($var[$varController])])) {
                    $maps    =   $maps[strtolower($var[$varController])];
                    if($action = array_search(strtolower($var[$varAction]),$maps)){
                        $var[$varAction] = $action;
                    }
                }
            }
            if($maps = C('URL_CONTROLLER_MAP')) {
                if($controller = array_search(strtolower($var[$varController]),$maps)){
                    $var[$varController] = $controller;
                }
            }
            if($urlCase) {
                $var[$varController]   =   parse_name($var[$varController]);
            }
            $module =   '';
            
            if(!empty($path)) {
                $var[$varModule]    =   implode($depr,$path);
            }else{
                if(C('MULTI_MODULE')) {
                    if(MODULE_NAME != C('DEFAULT_MODULE') || !C('MODULE_ALLOW_LIST')){
                        $var[$varModule]=   MODULE_NAME;
                    }
                }
            }
            if($maps = C('URL_MODULE_MAP')) {
                if($_module = array_search(strtolower($var[$varModule]),$maps)){
                    $var[$varModule] = $_module;
                }
            }
            if(isset($var[$varModule])){
                $module =   $var[$varModule];
                unset($var[$varModule]);
            }
            
        }
    }

    if(C('URL_MODEL') == 0) { // 鏅�氭ā寮廢RL杞崲
        $url        =   __APP__.'?'.C('VAR_MODULE')."={$module}&".http_build_query(array_reverse($var));
        if($urlCase){
            $url    =   strtolower($url);
        }        
        if(!empty($vars)) {
            $vars   =   http_build_query($vars);
            $url   .=   '&'.$vars;
        }
    }else{ // PATHINFO妯″紡鎴栬�呭吋瀹筓RL妯″紡
        if(isset($route)) {
            $url    =   __APP__.'/'.rtrim($url,$depr);
        }else{
            $module =   (defined('BIND_MODULE') && BIND_MODULE==$module )? '' : $module;
            $url    =   __APP__.'/'.($module?$module.MODULE_PATHINFO_DEPR:'').implode($depr,array_reverse($var));
        }
        if($urlCase){
            $url    =   strtolower($url);
        }
        if(!empty($vars)) { // 娣诲姞鍙傛暟
            foreach ($vars as $var => $val){
                if('' !== trim($val))   $url .= $depr . $var . $depr . urlencode($val);
            }                
        }
        if($suffix) {
            $suffix   =  $suffix===true?C('URL_HTML_SUFFIX'):$suffix;
            if($pos = strpos($suffix, '|')){
                $suffix = substr($suffix, 0, $pos);
            }
            if($suffix && '/' != substr($url,-1)){
                $url  .=  '.'.ltrim($suffix,'.');
            }
        }
    }
    if(isset($anchor)){
        $url  .= '#'.$anchor;
    }
    if($domain) {
        $url   =  (is_ssl()?'https://':'http://').$domain.$url;
    }
    return $url;
}

/**
 * 娓叉煋杈撳嚭Widget
 * @param string $name Widget鍚嶇О
 * @param array $data 浼犲叆鐨勫弬鏁�
 * @return void
 */
function W($name, $data=array()) {
    return R($name,$data,'Widget');
}

/**
 * 鍒ゆ柇鏄惁SSL鍗忚
 * @return boolean
 */
function is_ssl() {
    if(isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))){
        return true;
    }elseif(isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'] )) {
        return true;
    }
    return false;
}

/**
 * URL閲嶅畾鍚�
 * @param string $url 閲嶅畾鍚戠殑URL鍦板潃
 * @param integer $time 閲嶅畾鍚戠殑绛夊緟鏃堕棿锛堢锛�
 * @param string $msg 閲嶅畾鍚戝墠鐨勬彁绀轰俊鎭�
 * @return void
 */
function redirect($url, $time=0, $msg='') {
    //澶氳URL鍦板潃鏀寔
    $url        = str_replace(array("\n", "\r"), '', $url);
    if (empty($msg))
        $msg    = "绯荤粺灏嗗湪{$time}绉掍箣鍚庤嚜鍔ㄨ烦杞埌{$url}锛�";
    if (!headers_sent()) {
        // redirect
        if (0 === $time) {
            header('Location: ' . $url);
        } else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    } else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if ($time != 0)
            $str .= $msg;
        exit($str);
    }
}

/**
 * 缂撳瓨绠＄悊
 * @param mixed $name 缂撳瓨鍚嶇О锛屽鏋滀负鏁扮粍琛ㄧず杩涜缂撳瓨璁剧疆
 * @param mixed $value 缂撳瓨鍊�
 * @param mixed $options 缂撳瓨鍙傛暟
 * @return mixed
 */
function S($name,$value='',$options=null) {
    static $cache   =   '';
    if(is_array($options)){
        // 缂撳瓨鎿嶄綔鐨勫悓鏃跺垵濮嬪寲
        $type       =   isset($options['type'])?$options['type']:'';
        $cache      =   Think\Cache::getInstance($type,$options);
    }elseif(is_array($name)) { // 缂撳瓨鍒濆鍖�
        $type       =   isset($name['type'])?$name['type']:'';
        $cache      =   Think\Cache::getInstance($type,$name);
        return $cache;
    }elseif(empty($cache)) { // 鑷姩鍒濆鍖�
        $cache      =   Think\Cache::getInstance();
    }
    if(''=== $value){ // 鑾峰彇缂撳瓨
        return $cache->get($name);
    }elseif(is_null($value)) { // 鍒犻櫎缂撳瓨
        return $cache->rm($name);
    }else { // 缂撳瓨鏁版嵁
        if(is_array($options)) {
            $expire     =   isset($options['expire'])?$options['expire']:NULL;
        }else{
            $expire     =   is_numeric($options)?$options:NULL;
        }
        return $cache->set($name, $value, $expire);
    }
}

/**
 * 蹇�熸枃浠舵暟鎹鍙栧拰淇濆瓨 閽堝绠�鍗曠被鍨嬫暟鎹� 瀛楃涓层�佹暟缁�
 * @param string $name 缂撳瓨鍚嶇О
 * @param mixed $value 缂撳瓨鍊�
 * @param string $path 缂撳瓨璺緞
 * @return mixed
 */
function F($name, $value='', $path=DATA_PATH) {
    static $_cache  =   array();
    $filename       =   $path . $name . '.php';
    if ('' !== $value) {
        if (is_null($value)) {
            // 鍒犻櫎缂撳瓨
            if(false !== strpos($name,'*')){
                return false; // TODO 
            }else{
                unset($_cache[$name]);
                return Think\Storage::unlink($filename,'F');
            }
        } else {
            Think\Storage::put($filename,serialize($value),'F');
            // 缂撳瓨鏁版嵁
            $_cache[$name]  =   $value;
            return null;
        }
    }
    // 鑾峰彇缂撳瓨鏁版嵁
    if (isset($_cache[$name]))
        return $_cache[$name];
    if (Think\Storage::has($filename,'F')){
        $value      =   unserialize(Think\Storage::read($filename,'F'));
        $_cache[$name]  =   $value;
    } else {
        $value          =   false;
    }
    return $value;
}

/**
 * 鏍规嵁PHP鍚勭绫诲瀷鍙橀噺鐢熸垚鍞竴鏍囪瘑鍙�
 * @param mixed $mix 鍙橀噺
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

/**
 * XML缂栫爜
 * @param mixed $data 鏁版嵁
 * @param string $root 鏍硅妭鐐瑰悕
 * @param string $item 鏁板瓧绱㈠紩鐨勫瓙鑺傜偣鍚�
 * @param string $attr 鏍硅妭鐐瑰睘鎬�
 * @param string $id   鏁板瓧绱㈠紩瀛愯妭鐐筴ey杞崲鐨勫睘鎬у悕
 * @param string $encoding 鏁版嵁缂栫爜
 * @return string
 */
function xml_encode($data, $root='think', $item='item', $attr='', $id='id', $encoding='utf-8') {
    if(is_array($attr)){
        $_attr = array();
        foreach ($attr as $key => $value) {
            $_attr[] = "{$key}=\"{$value}\"";
        }
        $attr = implode(' ', $_attr);
    }
    $attr   = trim($attr);
    $attr   = empty($attr) ? '' : " {$attr}";
    $xml    = "<?xml version=\"1.0\" encoding=\"{$encoding}\"?>";
    $xml   .= "<{$root}{$attr}>";
    $xml   .= data_to_xml($data, $item, $id);
    $xml   .= "</{$root}>";
    return $xml;
}

/**
 * 鏁版嵁XML缂栫爜
 * @param mixed  $data 鏁版嵁
 * @param string $item 鏁板瓧绱㈠紩鏃剁殑鑺傜偣鍚嶇О
 * @param string $id   鏁板瓧绱㈠紩key杞崲涓虹殑灞炴�у悕
 * @return string
 */
function data_to_xml($data, $item='item', $id='id') {
    $xml = $attr = '';
    foreach ($data as $key => $val) {
        if(is_numeric($key)){
            $id && $attr = " {$id}=\"{$key}\"";
            $key  = $item;
        }
        $xml    .=  "<{$key}{$attr}>";
        $xml    .=  (is_array($val) || is_object($val)) ? data_to_xml($val, $item, $id) : $val;
        $xml    .=  "</{$key}>";
    }
    return $xml;
}

/**
 * session绠＄悊鍑芥暟
 * @param string|array $name session鍚嶇О 濡傛灉涓烘暟缁勫垯琛ㄧず杩涜session璁剧疆
 * @param mixed $value session鍊�
 * @return mixed
 */
function session($name='',$value='') {
    $prefix   =  C('SESSION_PREFIX');
    if(is_array($name)) { // session鍒濆鍖� 鍦╯ession_start 涔嬪墠璋冪敤
        if(isset($name['prefix'])) C('SESSION_PREFIX',$name['prefix']);
        if(C('VAR_SESSION_ID') && isset($_REQUEST[C('VAR_SESSION_ID')])){
            session_id($_REQUEST[C('VAR_SESSION_ID')]);
        }elseif(isset($name['id'])) {
            session_id($name['id']);
        }
        if('common' == APP_MODE){ // 鍏跺畠妯″紡鍙兘涓嶆敮鎸�
            ini_set('session.auto_start', 0);
        }
        if(isset($name['name']))            session_name($name['name']);
        if(isset($name['path']))            session_save_path($name['path']);
        if(isset($name['domain']))          ini_set('session.cookie_domain', $name['domain']);
        if(isset($name['expire']))          {
            ini_set('session.gc_maxlifetime',   $name['expire']);
            ini_set('session.cookie_lifetime',  $name['expire']);
        }
        if(isset($name['use_trans_sid']))   ini_set('session.use_trans_sid', $name['use_trans_sid']?1:0);
        if(isset($name['use_cookies']))     ini_set('session.use_cookies', $name['use_cookies']?1:0);
        if(isset($name['cache_limiter']))   session_cache_limiter($name['cache_limiter']);
        if(isset($name['cache_expire']))    session_cache_expire($name['cache_expire']);
        if(isset($name['type']))            C('SESSION_TYPE',$name['type']);
        if(C('SESSION_TYPE')) { // 璇诲彇session椹卞姩
            $type   =   C('SESSION_TYPE');
            $class  =   strpos($type,'\\')? $type : 'Think\\Session\\Driver\\'. ucwords(strtolower($type));
            $hander =   new $class();
            session_set_save_handler(
                array(&$hander,"open"), 
                array(&$hander,"close"), 
                array(&$hander,"read"), 
                array(&$hander,"write"), 
                array(&$hander,"destroy"), 
                array(&$hander,"gc")); 
        }
        // 鍚姩session
        if(C('SESSION_AUTO_START')){
			session_start();
		}
    }elseif('' === $value){ 
        if(''===$name){
            // 鑾峰彇鍏ㄩ儴鐨剆ession
            return $prefix ? $_SESSION[$prefix] : $_SESSION;
        }elseif(0===strpos($name,'[')) { // session 鎿嶄綔
            if('[pause]'==$name){ // 鏆傚仠session
                session_write_close();
            }elseif('[start]'==$name){ // 鍚姩session
                session_start();
            }elseif('[destroy]'==$name){ // 閿�姣乻ession
                $_SESSION =  array();
                session_unset();
                session_destroy();
            }elseif('[regenerate]'==$name){ // 閲嶆柊鐢熸垚id
                session_regenerate_id();
            }
        }elseif(0===strpos($name,'?')){ // 妫�鏌ession
            $name   =  substr($name,1);
            if(strpos($name,'.')){ // 鏀寔鏁扮粍
                list($name1,$name2) =   explode('.',$name);
                return $prefix?isset($_SESSION[$prefix][$name1][$name2]):isset($_SESSION[$name1][$name2]);
            }else{
                return $prefix?isset($_SESSION[$prefix][$name]):isset($_SESSION[$name]);
            }
        }elseif(is_null($name)){ // 娓呯┖session
            if($prefix) {
                unset($_SESSION[$prefix]);
            }else{
                $_SESSION = array();
            }
        }elseif($prefix){ // 鑾峰彇session
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;  
            }else{
                return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;                
            }            
        }else{
            if(strpos($name,'.')){
                list($name1,$name2) =   explode('.',$name);
                return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;  
            }else{
                return isset($_SESSION[$name])?$_SESSION[$name]:null;
            }            
        }
    }elseif(is_null($value)){ // 鍒犻櫎session
        if(strpos($name,'.')){
            list($name1,$name2) =   explode('.',$name);
            if($prefix){
                unset($_SESSION[$prefix][$name1][$name2]);
            }else{
                unset($_SESSION[$name1][$name2]);
            }
        }else{
            if($prefix){
                unset($_SESSION[$prefix][$name]);
            }else{
                unset($_SESSION[$name]);
            }
        }
    }else{ // 璁剧疆session
		if(strpos($name,'.')){
			list($name1,$name2) =   explode('.',$name);
			if($prefix){
				$_SESSION[$prefix][$name1][$name2]   =  $value;
			}else{
				$_SESSION[$name1][$name2]  =  $value;
			}
		}else{
			if($prefix){
				$_SESSION[$prefix][$name]   =  $value;
			}else{
				$_SESSION[$name]  =  $value;
			}
		}
    }
    return null;
}

/**
 * Cookie 璁剧疆銆佽幏鍙栥�佸垹闄�
 * @param string $name cookie鍚嶇О
 * @param mixed $value cookie鍊�
 * @param mixed $option cookie鍙傛暟
 * @return mixed
 */
function cookie($name='', $value='', $option=null) {
    // 榛樿璁剧疆
    $config = array(
        'prefix'    =>  C('COOKIE_PREFIX'), // cookie 鍚嶇О鍓嶇紑
        'expire'    =>  C('COOKIE_EXPIRE'), // cookie 淇濆瓨鏃堕棿
        'path'      =>  C('COOKIE_PATH'), // cookie 淇濆瓨璺緞
        'domain'    =>  C('COOKIE_DOMAIN'), // cookie 鏈夋晥鍩熷悕
        'secure'    =>  C('COOKIE_SECURE'), //  cookie 鍚敤瀹夊叏浼犺緭
        'httponly'  =>  C('COOKIE_HTTPONLY'), // httponly璁剧疆
    );
    // 鍙傛暟璁剧疆(浼氳鐩栭粰璁よ缃�)
    if (!is_null($option)) {
        if (is_numeric($option))
            $option = array('expire' => $option);
        elseif (is_string($option))
            parse_str($option, $option);
        $config     = array_merge($config, array_change_key_case($option));
    }
    if(!empty($config['httponly'])){
        ini_set("session.cookie_httponly", 1);
    }
    // 娓呴櫎鎸囧畾鍓嶇紑鐨勬墍鏈塩ookie
    if (is_null($name)) {
        if (empty($_COOKIE))
            return null;
        // 瑕佸垹闄ょ殑cookie鍓嶇紑锛屼笉鎸囧畾鍒欏垹闄onfig璁剧疆鐨勬寚瀹氬墠缂�
        $prefix = empty($value) ? $config['prefix'] : $value;
        if (!empty($prefix)) {// 濡傛灉鍓嶇紑涓虹┖瀛楃涓插皢涓嶄綔澶勭悊鐩存帴杩斿洖
            foreach ($_COOKIE as $key => $val) {
                if (0 === stripos($key, $prefix)) {
                    setcookie($key, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
                    unset($_COOKIE[$key]);
                }
            }
        }
        return null;
    }elseif('' === $name){
        // 鑾峰彇鍏ㄩ儴鐨刢ookie
        return $_COOKIE;
    }
    $name = $config['prefix'] . str_replace('.', '_', $name);
    if ('' === $value) {
        if(isset($_COOKIE[$name])){
            $value =    $_COOKIE[$name];
            if(0===strpos($value,'think:')){
                $value  =   substr($value,6);
                return array_map('urldecode',json_decode(MAGIC_QUOTES_GPC?stripslashes($value):$value,true));
            }else{
                return $value;
            }
        }else{
            return null;
        }
    } else {
        if (is_null($value)) {
            setcookie($name, '', time() - 3600, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
            unset($_COOKIE[$name]); // 鍒犻櫎鎸囧畾cookie
        } else {
            // 璁剧疆cookie
            if(is_array($value)){
                $value  = 'think:'.json_encode(array_map('urlencode',$value));
            }
            $expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
            setcookie($name, $value, $expire, $config['path'], $config['domain'],$config['secure'],$config['httponly']);
            $_COOKIE[$name] = $value;
        }
    }
    return null;
}

/**
 * 鍔犺浇鍔ㄦ�佹墿灞曟枃浠�
 * @var string $path 鏂囦欢璺緞
 * @return void
 */
function load_ext_file($path) {
    // 鍔犺浇鑷畾涔夊閮ㄦ枃浠�
    if($files = C('LOAD_EXT_FILE')) {
        $files      =  explode(',',$files);
        foreach ($files as $file){
            $file   = $path.'Common/'.$file.'.php';
            if(is_file($file)) include $file;
        }
    }
    // 鍔犺浇鑷畾涔夌殑鍔ㄦ�侀厤缃枃浠�
    if($configs = C('LOAD_EXT_CONFIG')) {
        if(is_string($configs)) $configs =  explode(',',$configs);
        foreach ($configs as $key=>$config){
            $file   = is_file($config)? $config : $path.'Conf/'.$config.CONF_EXT;
            if(is_file($file)) {
                is_numeric($key)?C(load_config($file)):C($key,load_config($file));
            }
        }
    }
}

/**
 * 鑾峰彇瀹㈡埛绔疘P鍦板潃
 * @param integer $type 杩斿洖绫诲瀷 0 杩斿洖IP鍦板潃 1 杩斿洖IPV4鍦板潃鏁板瓧
 * @param boolean $adv 鏄惁杩涜楂樼骇妯″紡鑾峰彇锛堟湁鍙兘琚吉瑁咃級 
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP鍦板潃鍚堟硶楠岃瘉
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 鍙戦�丠TTP鐘舵��
 * @param integer $code 鐘舵�佺爜
 * @return void
 */
function send_http_status($code) {
    static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Moved Temporarily ',  // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
    );
    if(isset($_status[$code])) {
        header('HTTP/1.1 '.$code.' '.$_status[$code]);
        // 纭繚FastCGI妯″紡涓嬫甯�
        header('Status:'.$code.' '.$_status[$code]);
    }
}

function think_filter(&$value){
	// TODO 鍏朵粬瀹夊叏杩囨护

	// 杩囨护鏌ヨ鐗规畩瀛楃
    if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN)$/i',$value)){
        $value .= ' ';
    }
}

// 涓嶅尯鍒嗗ぇ灏忓啓鐨刬n_array瀹炵幇
function in_array_case($value,$array){
    return in_array(strtolower($value),array_map('strtolower',$array));
}
