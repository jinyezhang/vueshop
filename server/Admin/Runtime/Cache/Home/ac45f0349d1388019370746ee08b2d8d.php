<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>后台管理系统</title>
<link href="/vueshopserver/Public/admin/css/defaults.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/vueshopserver/Public/admin/js/jquery.js"></script>
<script type="text/javascript" src="/vueshopserver/Public/admin/js/defaults.js"></script>
</head>

<body>
<div id="header">
	<div id="menu">
    	<div id="leftmenu"><img src="/vueshopserver/Public/admin/images/menu.png" border="0" />
        	<div id="downmenu">
            	<div id="downmenu1"></div>
                <div id="downmenu2">
                	<div id="showadmin">
                    	<div id="adminimg"><img src="/vueshopserver/Public/admin/images/myico.png" width="29" height="30" /></div>
                        <div id="name"><?php echo ($adminname); ?><br /><?php echo ($groupname); ?></div>
                    </div>
                    <div id="menucon">
                    	<ul class="ulnone">
                        	<li onclick="addNav('我的桌面','/vueshopserver/hadmin.php/Home/Desktop/','/vueshopserver/Public')">●&nbsp;我的桌面</li>
                        <?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li onclick="addNav('<?php echo ($vo["title"]); ?>','/vueshopserver/hadmin.php/Home/<?php echo ($vo["webs"]); ?>','/vueshopserver/Public')">●&nbsp;<?php echo ($vo["title"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                            <li><a href="/vueshopserver/hadmin.php/Home/Default/outlogin" target="_top">●&nbsp;安全退出</a></li>
                        </ul>
                    </div>
                </div>
                <div id="downmenu3"></div>
            </div>
        </div>
        <div id="cardmenu">
        	<div id="left"><a href="javascript:;"><img src="/vueshopserver/Public/admin/images/left.png" border="0" /></a></div>
            <div id="right"><a href="javascript:;"><img src="/vueshopserver/Public/admin/images/right.png" border="0" /></a></div>
            <div id="card">
            	<div id="cardborder">
                    <div class="current" onclick='onMenu(this)' val="/vueshopserver/hadmin.php/Home/Desktop/">我的桌面</div>
                </div>
            </div>
        </div>
    </div>
	<div id="logo">电商系统后台管理系统</div>
    <div id="rdiv">
    	<ul class="ulnone">
        	<li class="date"><?php echo ($nowdate); ?> <?php echo ($weekarray); ?> <?php echo ($nowtime); ?> </li>
            <li class="logintime">上次登录：<?php echo ($logintime); ?></li>
            <li class="funimg"><a href="javascript:void(0)" onclick="javascript:window.top.frames['main'].document.location.reload();"><img src="/vueshopserver/Public/admin/images/refresh.png" title="刷新" border="0" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:;" onclick="window.open('/vueshopserver/hadmin.php/Home/Funset','_blank','width=800,height=600,top=50,left=230,scrollbars=yes')"><img src="/vueshopserver/Public/admin/images/function.png" title="功能设置" border="0" /></a></li>
        </ul>
    </div>
</div>
<div id="framediv">
	<iframe id="main" name="main" src="/vueshopserver/hadmin.php/Home/Desktop/" frameborder="0" width="100%" scrolling="auto"></iframe>
</div>
<div id="footer">Copyright © 2016 电商系统 All Rights Reserved | IP：<?php echo ($ip); ?></div>
</body>
</html>