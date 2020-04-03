<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body>
<div id="spacemenu"></div>
<div class="alterdiv"></div>
<div id="navdiv">
        <table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td height="41" class="linkblue">&nbsp;&nbsp;&nbsp;您当前的位置：评价设置</td>
    </tr>
</table>
    </div>
<form name="form1" id="form1" method="post" action="/hadmin.php/Home/Reviewsmanage/setting?action=add">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="green" style="margin-top:20px;">
  <tr>
    <td width="37%" height="40" align="right">是否开启审核：</td>
    <td width="63%"><label>开启<input type="radio" name="isreview" value="1" <?php if($data['isreviews'] == '1'): ?>checked="checked"<?php endif; ?> /></label>&nbsp;&nbsp;&nbsp;<label>关闭<input type="radio" name="isreview" value="0" <?php if($data['isreviews'] == '0'): ?>checked="checked"<?php endif; ?> /></label></td>
  </tr>
  <tr>
    <td height="40" align="right">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="提交" class="addbtn" />&nbsp;&nbsp;
      <input type="reset" name="button2" id="button2" value="重置" class="resbtn" /></td>
  </tr>
</table>
</form>
</body>
</html>