<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/vueshopserver/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/vueshopserver/Public/admin/js/jquery.js" ></script>
<script type="text/javascript" src="/vueshopserver/Public/admin/js/jquery.form.js" ></script>
<script type="text/javascript" src="/vueshopserver/Public/admin/js/formfiles.js" ></script>
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
		alert ("请选择管理员分组");
		document.form1.gid.focus();
		return false;
	}
	if (document.form1.adminname.value.match(/^\s*$/)){
		alert ("用户名不能为空");
		document.form1.adminname.focus();
		return false;
	}
	if (document.form1.password.value.match(/^\s*$/) || document.form1.password.value.length<5){
		alert ("密码不能为空且不能小于5位");
		document.form1.password.focus();
		return false;
	}
	if (document.form1.qpwd.value.match(/^\s*$/)){
		alert ("确认密码不能为空");
		document.form1.qpwd.focus();
		return false;
	}
	if(document.form1.password.value!=document.form1.qpwd.value){
		alert("您输入的密码不一致");
		document.form1.qpwd.focus();
		return false;
	}
}
$(function(){
	$("#gid").change(function(){
		if($(this).val()==6){
			$("#attr").show();
		}else{
			$("#attr").hide();	
		}
	});
});
</script>
</head>

<body>
<div id="spacemenu"></div>
<div class="alterdiv"></div>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="38%" align="center" valign="top">
    	<div id="leftdiv">
        	<div id="titlediv">添加管理员</div>
            <div id="formdiv">
            <form name="form1" id="form1" method="post" action="/vueshopserver/hadmin.php/Home/AdminManage/add" onsubmit="return check()">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="green" style="margin-top:20px;">
  <tr>
    <td width="33%" height="40" align="right">管理员分组：</td>
    <td width="67%"><select name="gid" id="gid">
        <option selected="selected" value="">请选择</option>
        <?php if(is_array($gdata)): $i = 0; $__LIST__ = $gdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["groupname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
      </select></td>
  </tr>
  <tr>
      <td height="40" align="right">用户名：</td>
      <td width="67%"><input type="text" name="adminname" id="adminname" class="htinputcss" /></td>
  </tr>
  <tr>
    <td height="40" align="right">密码：</td>
    <td><input type="password" name="password" id="password" class="htinputcss" /></td>
  </tr>
  <tr>
    <td height="40" align="right">确认密码：</td>
    <td><input type="password" name="qpwd" id="qpwd" class="htinputcss" /></td>
  </tr>
  <tr>
    <td colspan="2" align="right">
    <table id="attr" style="display:none;" width="100%" border="0" cellspacing="0" cellpadding="0">
    	<tr>
          <td width="33%" height="40" align="right">上传头像：</td>
          <td><input id="photo" class="inputleft htinputcss" name="photo" value="" type="text"><a class="files" href="javascript:void(0);"><input id="FileUpload" onchange="SingleUpload('photo','FileUpload','thumb','/vueshopserver/hadmin.php/Home/AdminManage/upload')" name="FileUpload" type="file"></a><span class="uploading">正在上传，请稍候...</span></td>
          </tr>
          <tr>
              <td height="40" align="right">姓名：</td>
              <td width="67%"><input type="text" name="name" id="name" class="htinputcss" /></td>
            </tr>
           <tr>
              <td height="40" align="right">从业时间：</td>
              <td width="67%"><input type="text" name="worktime" id="worktime" class="htinputcss" /></td>
            </tr>
            <tr>
              <td height="40" align="right">服务过的团队：</td>
              <td width="67%"><input type="text" name="team" id="team" class="htinputcss" /></td>
            </tr>
          <tr>
            <td height="40" align="right">客服简介：</td>
            <td><textarea name="content" id="content" style="width:200px;height:100px;"></textarea></td>
          </tr>
       </table>
    </td>
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
    	<div id="ldiv_search">
        <form name="searfrom" id="searform" method="post" action="/vueshopserver/hadmin.php/Home/AdminManage/manage">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="24%" height="60" align="right" class="gray">管理员用户名：</td>
    <td width="76%"><input type="text" name="kwords" id="kwords" class="htinputcss" /><input type="submit" name="button3" id="button3" value="搜索" class="ssbtn" /></td>
  </tr>
</table>
</form>
        </div>
        <div id="lnk_listdiv">
        <form name="form2" id="form2" method="post" action="/vueshopserver/hadmin.php/Home/AdminManage/del" onSubmit="return confirm('确定要执行选定的操作吗？');">
        	<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td width="10%" height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="18%" align="center">用户名</td>
    <td align="center">管理员所在组</td>
    <td align="center">权限状态</td>
    <td width="30%" align="center">管理</td>
  </tr>
  <?php if(is_array($datalist)): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" <?php if($vo["id"] == 36): ?>disabled="disabled"<?php endif; ?> value="<?php echo ($vo["qid"]); ?>"  /></td>
    <td align="center"><?php echo ($vo["adminname"]); ?></td>
    <td align="center"><?php echo ($vo["groupname"]); ?></td>
    <td align="center"><?php echo ($vo["printf"]); ?>
    <?php if($vo[isrecom] == 1): endif; ?>
    </td>
    <td align="center"><a href="/vueshopserver/hadmin.php/Home/AdminManage/edit?id=<?php echo ($vo["id"]); ?>" class="edit">修改</a> 
	<?php if($vo["gid"] == 1): ?><span class="editgray">权限开放</span>
	<?php else: ?>
      <a href="/vueshopserver/hadmin.php/Home/AdminColumn/index?an=<?php echo ($vo["adminname"]); ?>&userid=<?php echo ($vo["id"]); ?>&page=<?php echo ($page); echo ($strname); ?>" class="editblue">栏目权限</a>&nbsp;<a href="/vueshopserver/hadmin.php/Home/AdminOther/index?an=<?php echo ($vo["adminname"]); ?>&userid=<?php echo ($vo["id"]); ?>&page=<?php echo ($page); echo ($strname); ?>"  class="editblue">其它版块</a><?php endif; ?>
      </td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" colspan="5" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delbtn" id="delbtn" class="delbtn" value="删除" />&nbsp;&nbsp;
      
    <input type="button" name="button3" id="button3" value="启用添加权限" class="powerbtn" onClick="this.form.action='/vueshopserver/hadmin.php/Home/AdminManage/addper';this.form.submit()" /> <input type="button" class="powerbtn" name="button3" id="button3" value="取消添加权限" onClick="this.form.action='/vueshopserver/hadmin.php/Home/AdminManage/clearadd';this.form.submit()" /> <input class="powerbtn" type="button" name="button4" id="button4" value="启用修改权限" onClick="this.form.action='/vueshopserver/hadmin.php/Home/AdminManage/modper';this.form.submit()" /> <input class="powerbtn" type="button" class="powerbtn" name="button4" id="button4" value="取消修改权限" onClick="this.form.action='/vueshopserver/hadmin.php/Home/AdminManage/clearmod';this.form.submit()" /> <input class="powerbtn" type="button" name="button5" id="button5" value="启用删除权限" onClick="this.form.action='/vueshopserver/hadmin.php/Home/AdminManage/delper';this.form.submit()" /> <input class="powerbtn" type="button" name="button5" id="button5" value="取消删除权限" onClick="this.form.action='/vueshopserver/hadmin.php/Home/AdminManage/cleardel';this.form.submit()" />
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