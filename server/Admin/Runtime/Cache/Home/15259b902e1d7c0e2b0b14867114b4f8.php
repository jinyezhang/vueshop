<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
<title></title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/admin/js/jquery.js" ></script>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#mainbox").height( jQuery(document).height());
	jQuery("#leftbox").height( jQuery("#mainbox").height() );

		jQuery("#dragbutton").click(function(){
		if( jQuery(this).attr("src") == "/Public/admin/images/arrow_left.gif" ){
			jQuery("#leftbox").css({width:"0px"}).hide();
			jQuery(this).attr("src","/Public/admin/images/arrow_right.gif");
			jQuery("#mainbox").css({width:"100%"});
		}else{
			jQuery("#leftbox").css({width:"160px"}).show();
			jQuery(this).attr("src","/Public/admin/images/arrow_left.gif");
			jQuery("#mainbox").css({width:"auto"});
		}
	});

});
</script>
</head>
<body>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
    	<td style="width:164px; height:100%; background: #c3efcc;" valign="top" id="leftbox">
        <!--左侧菜单-->
        <iframe name="leftframe" id="leftframe" frameborder="0" width="100%" height="100%" scrolling="auto" style="overflow:visible;" src="/hadmin.php/Home/AdManage/left"></iframe>
        <!--左侧菜单结束-->        </td>
        <td class="spacestyle" valign="middle" id="dragbox"><img src="/Public/admin/images/arrow_left.gif" width="10" height="30" border="0" id="dragbutton" style="cursor:pointer;" /></td>
        <td id="mainbox" valign="top">
        <!--内容主窗口-->
        <iframe name="mainframe" id="mainframe" frameborder="0" width="100%" height="100%" scrolling="auto" style="overflow:visible;" src="/hadmin.php/Home/AdManage/manage"></iframe></td>
    </tr>
</table>
<div id="otherwindow" class="floatdiv"></div>
</body>
</html>