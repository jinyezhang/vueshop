<?php
//获取项目目录
function getHost(){
	$snarr=@explode("/",$_SERVER["SCRIPT_NAME"]);
	array_pop($snarr);
	array_shift($snarr);
	foreach($snarr as $v){
		$urlpath.=$v."/";
	}
	if($snarr[0]!=""){
		$rurlpath="/".rtrim($urlpath,"/");
	}
	$domain_host="//".$_SERVER['HTTP_HOST'].$rurlpath;
	return $domain_host;
}

//截取字符
function strsub($string, $sublen = 80, $etc = '...',$code='utf-8')
{
	if($code == 'utf-8')
	{
		$pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
		preg_match_all($pa, $string, $t_string);
 
		if(count($t_string[0]) - $start > $sublen) return join('', array_slice($t_string[0], $start, $sublen)).$etc;
		return join('', array_slice($t_string[0], $start, $sublen));
	}
	else
	{
		$start = $start*2;
		$sublen = $sublen*2;
		$strlen = strlen($string);
		$tmpstr = '';
 
		for($i=0; $i<$strlen; $i++)
		{
			if($i>=$start && $i<($start+$sublen))
			{
				if(ord(substr($string, $i, 1))>129)
				{
					$tmpstr.= substr($string, $i, 2);
				}
				else
				{
					$tmpstr.= substr($string, $i, 1);
				}
			}
			if(ord(substr($string, $i, 1))>129) $i++;
		}
		if(strlen($tmpstr)<$strlen ) $tmpstr.= $etc;
		return $tmpstr;
	}

}

//网站gzip压缩
function ob_gzip(){
		ob_start("ob_gzhandler");	
}

//去除空格
function delspace($str) {
	$str = trim($str);
	$str = str_replace("\t","",$str);
	$str = str_replace("&nbsp;","",$str);
	$str = str_replace("\r\n","",$str);
	$str = str_replace("\r","",$str);
	$str = str_replace("\n","",$str);
	return trim($str);
}
//去除换行\r\n
function delwrap($str){
	$str1=str_replace(array("\r\n","\r","\n"),array("","",""),$str);
	return $str1;
}
//出库
function faceDecode($str){
	return preg_replace_callback('/@E(.{6}==)/', function($r) {return base64_decode($r[1]);}, $str);
}

//入库
function faceEncode($str){
	return preg_replace_callback('/[\xf0-\xf7].{3}/', function($r) { return '@E' . base64_encode($r[0]);}, $str);
}
//生成唯一id
function uniqueId(){
	$qidarr=@explode(".",uniqid('',true));
	$qid=rand(1,9).$qidarr[1];
	return $qid;	
}

//把&lt;等格式的字符转换成<这种形式
function chartohtml($str) {
	$str=str_replace('&ldquo;','“',$str);
	$str=str_replace('&rdquo;','”',$str);
	$str=str_replace('&quot;','"',$str);
	$str=str_replace("&lsquo;",'‘',$str);
	$str=str_replace("&rsquo;",'’',$str);
	$str=str_replace("&middot;",'·',$str);
	$str=str_replace("&bull;",'•',$str);
	$str=str_replace("&lt;",'<',$str);
	$str=str_replace("&gt;",'>',$str);
	$str=str_replace("&amp;",'&',$str);
	$str=str_replace("&mdash;",'—',$str);
	$str=str_replace("&alpha;",'α',$str);
	return $str;
}

//防整型注入
function get_int($str){
	$str=floor(floatval($str));
	if (!is_numeric($str)){
		header("location:".$_SERVER['HTTP_REFERER']."");
		exit;
	}
	return $str;
}

//防字符串注入
function get_str($str,$chk=0) {    
    if (ini_get('magic_quotes_gpc')) {
		if($chk==1){
			return $str;
		}else{ 
			return htmlspecialchars($str); 
		}
	} else {
		if($chk==1){
			return addslashes($str);
		}else{
			return addslashes(htmlspecialchars($str)); 
		}
	}    
}

function htmlcode($content){
	$content=str_replace(array(" ","\n","\n\r","\r"),array("&nbsp;","<br>","<br>",""),$content);
	return $content;
}

function textcode($content){
	$content=str_replace(array("&nbsp;","<br>","<br>","<br>"),array(" ","\n","\n\r","\r"),$content);
	return $content;
}

//禁止外部提交表单
function disPost(){
	$From_url=strval($_SERVER["HTTP_REFERER"]);
	$Serv_url=strval($_SERVER["SERVER_NAME"]);
	if(substr($From_url,7,strlen($Serv_url)) != $Serv_url){
		echo "禁止外部提交！";
		exit;
	}	
}
	
function filterCode($str){
	if($str!=""){
		$content=preg_replace("/<iframe(.+?)>/is","",$str);
		$content=preg_replace("/<\/iframe>/is","",$content);
		$content=preg_replace("/<script(.+?)>(.+?)<\/script>/is","",$content);
		$content=preg_replace("/<script(.+?)>/is","",$content);
		$content=preg_replace("/<\/script>/is","",$content);
		return $content;
	}else{
		return false;	
	}
}

//前台自动获取地址
function urlpath(){
	$url_string = $_SERVER['QUERY_STRING'];
	parse_str($url_string, $urls); 
	foreach ($urls as $field => $value){  
		$add[] = $field.'='.rawurlencode($value); //rawurlencode 将字符串编码成 URL 专用格式 
	}
	$urlthis=@implode("&",$add);
	return $urlthis;	
}

//防止恶意灌水
function irrigation(){
	$_SESSION["ippost"]=time(); //登记填写时的时间
	
	if(strtoupper($_SERVER['REQUEST_METHOD'])!="POST"){
		die ("错误：请勿在外部提交。");//检查页面获得方法是否为POST
	}
	if(!isset($_SESSION["ippost"])){
		die ("错误：请勿在外部提交。");//检查留言填写时的时间
	}
	if(isset($_SESSION["iptime"]) && (time()-$_SESSION["iptime"]<10)){
		echo "<script>alert('请稍后再提交!');history.go(-1);</script>"; //检查留言间隔
		exit;
	}
	unset($_SESSION["ippost"]); //注销ippost变量以防止一次进入填写页面多次进行提交
	
	$_SESSION["iptime"]=time(); //登记发送留言的时间，防止灌水或恶意攻击
}

//内容页分页
function contentPage($content,$url,$prev,$next,$first,$last){
	$expcon=explode('<div style="page-break-after: always;"><span style="display: none;">&nbsp;</span></div>',$content);//$content是内容
	$expcount=count($expcon);
	$page=isset($_GET["page"])?intval($_GET["page"]):0;
	$info="<a href={$url}&amp;page=0>".$first."</a>&nbsp;";
	
	if($page>0){
		$info.="<a href={$url}&amp;page=".($page-1).">".$prev."</a>&nbsp;";
	}else{
		$info.="".$prev."&nbsp;";
	}
	
	for($i=1;$i<$expcount+1;$i++){
		if(($i-1)==$page){
			$info.="{$i}";
		}else{
			$info.="<a href={$url}&amp;page=".($i-1).">[".$i."]</a>";
		}
		if($i<$expcount){
			$info.="&nbsp;|&nbsp;";
		}
	}
	
	if($page<$expcount-1){
		$info.="&nbsp;<a href={$url}&amp;page=".($page+1).">".$next."</a>";
	}else{
		$info.="&nbsp;".$next."";
	}
	
	$info.="&nbsp;<a href={$url}&amp;page=".($expcount-1).">".$last."</a>";
	
	$expdata=array(
		"info"=>$info,
		"expcon"=>$expcon[$page]
	);
	
	return $expdata;
}

//根据某年的第几周星期几返回具体日期
function getd($week){
  $yearstr=date("Y").'-1-1';
  $weeknumstr=date("W")-1;
  $weekw=date('W',strtotime($yearstr));
  $weekx=date('w',strtotime($yearstr));
  $dnum=0;

  if($weekw!='01'){
    $dnum=7-$weekx;
  }else{
    $dnum=-$weekx;
  }
  $weekstr=$week+$dnum;
  $nowdate=date('Y-m-d',strtotime($yearstr."+$weeknumstr week $weekstr days"));
   return $nowdate;
}

//根据具体日期返回是这年的第几周星期几
function getweek($date){
  $week=date('w',strtotime($date));
  return $week;
}

//利用生日算出年龄
function getAges($date){
	if($date!=""){
		$now=time();
		$birthday=$now-strtotime($date);
		return round($birthday/31536000);
	}else{
		return "";	
	}
}

//活动数量明细组合
function actjsontostr($json){
	$arr=json_decode($json,true);
	if(is_array($arr) && count($arr)>0){
		foreach($arr as $v){
			$gtitle.=$v["count"].$v["type"];
		}
		return $gtitle;
	}else{
		return false;	
	}
}