$(function(){
	//下拉菜单
	var timeoutid=null;
	var timeoutid2=null;
	//下拉菜单
	$("#leftmenu").hover(function(){
		clearTimeout(timeoutid);
		timeoutid2=setTimeout(function(){
			$("#downmenu").show(100)
		},100);
			
	},function(){
		clearTimeout(timeoutid2);
		timeoutid=setTimeout(function(){
			$("#downmenu").hide(100);
		},100);	
	});
	
	//选项卡滚动
	//计算出card下所有div的宽
	function cardScroll(){
		var cardsWidth=($("#cardborder").children("div").width()+5)*$("#cardborder").children("div").length;
		var cardWidth=$("#cardborder").css("width",cardsWidth);
		var count=0;
		var cardWidthHalf=0;
		
		//箭头自动出来
		if(cardsWidth>$("#card").width()){
			$("#left").css("display","block");
			$("#right").css("display","block");
		}
		
		//点击左箭头滚动
		$("#left").live("click",function(){
			count=Math.ceil(parseInt($("#cardborder").css("left")));
			if(count<-$("#cardborder").children("div").eq(0).width()+5){
				count=Math.ceil(parseInt($("#cardborder").css("left"))+$("#cardborder").children("div").eq(0).width()+5);
			}else{
				count=0;
			}
			$("#cardborder").animate({"left":count+'px'});
		});
		
		//点击右箭头
		$("#right").live("click",function(){
			cardWidthHalf=Math.ceil((($("#cardborder").children("div").eq(0).width()+5)*$("#cardborder").children("div").length))-$("#card").width();
			if(Math.abs(count)<cardWidthHalf){
				count=Math.ceil(parseInt($("#cardborder").css("left"))-$("#cardborder").children("div").eq(0).width()+5);
				$("#cardborder").animate({"left":count+'px'});
			}
		});
	}
	cardScroll();
	
	//框架framediv的高
	$("iframe").css("height",$(window).height()-148);
	
});

//点击关闭
function onClose(id,e){
	e.cancelBubble=true;//防止事件起泡
	var carddiv=$(id).parent();
	var iframe=$("#main");
	var cardborder=$("#cardborder");
	
	cardborder.children('div').removeClass("current");
	
	//上一个元素改变样式
	carddiv.prev().addClass("current");
	
	//改变上一个元素的框架链接
	iframe.attr("src",carddiv.prev().attr("val"));
	
	//删除当前元素
	carddiv.remove();
	
	
}

//点击导航栏目便签切换
function onMenu(id){
	var menudiv=$("#cardborder div");
	menudiv.removeClass("current");
	
	$(id).addClass("current");
	$("#main").attr("src",$(id).attr("val"));
}

//点击添加到下面的导航菜单
function addNav(text,lnk,path){
	var iframe=$("#main");
	var cardborder=$("#cardborder");
	var isNull=true;
	
	cardborder.children('div').removeClass('current');
	
	//如果有相同的便签就不添加
	var aCards=cardborder.children("div");
	var selectCardsWidth=(cardborder.children("div").width()+5)*cardborder.children("div").length;
	aCards.each(function(i){
		if($(this).attr("val")==lnk){
			$(this).addClass("current");
			iframe.attr("src",$(this).attr("val"));
			isNull=false;
			if(selectCardsWidth>$("#card").width()){
				cardborder.css("left",-$(this).width()*i+'px');
			}
			return false;
		}
	});
	if(isNull){
		var menu="<div class='current' val='"+lnk+"' title='"+text+"' onclick='onMenu(this)'>"+text+"<div class='close' onclick='onClose(this,event)'><img src='"+path+"/admin/images/m_close.png' width='9' height='10' title='关闭' /></div></div>";
		cardborder.append(menu);
		iframe.attr("src",lnk);
		
		var cardsWidth=(cardborder.children("div").width()+5)*cardborder.children("div").length;
		cardborder.css("width",cardsWidth);
		
		//箭头自动出来
		if(cardsWidth>$("#card").width()){
			$("#left").css("display","block");
			$("#right").css("display","block");
			
			//添加的便签自动显示最后一个
			var cardWidthHalf=parseInt(cardborder.width()/2);
			cardborder.css("left",-cardWidthHalf+'px');
			
		}
	}
}