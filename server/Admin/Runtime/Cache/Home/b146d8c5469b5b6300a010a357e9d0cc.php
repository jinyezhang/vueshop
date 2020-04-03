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
<form id="form1" name="form1" method="post"  action="/hadmin.php/Home/Reviews/del">
    <div id="navdiv">
        <table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td height="41" class="linkblue">&nbsp;&nbsp;&nbsp;您当前的位置：商品评价</td>
    <td width="59%" class="linkblue">会员帐号：<input type="text" name="kwords" id="kwords" class="htinputcss" /><input type="button" name="button3" id="button3" value="搜索" class="ssbtn" onClick="this.form.action='/hadmin.php/Home/Reviews/manage';this.form.submit()" onFocus="this.blur()" />
    </td>
    </tr>
</table>
    </div>

<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td width="5%" height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="15%" align="center">商品标题</td>
    <td width="25%" align="center">评价内容</td>
    <td align="center">会员手机号</td>
    <td align="center">提交时间</td>
    <td width="8%" align="center">是否审核</td>
    </tr>
<?php if(is_array($datalist)): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" value="<?php echo ($vo["id"]); ?>" /></td>
    <td align="center"><?php echo ($vo["title"]); ?></td>
    <td align="center"><?php echo ($vo['content']); ?></td>
    <td align="center"><?php echo ($vo["cellphone"]); ?>(<?php echo ($vo['nickname']); ?>)</td>
    <td align="center"><?php echo ($vo['times']); ?></td>
    <td align="center">
    <?php if($vo['audit'] == '1'): ?><font color="#0000FF">已审核</font>
    <?php else: ?>
    	<font color="#FF0000">未审核</font><?php endif; ?>
    </td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" colspan="8" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delbtn" id="delbtn" class="delbtn" value="删除" onclick="return confirm('确认要删除吗？')" />&nbsp;&nbsp;<input type="button" name="button4" id="button4" class="orderbtn" value="审核通过" onClick="this.form.action='/hadmin.php/Home/Reviews/audit';this.form.submit();" onFocus="this.blur()" />&nbsp;&nbsp;<input type="button" name="button4" id="button4" class="orderbtn" value="撤销审核" onClick="this.form.action='/hadmin.php/Home/Reviews/unaudit';this.form.submit();" onFocus="this.blur()" /></td>
    </tr>
</table>
</form>
</div>
<div id="pagediv">
    <?php echo ($getpage); ?>
</div>
</body>
</html>