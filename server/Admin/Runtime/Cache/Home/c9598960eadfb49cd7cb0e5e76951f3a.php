<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
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
</script>
</head>

<body>
<div id="spacemenu"></div>
<div class="alterdiv"></div>
<div id="maindiv">
<form id="form1" name="form1" method="post"  action="/vueshopserver/hadmin.php/Home/Order/del">
    <div id="navdiv">
        <table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td height="41" class="linkblue">&nbsp;&nbsp;&nbsp;您当前的位置：订单管理</td>
    <td width="65%" class="linkblue">筛选：<select name="screen" id="screen">
    	<option value="1" <?php if($screen == '1'): ?>selected="selected"<?php endif; ?>>订单编号</option>
        <option value="2" <?php if($screen == '2'): ?>selected="selected"<?php endif; ?> >手机号</option>
      </select>&nbsp;&nbsp;<input type="text" name="kwords" id="kwords" class="htinputcss" /><input type="button" name="button3" id="button3" value="搜索" class="ssbtn" onClick="this.form.action='/vueshopserver/hadmin.php/Home/Order';this.form.submit()" onFocus="this.blur()" />
    </td>
    </tr>
</table>
    </div>

<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td width="5%" height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="8%" align="center">订单编号</td>
    <td align="center">会员手机号</td>
    <td align="center">联系人</td>
    <td align="center">联系人手机号</td>
    <td  align="center" class="linkblue"><?php if($otime == 'desc'): ?><a href="/vueshopserver/hadmin.php/Home/Order?otime=asc">订购时间 ˇ</a><?php else: ?><a href="/vueshopserver/hadmin.php/Home/Order?otime=desc">订购时间 ˆ</a><?php endif; ?></td>
    <td  align="center" class="linkblue"><?php if($paytype == 'desc'): ?><a href="/vueshopserver/hadmin.php/Home/Order?paytype=asc">支付类型 ˇ</a><?php else: ?><a href="/vueshopserver/hadmin.php/Home/Order?paytype=desc">支付类型 ˆ</a><?php endif; ?></td>
    <td align="center" class="linkblue"><?php if($status == 'desc'): ?><a href="/vueshopserver/hadmin.php/Home/Order?status=asc">订单状态 ˇ</a><?php else: ?><a href="/vueshopserver/hadmin.php/Home/Order?status=desc">订单状态 ˆ</a><?php endif; ?></td>
    <td align="center">是否评价</td>
    <td width="12%" align="center">管理</td>
    </tr>
<?php if(is_array($datalist)): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" value="<?php echo ($vo["ordernum"]); ?>" /></td>
    <td align="center"><?php echo ($vo["ordernum"]); ?></td>
    <td align="center"><?php echo ($vo['ucellphone']); ?></td>
    <td align="center"><?php echo ($vo[name]); ?></td>
    <td align="center"><?php echo ($vo[cellphone]); ?></td>
    <td align="center"><?php echo ($vo["times"]); ?></td>
    <td align="center">
    <?php if($vo['paytype'] == '1'): ?><font color="#0000FF">支付宝</font>
    <?php elseif($vo['paytype'] == '2'): ?>
    	<font color="#00CC33">微信支付</font>
    <?php elseif($vo['paytype'] == '3'): ?>
    	<font color="#33CCFF">银联</font>
    <?php else: ?>
    	--<?php endif; ?>
    </td>
    <td align="center">
    <?php if($vo['status'] == '0'): ?>待付款
    <?php elseif($vo['status'] == '1'): ?>
    	<font color="#009966">待收货</font>
    <?php elseif($vo['status'] == '-1'): ?>
    	<font color="#FF0000">取消订单</font>
    <?php elseif($vo['status'] == '2'): ?>
    	<font color="#FF00FF">已收货</font><?php endif; ?>
    </td>
    <td align="center">
    <?php if($vo['iscomm'] == '1'): ?><font color="#0000FF">已评价</font>
    <?php else: ?>
    	<font color="#FF0000">未评价</font><?php endif; ?>
    </td>
    <td align="center">
    <a href="/vueshopserver/hadmin.php/Home/Order/desc?id=<?php echo ($vo["ordernum"]); ?>&page=<?php echo ($page); echo ($strname); ?>" class="editblue">订单详情</a></td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" colspan="13" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delbtn" id="delbtn" class="delbtn" value="删除" onclick="return confirm('确认要删除吗？')" />&nbsp;&nbsp;<?php if($gid == '1'): ?><input type="submit" name="delbtn" class="delbtn" value="取消订单" onClick="this.form.action='/vueshopserver/hadmin.php/Home/Order?action=orderstatus&status=-1';this.form.submit();" onFocus="this.blur()" />&nbsp;&nbsp;<input type="submit" name="delbtn" class="delbtn" value="待付款" onClick="this.form.action='/vueshopserver/hadmin.php/Home/Order?action=orderstatus&status=0';this.form.submit();" onFocus="this.blur()" />&nbsp;&nbsp;<input type="submit" name="delbtn" class="delbtn" value="待收货" onClick="this.form.action='/vueshopserver/hadmin.php/Home/Order?action=orderstatus&status=1';this.form.submit();" onFocus="this.blur()" />&nbsp;<input type="submit" name="delbtn" class="delbtn" value="已收货" onClick="this.form.action='/vueshopserver/hadmin.php/Home/Order?action=orderstatus&status=2';this.form.submit();" onFocus="this.blur()" /><?php endif; ?></td>
    </tr>
</table>
</form>
</div>
<div id="pagediv">
    <?php echo ($getpage); ?>
</div>
</body>
</html>