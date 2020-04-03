<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/admin/js/jquery.js"></script>
</head>

<body>
<div id="spacemanage"></div>
<div id="desmenu">
	<div id="movediv">
    	<div id="fold"><a href="javascript:;" id="menu_co">全部折叠</a></div>
        <div id="searchdiv">
        <form id="cform" name="cform" method="post" action="/hadmin.php/Home/ColumnManage">
        	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="gray">
  <tr>
    <td>检索一级目录：<input type="text" name="search" id="search" class="htinputcss" /><input type="submit" name="addbtn" id="addbtn" class="ssbtn" value="搜索" /></td>
    <td>&nbsp;</td>
  </tr>
</table>
	</form>
        </div>
        <div id="column" class="gray"><img src="/Public/admin/images/addcon.png" alt="内容管理" />:内容管理&nbsp;&nbsp;<img src="/Public/admin/images/addcol.png" alt="栏目添加" />:栏目添加&nbsp;&nbsp;<img src="/Public/admin/images/editcol.png" alt="栏目修改" />:栏目修改&nbsp;&nbsp;<img src="/Public/admin/images/delcol.png" alt="栏目删除" />:栏目删除&nbsp;&nbsp;<img src="/Public/admin/images/adddesk.png" alt="添加到桌面" />:添加到桌面</div>
    </div>
</div>
<div id="topmenu">
	<div id="movediv" class="gray">
    	<div style="width:50%;height:100%;float:left;">栏目名称</div>
        <div style="width:20%;height:100%;float:left;">栏目管理</div>
        <div style="width:20%;height:100%;float:left;">栏目排序</div>
        <div style="width:10%;height:100%;float:left;">栏目删除</div>
    </div>
</div>
<div id="colmain" class="backfont">
<?php if($total > 0): ?><div id="showmenu"><?php echo ($menu); ?></div>
<?php else: ?>
    <center>没有找到相关栏目，请输入一级栏目的名称。</center><?php endif; ?>
</div>


</div>
<script type="text/javascript">

//栏目排序
function ajxorder(action,pid,id,num){
	$.get("/hadmin.php/Home/ColumnManage/corder",{action:action,pid:pid,id:id,num:num,date:new Date().getTime()},function(data){
			$("#showmenu").html(data);
	})	
}

	$("#menu_co").click(function(){
		if($(".pdiv").attr("pvar")=='1'){
			$(".pdiv").css("display","none").attr("pvar",'0');
			$(this).html('全部展开');
			$(".pdiv").prev('div[onclick]').children(".movediv").children("div").children('img').attr("src","/Public/admin/images/plus.jpg");
			$(".pdiv").prev('div[onclick]').children(".movediv").children("div").children("div").children('img').attr("src","/Public/admin/images/plus.jpg");
			$(".pdiv").prev().removeClass("outerdiv2").addClass("outerdiv");
			$(".pdiv").prev().css("background-color","");
		}else{
			$(".pdiv").css("display","block").attr("pvar",'1');
			$(this).html('全部折叠');
			$(".pdiv").prev('div[onclick]').children(".movediv").children("div").children('img').attr("src","/Public/admin/images/minsign.jpg");
			$(".pdiv").prev('div[onclick]').children(".movediv").children("div").children("div").children('img').attr("src","/Public/admin/images/minsign.jpg");
			$(".pdiv").prev().removeClass("outerdiv").addClass("outerdiv2");
			
		}
	});
	
	//鼠标滑过变色
	var oColor=null;
	function overColumn(my){
		oColor=$(my).css("background-color");
		$(my).css("background-color","#e5e4e4");
		$(my).children(".movediv").children("div").children(".deskico").css("display","block");
	}
	function outColumn(my){
		my.style.background=oColor;
		$(my).css("background-color",oColor);
		$(my).children(".movediv").children("div").children(".deskico").css('display',"none");
	}
	
	//点击栏目折叠展开
	function oncard(id){
		var cid=$("#c"+id);
		if(cid.next().is(":visible")){//隐藏
			cid.css("background-color","#FFF");
			cid.next().css("display","none");
			cid.children(".movediv").children("div").children("img").attr('src','/Public/admin/images/plus.jpg');
			cid.children(".movediv").children("div").children("div").children("img").attr('src','/Public/admin/images/plus.jpg');
			cid.removeClass("outerdiv2").addClass("outerdiv");
		}else{//显示
			cid.next().css("display","block");
			cid.children(".movediv").children("div").children("img").attr('src','/Public/admin/images/minsign.jpg');
			cid.children(".movediv").children("div").children("div").children("img").attr('src','/Public/admin/images/minsign.jpg');
			cid.removeClass("outerdiv").addClass("outerdiv2");
		}
	}
	
	//添加到桌面
	function addDesk(e,oId){
		e.cancelBubble=true;
		$.ajax({
			type:"GET",
			url:"/hadmin.php/Home/ColumnManage/addDesk",
			data:{cid:oId},
			success: function(data){
				alert(data);
			}	
		});
	}
</script>
</body>
</html>