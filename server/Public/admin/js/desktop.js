//设置desktop的高
$(function(){
	
	//鼠标放上去显示删除快捷方式和更换图标
	$(".modules").hover(function(){
		$(this).children(".imgdivs").children(".delico").css("display","block");	
		$(this).children(".imgdivs").children(".chgico").css("display","block");
		
	},function(){
		$(this).children(".imgdivs").children(".delico").css("display","none");
		$(this).children(".imgdivs").children(".chgico").css("display","none");	
	});
	
	//框架desktop的高
	$("#desktop").css("height",$(window).height());
});
//板块拖拽
	function idrag(pUrl,eleUrl){
		$(".modules").bind('mouseover',function(){
			$(this).css("cursor","move")
		});
		
		var $orderlist = $("#orderlist");
		var $list = $("#main");
		var fAttr=null;
		$list.sortable({
			opacity: 0.6,
			revert: true,
			cursor: 'move',
			start:function(){
				var imgdivs=document.getElementsByClassName("imgdivs");
				for(var i=0;i<imgdivs.length;i++){
					imgdivs[i].onclick=function(){
						return false;	
					}	
				}
			
			},
			beforeStop:function(){
				var imgdivs=document.getElementsByClassName("imgdivs");
				for(var i=0;i<imgdivs.length;i++){
					imgdivs[i].onclick=function(){
						addNav(this.getAttribute("data-title"),this.getAttribute("data-url"),eleUrl);	
					};
				}
				
			},
			update: function(){
				 var new_order = [];
				 $list.children(".modules").each(function() {
					new_order.push($(this).attr("val"));
				 });
				 var newid = new_order.join(',');
				 $.ajax({
					type: "post",
					url: pUrl,
					data: { newid: newid},   //id:新的排列对应的ID,order：原排列顺序
					success: function(msg) {
					}
				 });
			}
		});	
	}
//点击添加到下面的导航菜单
function addNav(text,lnk,path){
	var iframe=$("#main",parent.document);
	var cardborder=$("#cardborder",parent.document);
	var isNull=true;
	
	cardborder.children('div').removeClass('current');
	
	//如果有相同的便签就不添加
	var aCards=cardborder.children("div");
	var selectCardsWidth=(cardborder.children("div").width()+5)*cardborder.children("div").length;
	aCards.each(function(i){
		if($(this).attr("val")==lnk){
			$(this).addClass("current");
			iframe.attr("src",$(this).attr("val"));
			if(selectCardsWidth>$("#card",parent.document).width()){
				cardborder.css("left",-$(this).width()*i+'px');
			}
			isNull=false;
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
		if(cardsWidth>$("#card",parent.document).width()){
			$("#left",parent.document).css("display","block");
			$("#right",parent.document).css("display","block");
			
			//添加的便签自动显示最后一个
			var cardWidthHalf=parseInt(cardborder.width()/2);
			cardborder.css("left",-cardWidthHalf+'px');
			
		}
	}
}

//删除快捷方式
function delShort(oId,e,pUrl,ajxurl){
	e.cancelBubble=true;//防止事件起泡
	if(confirm('确认要删除吗?')){
		$.ajax({
			type:"get",
			url:ajxurl,
			data:{id:oId},
			success: function(data){
				location.href=pUrl;	
			}	
		});
	}
}

//改变图标
var picurl;
function chgico(oEle,oEv,oId,path,purl){
	oEv.cancelBubble=true;
	$(oEle).parent().parent().parent().children(".modules").css("z-index","10");
	$(oEle).parent().parent().css("z-index","99");
	picurl=purl;
	var str="";
	str="<div class='icosdiv' onclick='unBubble(event)' onmousemove='unBubble(event)' onmousedown='unBubble(event)'><div class='condiv'>";
	for(var i=1;i<=28;i++){
		str+="<div><img src='"+path+"/admin/ico/"+i+".png' border='0' onclick='picLib(this,"+oId+")' /></div>";
	}
	str+="</div><div class='close' onclick='icoClose(this,event)'></div></div>";
	$(oEle).parent().append(str);
	var icons=$(oEle).parent().children('.icosdiv');
	var eleLeft=$(oEle).parent().offset().left;
	var winWidth=$(window).width();
	if(winWidth-eleLeft<350){
		icons.css("left","-422px");
	}
	
}

//点击图标库里的图片
function picLib(oEle,oId){
	var sPic=$(oEle).attr('src');
	$(oEle).parent().parent().parent().parent().children("img").attr('src',sPic);
	$.ajax({
		type:"get",
		url:picurl,
		data:{id:oId,pic:sPic},
		success: function(){
		}	
	});
	
}

//关闭图片选项
function icoClose(oId,oEv){
	oEv.cancelBubble=true;
	$(oId).parent().remove();
}

//防止起泡
function unBubble(oEv){
	oEv.cancelBubble=true;
}

//改变图标标题
var oTextcon;
var tpurl;
function editText(oEl,oId,e,pUrl){
	tpurl=pUrl;
	e.cancelBubble=true;
	oTextcon=$(oEl).html();
	$(oEl).html("<input onclick='unBubble(event)' name='t"+oId+"' id='t"+oId+"' onkeydown='changeText(event,this,"+oId+",true)' onblur='changeText(event,this,"+oId+",false)' type='text' style='width:100%;height:100%;border:0px; none;' value='"+oTextcon+"'>");
	document.getElementById("t"+oId).select();
}

function changeText(e,oEl,oId,iskey){
	e.cancelBubble=true;
	if(iskey){
		if(e.keyCode==13){//回车键
			if($(oEl).val()==""){
				$(oEl).parent().html(oTextcon);
			}else{
				chgTextData(oEl,oId);
			}
		}else if(e.keyCode==27){//esc键
			$(oEl).val(""+oTextcon+"");
			$(oEl).parent().html(oTextcon);
		}
	}else{
		if($(oEl).val()==""){
			$(oEl).parent().html(oTextcon);
		}else{
			chgTextData(oEl,oId);
		}
	}
}

function chgTextData(oEl,oId){
	$(oEl).parent().html($(oEl).val());
	$.ajax({
		type:"post",
		url:tpurl,
		data:{tid:oId,text:$(oEl).val()},
		success: function(data){
		}	
	});	
}