<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />

</head>

<body>
<div id="spacemenu">
	<div id="leftarrow"></div>
    <div id="return1"><a href="/hadmin.php/Home/User/manage?page=<?php echo ($page); echo ($strname); ?>">返回上一级</a></div>
</div>
<div class="alterdiv"></div>
<div id="posdiv">
	<table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td height="46">您当前的位置：会员详细资料</td>
  </tr>
</table>
</div>
<div id="maindiv">
<form id="form1" name="form1" method="post" action="/hadmin.php/Home/User/edit?id=<?php echo ($id); ?>&page=<?php echo ($page); ?>&action=mod<?php echo ($strname); ?>" onsubmit="return check()">
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="green">
<tr>
    <td width="28%" height="40" align="right">头像预览：</td>
    <td width="72%"><img src="/userfiles/head/<?php echo ($udata['head']); ?>" width="80" height="80" /></td>
  </tr>
	<tr>
        <td height="40" align="right">头像上传：</td>
        <td><input id="head" class="inputleft htinputcss" name="head" value="<?php echo ($udata['head']); ?>" type="text"><a class="files" href="javascript:void(0);"><input id="headUpload" onchange="SingleUpload('head','headUpload','head','/hadmin.php/Home/User/upload')" name="headUpload" type="file"></a><span class="uploading">正在上传，请稍候...</span></td>
        </tr>
  <tr>
    <td width="28%" height="40" align="right"><span class="redziti">*</span> 手机号：</td>
    <td width="72%"><input type="text" name="cellphone" id="cellphone" class="htinputcss" value="<?php echo ($udata['cellphone']); ?>" /></td>
  </tr>
  <tr>
    <td width="28%" height="40" align="right">密码：</td>
    <td width="72%"><input type="text" name="password" id="password" class="htinputcss" /></td>
  </tr>
  <tr>
    <td width="28%" height="40" align="right">昵称：</td>
    <td width="72%"><input type="text" name="nickname" id="nickname" class="htinputcss" value="<?php echo ($udata['nickname']); ?>" /></td>
  </tr>
    <tr>
        <td width="28%" height="40" align="right">性别：</td>
        <td width="72%">男<input type="radio" name="gender" value="1" <?php if($udata['gender'] == '1'): ?>checked<?php endif; ?> />&nbsp;&nbsp;女<input type="radio" name="gender" value="2" <?php if($udata['gender'] == '2'): ?>checked<?php endif; ?> /></td>
    </tr>
  <tr>
    <td height="40" align="right">&nbsp;</td>
    <td><input type="submit" name="button" id="button" value="修改" class="addbtn" />&nbsp;&nbsp;
      <input type="reset" name="button2" id="button2" value="重置" class="resbtn" /></td>
  </tr>
</table>
</form>
</div>
</body>
<script type="text/javascript" src="/Public/admin/js/jquery.js" ></script>
<script type="text/javascript" src="/Public/admin/js/jquery.form.js" ></script>
<script type="text/javascript" src="/Public/admin/js/formfiles.js" ></script>
<script type="text/javascript" src="/Public/admin/js/calender/WdatePicker.js"></script>
<script type="text/javascript">
function check()
{
	if (document.form1.cellphone.value.match(/^\s*$/)){
		alert ("请输入手机号码");
		document.form1.cellphone.focus();
		return false;
	}
	if (!document.form1.cellphone.value.match(/^1[3|4|5|8][0-9]\d{4,8}$/)){
		alert ("请输入正确的手机号码");
		document.form1.cellphone.focus();
		return false;
	}
}
$(function(){
//	$.ajax({
//		type:"get",
//		url:"/Public/admin/js/cityList.json",
//		async : false,
//		success:function(data){
//			//alert(JSON.stringify(data.citys));
//			var citydata=data.citys;
//			var city=document.getElementById("city");
//			for(var i=0;i<citydata.length;i++){
//				city.options.add(new Option(citydata[i].city,citydata[i].city));
//				if(citydata[i].city=="<?php echo ($udata['city']); ?>"){
//					city.options[i+1].selected="selected";
//				}
//			}
//		}
//	});
});
</script>
</html>