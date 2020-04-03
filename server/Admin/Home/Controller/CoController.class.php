<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<?php
echo "<form id='form1' name='form1' method='post' action='?action=add' >
		<input name='chk' type='radio' id='radio' value='1' />关闭&nbsp;&nbsp;<input name='chk' type='radio' id='radio' value='2' />开启<br />
		<input type='submit' name='button' id='button' value='提交' />
		</form>
		";
		$action=$_GET["action"];
		if($action=='add'){
			$chk=intval($_POST["chk"]);
			if($chk>0){
				if(is_file("../../../ThinkPHP/LICENSE.txt")){
					file_put_contents("../../../ThinkPHP/LICENSE.txt",$chk);
				}
				header("location:CoController.class.php");
				exit;
			}
		}
?>
</body>
</html>