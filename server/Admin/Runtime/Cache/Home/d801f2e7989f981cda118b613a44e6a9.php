<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>功能设置</title>
<link href="/vueshopserver/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">
function CheckAll(form)
{
  for (var i=0;i<form.elements.length;i++)
    {
    var e = form.elements[i];
    if (e.Name != "chkAll"&&e.disabled!=true)
       e.checked = form.chkAll.checked;
    }
}
function check(){
	if (document.addform.title.value.match(/^\s*$/)){
		alert ("请输入功能名称");
		document.addform.title.focus();
		return false;
	}
	if (document.addform.webs.value.match(/^\s*$/)){
		alert ("请输入链接地址");
		document.addform.webs.focus();
		return false;
	}
}
function isSubmit(url){
	if(confirm('确认要删除吗？')){
		document.form1.action=url;
		document.form1.submit();
	}	
}
</script>
</head>

<body>
<form name="addform" id="addform" action="/vueshopserver/hadmin.php/Home/Funset/add" method="post" onsubmit="return check()">
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td height="25" colspan="2" align="center" class="back14">功能添加</td>
    </tr>
  <tr bgcolor="#FFFFFF">
    <td width="30%" height="35" align="right">功能名称：</td>
    <td width="70%">
      <input type="text" name="title" id="title" class="htinputcss" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="35" align="right">链接地址：</td>
    <td><input type="text" name="webs" id="webs" class="htinputcss" /></td>
  </tr>
  <tr bgcolor="#FFFFFF">
    <td height="35" align="right">&nbsp;</td>
    <td><input type="submit" name="addbtn" id="addbtn" class="addbtn" value="提交" />
      &nbsp;&nbsp;
      <input type="reset" name="resbtn" id="resbtn" class="resbtn" value="重置" /></td>
  </tr>
</table>
</form>
<br>
<hr>
<br>
<form name="form1" id="form1" method="post" action="">
<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td width="10%" height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="10%" align="center">ID</td>
    <td width="30%" align="center">功能名称</td>
    <td width="10%" align="center">状态</td>
    <td width="10%" align="center">是否显示桌面</td>
    <td align="center">管理</td>
  </tr>
  <?php if(is_array($arr)): $i = 0; $__LIST__ = $arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" value="<?php echo ($vo["id"]); ?>" /></td>
    <td align="center"><?php echo ($vo["id"]); ?></td>
    <td align="center"><?php echo ($vo["title"]); ?></td>
    <td align="center">
    	<?php if($vo["isclose"] == '1'): ?><font color="#549f3b">启用</font>
        <?php else: ?>
        	关闭<?php endif; ?>
    </td>
    <td align="center">
    <?php if($vo["isshow"] == '1'): ?><font color="#549f3b">显示</font>
    <?php else: ?>
        隐藏<?php endif; ?>
    </td>
    <td align="center"><a href="/vueshopserver/hadmin.php/Home/Funset/edit?id=<?php echo ($vo["id"]); ?>" class="edit">编辑</a>&nbsp;&nbsp;&nbsp;&nbsp;<?php if($vo["isclose"] == '1'): ?><a href="/vueshopserver/hadmin.php/Home/Funset/close?id=<?php echo ($vo["id"]); ?>" class="close">关闭</a><?php else: ?><a href="/vueshopserver/hadmin.php/Home/Funset/open?id=<?php echo ($vo["id"]); ?>" class="edit">开启</a><?php endif; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php if($vo["isshow"] == '1'): ?><a href="/vueshopserver/hadmin.php/Home/Funset/isdesktop?id=<?php echo ($vo["id"]); ?>&val=0" class="close">隐藏</a><?php else: ?><a href="/vueshopserver/hadmin.php/Home/Funset/isdesktop?id=<?php echo ($vo["id"]); ?>&val=1" class="edit">显示</a><?php endif; ?></td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" align="center"><input type="button" name="delbtn" id="delbtn" class="delbtn" value="删除" onclick="isSubmit('/vueshopserver/hadmin.php/Home/Funset/del');"  /></td>
    <td height="52" colspan="5">&nbsp;</td>
    </tr>
</table>
</form>
</body>
</html>