<?php
namespace Home\Org;
set_time_limit(0);
class UploadFileOrg{
	protected $filepath; //上传路径
	protected $fileinfo; //文件信息
	protected $extfile; //文件扩展名
	protected $filename; //文件名
	protected $filesizes; //文件大小
	protected $UpFileType;//上传文件类型
	protected $myfile;//formfiles.js里的文件上传路径
	
	//执行上传文件
	function upfileload($filepath,$upfiletype,$setsize,$action=''){
		$this->fileoption($upfiletype,$filepath);
		$this->fileError($this->extfile,$this->UpFileType);
		$this->upfiles($this->filesizes,$this->filepath,$this->filename,$setsize);
		if($action=="head"){
			import("@.Org.Images");
			$img=new \Images($filepath);
			$img->thumb($this->filename,80,80,"");
		}
		/*if($action=="thumb"){
			import("@.Org.Images");
			$img=new \Images($filepath);
			$img->thumb($this->filename,150,150,"");
		}*/
		if($action=="images"){
			import("@.Org.Images");
			$img=new \Images($filepath);
			$img->thumb($this->filename,750,750,"");
		}
	}
	
	//获取文件属性
	private function fileoption($FileType,$filepath){
		$this->myfile=$_GET["UpFilePath"];
		$this->fileinfo=pathinfo($_FILES["{$this->myfile}"]["name"]); //获取文件信息
		$this->extfile=$this->fileinfo["extension"]; //获取文件扩展名
		$this->filename=time().".".$this->extfile; //自动生成文件名
		$this->filesizes=$_FILES["{$this->myfile}"]["size"]/1024;//1kB
		$this->UpFileType=$FileType;//获取文件类型:设置模式array("jpg","gif","png","jpeg")
		$this->filepath=$filepath."/".$this->filename;//获取文件路径
	}
	
	//设置文件错误提示
	private function fileError($extfile,$upfiletype){
		if ($_FILES["{$this->myfile}"]["error"]>0){
			switch($_FILES["{$this->myfile}"]["error"]){
				case 1:
					echo "{\"msbox\":\"上传文件超过了php.ini中upload_max_filesize这个选项设置的值\"}";
					break;
				case 2:
					echo "{\"msbox\":\"上传的文件大小超过了HTML表单中MAX_FILE_SIZE选项指定的值\"}";
					break;
				case 3:
					echo "{\"msbox\":\"文件只有部分被上传\"}";
					break;
				case 4:
					echo "{\"msbox\":\"没有文件上传\"}";
					break;
			}
			exit;	
		}else{
			if (!in_array($extfile,$upfiletype) || preg_match('/(<\?php)|(<script)|(<html)|(<iframe)|(<body)/i',file_get_contents($_FILES["{$this->myfile}"]["tmp_name"]),$con)){
				echo "{\"msbox\":\"文件类型上传不正确\"}";
				exit;
			}
		}
	}
	
	//执行文件上传
	private function upfiles($filesize,$filepath,$filename,$setsize){
		if(is_uploaded_file($_FILES["{$this->myfile}"]["tmp_name"])){
			if($filesize<$setsize){
				if(move_uploaded_file($_FILES["{$this->myfile}"]["tmp_name"],$filepath)){
					echo "{\"msg\":\"1\",\"msbox\":\"".$filename."\"}";
				}else{
					echo "{\"msbox\":\"上传文件失败\"}";	
				}
			}else{
				echo "{\"msbox\":\"文件大小超过了限制\"}";
			}
		}
	}
	
}
?>