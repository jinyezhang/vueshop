<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>订单详情</title>
<link href="/vueshopserver/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />

</head>

<body>
<div id="spacemenu">
	<div id="leftarrow"></div>
    <div id="return1"><a href="/vueshopserver/hadmin.php/Home/Order?page=<?php echo ($page); ?>&kwords=<?php echo ($kwords); ?>">返回上一级</a></div>
</div>
<div class="alterdiv"></div>
<div id="posdiv">
	<table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td height="46">您当前的位置：订单详情</td>
  </tr>
</table>
</div>
<div id="maindiv">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="green">
<tr>
    <td width="36%" height="40" align="right">订单编号：</td>
    <td width="64%" class="gray"><?php echo ($odata['ordernum']); ?></td>
  </tr>
<tr>
    <td width="36%" height="40" align="right">订购时间：</td>
    <td width="64%" class="gray"><?php echo ($odata['times']); ?></td>
  </tr>
  <tr>
    <td width="36%" height="40" align="right">支付类型：</td>
    <td width="64%" class="gray">
    <?php if($odata['paytype'] == '1'): ?><font color="#0000FF">支付宝</font>
    <?php elseif($odata['paytype'] == '2'): ?>
    	<font color="#00CC33">微信支付</font>
    <?php elseif($odata['paytype'] == '3'): ?>
    	<font color="#33CCFF">银联</font>
    <?php else: ?>
    	--<?php endif; ?>
    </td>
  </tr>
  <tr>
  <td height="40" align="right">订单状态：</td>
  <td width="64%" class="gray">
  <?php if($odata['status'] == '0'): ?>待付款
<?php elseif($odata['status'] == '1'): ?>
    <font color="#009966">已付款</font>
<?php elseif($odata['status'] == '-1'): ?>
    <font color="#FF0000">取消订单</font>
<?php elseif($odata['status'] == '2'): ?>
    	<font color="#FF00FF">确认收货</font><?php endif; ?>
</if>
  </td>
  </tr>
  <tr>
  <td height="40" align="right">是否评价：</td>
  <td width="64%" class="gray">
  <?php if($odata['iscomm'] == '1'): ?><font color="#0000FF">已评价</font>
<?php else: ?>
    <font color="#FF0000">未评价</font><?php endif; ?>
  </td>
  </tr>
  <tr>
    <td width="36%" height="40" align="right">会员手机号：</td>
    <td width="64%" class="gray"><?php echo ($udata['cellphone']); ?></td>
  </tr>
  <tr>
  <td height="40" align="right">收货人：</td>
  <td class="gray"><?php echo ($addsData['name']); ?></td>
  </tr>
  <tr>
  <td height="40" align="right">收货人电话：</td>
  <td class="gray"><?php echo ($addsData['cellphone']); ?></td>
  </tr>
    <tr>
        <td height="40" align="right">收货地址：</td>
        <td class="gray"><?php echo ($addsData['province']); echo ($addsData["city"]); echo ($addsData["area"]); echo ($addsData["address"]); ?></td>
    </tr>
  <?php if(is_array($gdata)): $i = 0; $__LIST__ = $gdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
  <td height="40" align="right">商品名称：</td>
  <td class="gray"><?php echo ($vo['title']); ?></td>
  </tr>
  <tr>
  <td height="40" align="right">价格：</td>
  <td class="gray"><?php echo ($vo['price']); ?>元</td>
  </tr>
  <tr>
  <td height="40" align="right">数量：</td>
  <td class="gray"><?php echo ($vo['amount']); ?></td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr>
  <tr>
    <td width="36%" height="40" align="right">运费：</td>
    <td width="64%" class="gray"><?php echo ($odata['freight']); ?></td>
  </tr>
  <td height="40" align="right">总价：</td>
  <td class="gray"><?php echo ($total); ?>元</td>
  </tr>
</table>
</div>
</body>
</html>