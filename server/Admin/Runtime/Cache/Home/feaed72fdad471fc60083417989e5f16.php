<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/admin/js/jquery.js" ></script>
<script type="text/javascript" src="/Public/admin/js/jquery.form.js" ></script>
<script type="text/javascript" src="/Public/admin/js/formfiles.js" ></script>
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
	if (document.form1.gid.value.match(/^\s*$/)){
		alert ("请选择图片组");
		document.form1.gid.focus();
		return false;
	}
	if (document.form1.title.value.match(/^\s*$/)){
		alert ("请输入标题");
		document.form1.title.focus();
		return false;
	}
}
</script>
</head>

<body>
<div id="spacemenu"></div>
<div class="alterdiv"></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="38%" align="center" valign="top">
    	<div id="leftdiv">
        	<div id="titlediv">添加图片</div>
            <div id="formdiv">
            <form name="form1" id="form1" method="post" action="/hadmin.php/Home/AdManage/add" onsubmit="return check()">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="green" style="margin-top:20px;">
  <tr>
    <td width="33%" height="40" align="right">图片组：</td>
    <td width="67%"><select name="gid" id="gid">
        <option selected="selected" value="">请选择</option>
        <?php if(is_array($gdata)): $i = 0; $__LIST__ = $gdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
      </select></td>
  </tr>
  <tr>
      <td height="40" align="right">标题：</td>
      <td width="67%"><input type="text" name="title" id="title" class="htinputcss" /></td>
  </tr>
  <tr>
    <td height="40" align="right">链接地址：</td>
    <td><input type="text" name="webs" id="webs" class="htinputcss" /></td>
  </tr>
  <tr>
  <td height="40" align="right">图片上传：</td>
  <td><input id="photo" class="inputleft htinputcss" name="photo" value="" type="text"><a class="files" href="javascript:void(0);"><input id="FileUpload" onchange="SingleUpload('photo','FileUpload','images','/hadmin.php/Home/AdManage/upload')" name="FileUpload" type="file"></a><span class="uploading">正在上传，请稍候...</span></td>
  </tr>
  <tr>
    <td height="40">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="提交" class="addbtn" />&nbsp;&nbsp;
      <input type="reset" name="button2" id="button2" value="重置" class="resbtn" /></td>
  </tr>
</table>
</form>
            </div>
        </div>
    </td>
    <td width="62%" valign="top">
    	<div id="ldiv_search"></div>
        <div id="lnk_listdiv">
        <form name="form2" id="form2" method="post" action="/hadmin.php/Home/AdManage/del" onSubmit="return confirm('确定要执行选定的操作吗？');">
        	<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td width="10%" height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="24%" align="center">标题</td>
    <td width="15%" align="center">图片预览</td>
    <td width="10%" align="center">图片组</td>
    <td width="18%" align="center">排序</td>
    <td width="10%" align="center">管理</td>
  </tr>
  <?php if(is_array($datalist)): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" value="<?php echo ($vo["id"]); ?>"  /></td>
    <td align="center"><?php echo ($vo["title"]); ?></td>
    <td align="center"><a href="/uploadfiles/<?php echo ($vo["photo"]); ?>" target="_blank"><img src="/uploadfiles/<?php echo ($vo["photo"]); ?>" width="100" height="50" border="0" /></a></td>
    <td align="center"><?php echo ($vo["gtitle"]); ?></td>
    <td align="center"><input type="text" name="num[]" id="num[]" class="htinputcss" style="width:50px;" value="<?php echo ($vo["num"]); ?>" /><input name="numid[]" type="hidden" id="numid[]"  value="<?php echo ($vo["id"]); ?>" /></td>
    <td align="center"><a href="/hadmin.php/Home/AdManage/edit?id=<?php echo ($vo["id"]); ?>" class="edit">修改</a> </td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" colspan="6" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delbtn" id="delbtn" class="delbtn" value="删除" />&nbsp;&nbsp;<input type="button" name="button4" id="button4" class="orderbtn" value="修改排序" onClick="this.form.action='/hadmin.php/Home/AdManage/order';this.form.submit();" onFocus="this.blur()" />
      </td>
    </tr>
</table>
</form>
        </div>
        <div id="pagediv">
        	<?php echo ($getpage); ?>
        </div>
    </td>
  </tr>
</table>
</body>
</html>