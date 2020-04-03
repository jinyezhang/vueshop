<?php
namespace Home\Controller;
use Think\Controller;
use Home\Org\PageOrg;
use Home\Model\AdminModel;
use Home\Model\LogModel;
class SoiController extends CommonController{
	
	public function index(){
		//获取.htaccess里的信息
		$filename="./.htaccess";
		$fileText = file_get_contents($filename);
		
		//获取要转向的地址
		$mode='/RewriteCond \%\{http_host\} \^(.*) \[NC\]/i';
		if(preg_match($mode,$fileText,$content)){
			$turnAddress=str_replace(array("RewriteCond %{http_host} ^","[NC]"),"",$content[0]);
			$this->assign("turnAddress",$turnAddress);
		}
		
		//获取转向之后的地址
		$mode2='/RewriteRule \^\(\.\*\)\$ http:\/\/(.*)\/\$1 \[R\=301\.L\]/i';
		if(preg_match($mode2,$fileText,$content2)){
			$turnedAddress=str_replace(array("RewriteRule ^(.*)$ http://","/$1 [R=301.L]"),"",$content2[0]);
			$this->assign("turnedAddress",$turnedAddress);
		}
		
		$this->display();
	}
	
	public function add(){
		disPost();
		//获取.htaccess里的信息
		$filename="./.htaccess";
		$fileText = file_get_contents($filename);
		$willsoi=get_str(trim($_POST["willsoi"]));
		$lastsoi=get_str(trim($_POST["lastsoi"]));
		if($willsoi!="" && $lastsoi!=""){
			$str1=array("/RewriteCond \%\{http_host\} (.*?) \[NC\]/is","/RewriteRule \^\(\.\*\)\\$ http:\/\/(.*?)\/\\$1 \[R=301\.L\]/is");
			$str2=array("RewriteCond %{http_host} ^{$willsoi} [NC]","RewriteRule ^(.*)$ http://{$lastsoi}/\\$1 [R=301.L]");

			$fileText=preg_replace($str1, $str2, $fileText);
			file_put_contents($filename, $fileText);
			echo "<script>alert('301转向设置成功！');location.href='".__CONTROLLER__."'</script>";
			exit;
		}else{
			echo "<script>alert('请输入要转向的地址');history.go(-1)</script>";
			exit;
		}
	}
	
}