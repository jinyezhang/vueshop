<?php
//文件下载类
class Download{
	protected $debug=true;
	protected $errormsg='';
	protected $Filter=array();
	protected $filename='';
	protected $mineType='text/plain';
	protected $xlq_filetype=array();
	
	function __construct($fileFilter='',$isdebug=true){
	$this->setFilter($fileFilter);
	$this->setdebug($isdebug);        
	$this->setfiletype();
	}
	
	private function setFilter($fileFilter){
	if(empty($fileFilter)) return ;
		$this->Filter=explode(',',strtolower($fileFilter));
	}
	
	 private function setdebug($debug){
		$this->debug=$debug;
	  }
	
	private function setfilename($filename){
	$this->filename=$filename;
	}
	
	//调用执行程序
	function downloadfile($filename){
		$this->setfilename($filename);
		if($this->filecheck())
			{
			  $fn = array_pop( explode( '/', strtr( $this->filename, '\\', '/' ) ) );
		  		// 读取文件
				if (is_readable($this->filename)) 
				{ 
					/* 
					简述: ob_end_clean() 清空并关闭输出缓冲, 详见手册 
					说明: 关闭输出缓冲, 使文件片段内容读取至内存后即被送出, 减少资源消耗 
					*/ 
					ob_end_clean(); 
					/* 
					HTTP头信息: 指示客户机可以接收生存期不大于指定时间（秒）的响应 
					*/ 
					header('Cache-control: max-age=31536000'); 
					/* 
					HTTP头信息: 缓存文件过期时间(格林威治标准时) 
					*/ 
					header('Expires: ' . gmdate('D, d M Y H:i:s', time()+31536000) . ' GMT'); 
					/* 
					HTTP头信息: 文件在服务期端最后被修改的时间 
					Cache-control,Expires,Last-Modified 都是控制浏览器缓存的头信息 
					在一些访问量巨大的门户, 合理的设置缓存能够避免过多的服务器请求, 一定程度下缓解服务器的压力 
					*/ 
					header('Last-Modified: ' . gmdate('D, d M Y H:i:s' , filemtime($this->filename) . ' GMT')); 
					/* 
					HTTP头信息: 文档的编码(Encode)方法, 因为附件请求的文件多样化, 改变编码方式有可能损坏文件, 故为none 
					*/ 
					header('Content-Encoding: none'); 
					/* 
					HTTP头信息: 告诉浏览器当前请求的文件类型. 
					1.始终指定为application/octet-stream, 就代表文件是二进制流, 始终提示下载. 
					2.指定对应的类型, 如请求的是mp3文件, 对应的MIME类型是audio/mpeg, IE就会自动启动Windows Media Player进行播放. 
					*/ 
					header('Content-type: ' . $this->mineType); 
					/* 
					HTTP头信息: 如果为attachment, 则告诉浏览器, 在访问时弹出"文件下载"对话框, 并指定保存时文件的默认名称(可以与服务器的文件名不同) 
					如果要让浏览器直接显示内容, 则要指定为inline, 如图片, 文本 
					*/ 
					header('Content-Disposition: attachment; filename=' . $fn); 
					/* 
					HTTP头信息: 告诉浏览器文件长度 
					(IE下载文件的时候不是有文件大小信息么?) 
					*/ 
					header('Content-Length: ' . filesize($this->filename)); 
					// 打开文件(二进制只读模式)
					$fp = fopen($this->filename, 'rb'); 
					// 输出文件
					fpassthru($fp); 
					// 关闭文件
					fclose($fp); 
					return true;
				}
		  
			}else
			{
			return false;
			}
		}
		function geterrormsg()
		{
		return $this->errormsg;
	}
	
	private function filecheck(){
		$filename=$this->filename;
		if(file_exists($filename))
		{
			$filearr=explode('.',$filename);
		   $filetype=strtolower(array_pop($filearr));
		   if(in_array($filetype,$this->Filter)){
			 $this->errormsg.=$filename.'不允许下载！';
				 if($this->debug) exit($filename.'不允许下载！') ;
				 return false;
		   	}else{
			 if ( function_exists( "mime_content_type" ) )
				 {
		   $this->mineType = mime_content_type( $filename );
		 }
				 if(empty($this->mineType))
				 {
					if( isset($this->xlq_filetype[$filetype]) )  $this->mineType = $this->xlq_filetype[$filetype];
				 }
				 if(!empty($this->mineType))
				   return true;
				 else
				 {
					$this->errormsg.='获取'.$filename.'文件类型时候发生错误，或者不存在预定文件类型内';
						if($this->debug) exit('获取文件类型出错');
						return false;
				 }
		   } 
		}else{
		  $this->errormsg.=$filename.'不存在!';
		  if($this->debug) exit($filename.'不存在!') ;
		  return false;
		}
	}
	
	private function setfiletype(){
	$this->xlq_filetype['chm']='application/vnd.ms-htmlhelp';
	$this->xlq_filetype['ppt']='application/vnd.ms-powerpoint';
	$this->xlq_filetype['xls']='application/vnd.ms-excel';
	$this->xlq_filetype['doc']='application/ms-download';
	$this->xlq_filetype['exe']='application/x-msdownload';
	$this->xlq_filetype['rar']='application/x-rar-compressed';
	$this->xlq_filetype['js']="application/javascript";
	$this->xlq_filetype['css']="text/css";
	$this->xlq_filetype['hqx']="application/mac-binhex40";
	$this->xlq_filetype['bin']="application/octet-stream";
	$this->xlq_filetype['oda']="application/oda";
	$this->xlq_filetype['pdf']="application/pdf";
	$this->xlq_filetype['ai']="application/postsrcipt";
	$this->xlq_filetype['eps']="application/postsrcipt";
	$this->xlq_filetype['es']="application/postsrcipt";
	$this->xlq_filetype['rtf']="application/rtf";
	$this->xlq_filetype['mif']="application/x-mif";
	$this->xlq_filetype['csh']="application/x-csh";
	$this->xlq_filetype['dvi']="application/x-dvi";
	$this->xlq_filetype['hdf']="application/x-hdf";
	$this->xlq_filetype['nc']="application/x-netcdf";
	$this->xlq_filetype['cdf']="application/x-netcdf";
	$this->xlq_filetype['latex']="application/x-latex";
	$this->xlq_filetype['ts']="application/x-troll-ts";
	$this->xlq_filetype['src']="application/x-wais-source";
	$this->xlq_filetype['zip']="application/zip";
	$this->xlq_filetype['bcpio']="application/x-bcpio";
	$this->xlq_filetype['cpio']="application/x-cpio";
	$this->xlq_filetype['gtar']="application/x-gtar";
	$this->xlq_filetype['shar']="application/x-shar";
	$this->xlq_filetype['sv4cpio']="application/x-sv4cpio";
	$this->xlq_filetype['sv4crc']="application/x-sv4crc";
	$this->xlq_filetype['tar']="application/x-tar";
	$this->xlq_filetype['ustar']="application/x-ustar";
	$this->xlq_filetype['man']="application/x-troff-man";
	$this->xlq_filetype['sh']="application/x-sh";
	$this->xlq_filetype['tcl']="application/x-tcl";
	$this->xlq_filetype['tex']="application/x-tex";
	$this->xlq_filetype['texi']="application/x-texinfo";
	$this->xlq_filetype['texinfo']="application/x-texinfo";
	$this->xlq_filetype['t']="application/x-troff";
	$this->xlq_filetype['tr']="application/x-troff";
	$this->xlq_filetype['roff']="application/x-troff";
	$this->xlq_filetype['shar']="application/x-shar";
	$this->xlq_filetype['me']="application/x-troll-me";
	$this->xlq_filetype['ts']="application/x-troll-ts";
	$this->xlq_filetype['gif']="image/gif";
	$this->xlq_filetype['jpeg']="image/pjpeg";
	$this->xlq_filetype['jpg']="image/pjpeg";
	$this->xlq_filetype['jpe']="image/pjpeg";
	$this->xlq_filetype['ras']="image/x-cmu-raster";
	$this->xlq_filetype['pbm']="image/x-portable-bitmap";
	$this->xlq_filetype['ppm']="image/x-portable-pixmap";
	$this->xlq_filetype['xbm']="image/x-xbitmap";
	$this->xlq_filetype['xwd']="image/x-xwindowdump";
	$this->xlq_filetype['ief']="image/ief";
	$this->xlq_filetype['tif']="image/tiff";
	$this->xlq_filetype['tiff']="image/tiff";
	$this->xlq_filetype['pnm']="image/x-portable-anymap";
	$this->xlq_filetype['pgm']="image/x-portable-graymap";
	$this->xlq_filetype['rgb']="image/x-rgb";
	$this->xlq_filetype['xpm']="image/x-xpixmap";
	$this->xlq_filetype['txt']="text/plain";
	$this->xlq_filetype['c']="text/plain";
	$this->xlq_filetype['cc']="text/plain";
	$this->xlq_filetype['h']="text/plain";
	$this->xlq_filetype['html']="text/html";
	$this->xlq_filetype['htm']="text/html";
	$this->xlq_filetype['htl']="text/html";
	$this->xlq_filetype['rtx']="text/richtext";
	$this->xlq_filetype['etx']="text/x-setext";
	$this->xlq_filetype['tsv']="text/tab-separated-values";
	$this->xlq_filetype['mpeg']="video/mpeg";
	$this->xlq_filetype['mpg']="video/mpeg";
	$this->xlq_filetype['mpe']="video/mpeg";
	$this->xlq_filetype['avi']="video/x-msvideo";
	$this->xlq_filetype['qt']="video/quicktime";
	$this->xlq_filetype['mov']="video/quicktime";
	$this->xlq_filetype['moov']="video/quicktime";
	$this->xlq_filetype['movie']="video/x-sgi-movie";
	$this->xlq_filetype['au']="audio/basic";
	$this->xlq_filetype['snd']="audio/basic";
	$this->xlq_filetype['wav']="audio/x-wav";
	$this->xlq_filetype['aif']="audio/x-aiff";
	$this->xlq_filetype['aiff']="audio/x-aiff";
	$this->xlq_filetype['aifc']="audio/x-aiff";
	$this->xlq_filetype['swf']="application/x-shockwave-flash";
	$this->xlq_filetype['exe']="application/x-msdownload";
	}
}
/*
使用方法
$download=new Download('php,exe,html',false);
if(!$download->downloadfile($filename)){
   echo $download->geterrormsg();
} */
?>