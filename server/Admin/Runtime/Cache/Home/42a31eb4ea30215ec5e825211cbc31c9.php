<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="/vueshopserver/Public/admin/css/defaults.css" rel="stylesheet" type="text/css" />
<title>无标题文档</title>
<script type="text/javascript" src="/vueshopserver/Public/admin/js/jquery.js"></script>
<script type="text/javascript" src="/vueshopserver/Public/admin/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/vueshopserver/Public/admin/js/desktop.js"></script>
<script type="text/javascript">
$(function(){
idrag("/vueshopserver/hadmin.php/Home/Desktop/desktoporder","/vueshopserver/Public");
});
</script>
</head>

<body style="background:url(/vueshopserver/Public/admin/images/desktopbg.jpg);">
<div id="spacemenu"></div>
<div style="height:32px;width:100%"></div>
<div id="desktop">
	<div id="main">
    <?php if(is_array($arr)): $i = 0; $__LIST__ = $arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="modules" val="<?php echo ($vo["id"]); ?>">
        	<div class="imgdivs" data-title="<?php echo ($vo["title"]); ?>" data-url="<?php if($vo["cid"] != ''): ?>/vueshopserver/hadmin.php/Home/ColumnManage#<?php echo ($vo["cid"]); else: ?>/vueshopserver/hadmin.php/Home/<?php echo ($vo["webs"]); endif; ?>"  onclick="
            <?php if($vo["cid"] != ''): ?>addNav('<?php echo ($vo["title"]); ?>','/vueshopserver/hadmin.php/Home/ColumnManage#<?php echo ($vo["cid"]); ?>','/vueshopserver/Public')
            <?php else: ?>
            	addNav('<?php echo ($vo["title"]); ?>','/vueshopserver/hadmin.php/Home/<?php echo ($vo["webs"]); ?>','/vueshopserver/Public')<?php endif; ?>
            "><img src="/vueshopserver/Public/admin/ico/<?php echo ($vo["pic_path"]); ?>" width="77" height="77" border="0" />
            	<div class="delico">
                <?php if($vo["fid"] == ''): ?><img onclick="delShort('<?php echo ($vo["id"]); ?>',event,'/vueshopserver/hadmin.php/Home/Desktop','/vueshopserver/hadmin.php/Home/Desktop/delShort')" src="/vueshopserver/Public/admin/images/delico.png" title="删除快捷方式" /><?php endif; ?>
                </div>
                <div class="chgico" onclick="chgico(this,event,<?php echo ($vo["id"]); ?>,'/vueshopserver/Public','/vueshopserver/hadmin.php/Home/Desktop/modpic')"><img src="/vueshopserver/Public/admin/images/changeico.png" title="更换图标" /></div>
            </div>
            <div class="text" onclick="editText(this,<?php echo ($vo["id"]); ?>,event,'/vueshopserver/hadmin.php/Home/Desktop/edittext')" title="<?php echo ($vo["title"]); ?>"><?php echo ($vo["title"]); ?></div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
</body>
</html>