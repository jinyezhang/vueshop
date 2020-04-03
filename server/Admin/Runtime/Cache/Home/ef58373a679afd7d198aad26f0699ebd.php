<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/admin/js/movedata.js"></script>
<script type="text/javascript" src="/Public/admin/js/checkall.js"></script>
</head>

<body>
<div id="spacemenu"></div>
<div class="alterdiv"></div>
<div id="maindiv">
<?php if($total > 0): ?><form id="form1" name="form1" method="post"  action="/hadmin.php/Home/GoodsList/del?id=<?php echo ($id); ?>">
    <div id="navdiv">
        <table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td width="20%" height="41">选中文章 <?php echo ($moveselpcls); ?></td>
    <td width="46%" align="right">检索标题：<input type="text" name="kwords" id="kwords" class="htinputcss" /><input type="button" name="button3" id="button3" value="搜索" class="ssbtn" onClick="this.form.action='/hadmin.php/Home/GoodsList/manage?id=<?php echo ($id); ?>';this.form.submit()" onFocus="this.blur()" />&nbsp;&nbsp;&nbsp;&nbsp;</td>
    <td width="38%">您当前的位置：栏目管理 &gt;&gt; <?php echo ($cname); ?></td>
  </tr>
</table>
    </div>

<table width="100%" border="0" cellspacing="1" cellpadding="0" bgcolor="#e2e2e2" class="backfont">
  <tr bgcolor="#f7f7f7">
    <td width="8%" height="28" align="center"><input type="checkbox" onclick="CheckAll(this.form)" name="chkAll" value="checkbox" /></td>
    <td width="8%" align="center">ID</td>
    <td width="30%" align="center">文章标题</td>
    <td align="center">图片预览</td>
    <td width="11%" align="center">排序</td>
    <td width="12%" align="center">添加时间</td>
    <td width="11%" align="center">管理</td>
  </tr>
<?php if(is_array($datalist)): $i = 0; $__LIST__ = $datalist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr bgcolor="#FFFFFF" onmouseover="this.style.backgroundColor='#f7f7f7'" onmouseout="this.style.background='#FFFFFF'">
    <td height="52" align="center"><input type="checkbox" name="del[]" value="<?php echo ($vo["qid"]); ?>" onclick="unselectall()" /></td>
    <td align="center"><?php echo ($vo["qid"]); ?></td>
    <td align="center"><?php echo ($vo["title"]); ?></td>
    <td align="center"><a href="/uploadfiles/<?php echo ($vo["photo"]); ?>" target="_blank"><img src="/uploadfiles/<?php echo ($vo["photo"]); ?>" width="163" height="98" border="0" /></a></td>
    <td align="center"><input type="text" name="num[]" id="num[]" class="htinputcss" style="width:50px;" value="<?php echo ($vo["num"]); ?>" /><input name="numid[]" type="hidden" id="numid[]"  value="<?php echo ($vo["qid"]); ?>" /></td>
    <td align="center"><?php echo ($vo["dates"]); ?></td>
    <td align="center">
    	<?php if($cdata["fun"] == 'card'): ?><a href="/hadmin.php/Home/GoodsList/cardlist?id=<?php echo ($vo["qid"]); ?>&page=<?php echo ($page); ?>&cid=<?php echo ($id); ?>" class="editblue">内容添加</a>&nbsp;<?php endif; ?>
      	<a href="
        <?php if($cdata["fun"] == 'pro'): ?>/hadmin.php/Home/GoodsList/editgoods?id=<?php echo ($vo["qid"]); ?>&page=<?php echo ($page); ?>&cid=<?php echo ($id); echo ($strname); ?>
        <?php elseif($cdata["fun"] == 'card'): ?>
        	/hadmin.php/Home/GoodsList/editcard?id=<?php echo ($vo["qid"]); ?>&page=<?php echo ($page); ?>&cid=<?php echo ($id); echo ($strname); endif; ?>
        " class="editblue">修改</a>
      </td>
  </tr><?php endforeach; endif; else: echo "" ;endif; ?>
  <tr bgcolor="#FFFFFF">
    <td height="52" colspan="7" align="left">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delbtn" id="delbtn" class="delbtn" value="删除" onclick="return confirm('确认要删除吗？')" />&nbsp;&nbsp;
      <input type="button" name="button4" id="button4" class="orderbtn" value="修改排序" onClick="this.form.action='/hadmin.php/Home/GoodsList/order?id=<?php echo ($id); ?>';this.form.submit();" onFocus="this.blur()" /></td>
    </tr>
</table>
</form>
<?php else: ?>
<br><center class='gray linkblue'>没有任何内容 ---- <a href='javascript:;' onclick='history.go(-1)'>返回</a></center><?php endif; ?>
</div>
<div id="pagediv">
	<?php echo ($getpage); ?>
</div>

</body>
</html>