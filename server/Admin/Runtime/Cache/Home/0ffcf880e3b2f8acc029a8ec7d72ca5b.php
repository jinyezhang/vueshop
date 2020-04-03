<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
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
</script>
</head>

<body>
<div id="spacemenu"></div>
<div class="alterdiv"></div>
<div id="maindiv">
<form id="form1" name="form1" method="post"  action="/hadmin.php/Home/User/del?utype=<?php echo ($utype); ?>">
    <div id="navdiv">
        <table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td width="26%" height="41" class="linkblue">&nbsp;&nbsp;&nbsp;您当前的位置：<?php if($utype == '0'): ?>会员信息<?php else: ?>商家信息<?php endif; ?></td>
    <td width="74%" class="linkblue">筛选：<select name="screen" id="screen">
    	<option value="1" <?php if($screen == '1'): ?>selected="selected"<?php endif; ?>>用户编号</option>
        <option value="3" <?php if($screen == '3'): ?>selected="selected"<?php endif; ?> >手机号</option>
        <option value="5" <?php if($screen == '5'): ?>selected="selected"<?php endif; ?> >昵称</option>
      </select>&nbsp;&nbsp;
      <input type="text" name="kwords" id="kwords" class="htinputcss" value="<?php echo ($kwords); ?>" /><input type="button" name="button3" id="button3" value="搜索" class="ssbtn" onClick="this.form.action='/hadmin.php/Home/User/manage?utype=<?php echo ($utype); ?>';this.form.submit()" onFocus="this.blur()" />
    </td>
    </tr>
</table>
    </div>

<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="10%" align="center">用户编号</td>
    <td align="center">昵称</td>
    <td align="center">会员类型</td>
    <td width="15%" align="center">手机号</td>
    <td width="15%" align="center" class="linkblue"><?php if($timeorder == 'desc' || $timeorder == ''): ?><a href="/hadmin.php/Home/User/manage?timeorder=asc&utype=<?php echo ($utype); ?>">注册时间 ˇ</a><?php else: ?><a href="/hadmin.php/Home/User/manage?timeorder=desc&utype=<?php echo ($utype); ?>">注册时间 ˆ</a><?php endif; ?></td>
    <td width="15%" align="center">管理</td>
    </tr>
<?php if(is_array($datalist)): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" value="<?php echo ($vo["qid"]); ?>" /></td>
    <td align="center"><?php echo ($vo["qid"]); ?></td>
    <td align="center"><?php echo ($vo['nickname']); ?></td>
    <td align="center">
    <?php if($vo['utype'] == '0'): ?>普通会员
    <?php elseif($vo['utype'] == '1'): ?>
    	<font color="#0000FF">商家</font><?php endif; ?>
    </td>
    <td align="center"><?php echo ($vo[cellphone]); ?></td>
    <td align="center"><?php echo ($vo["times"]); ?></td>
    <td align="center"><a href="
    <?php if($vo['utype'] == '0'): ?>/hadmin.php/Home/User/edit?id=<?php echo ($vo["qid"]); ?>&page=<?php echo ($page); echo ($strname); ?>
    <?php else: ?>
    	/hadmin.php/Home/User/editseller?id=<?php echo ($vo["qid"]); ?>&page=<?php echo ($page); echo ($strname); endif; ?>
    " class="editblue">用户详情</a></td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" colspan="9" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delbtn" id="delbtn" class="delbtn" value="删除" onclick="return confirm('确认要删除吗？')" /><!--&nbsp;<input type="button" name="button4" id="button4" class="orderbtn" value="成为商家" onClick="this.form.action='/hadmin.php/Home/User/audit?utype=<?php echo ($utype); ?>';this.form.submit();" onFocus="this.blur()" />&nbsp;<input type="button" name="button4" id="button4" class="orderbtn" value="撤销商家" onClick="this.form.action='/hadmin.php/Home/User/unaudit?utype=<?php echo ($utype); ?>';this.form.submit();" onFocus="this.blur()" />--></td>
    </tr>
</table>
</form>
</div>
<div id="pagediv">
    <?php echo ($getpage); ?>
</div>
</body>
</html>