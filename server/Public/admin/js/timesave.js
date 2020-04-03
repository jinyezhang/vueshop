// JavaScript Document
function getContentValue()
{
var content= CKEDITOR.instances.bodys.getData()
var re3=/\&[a-z]+\;|\&[0-9]+\;|\&[a-z][0-9]+\;|\&[0-9][a-z]+\;|\&#[0-9]+\;|\&#[a-z]+\;/g;
	acontent=content.replace(re3,"");
	
	return acontent;
}

function clicksave(){
	//var bodys=document.form1.bodys.value;
	//var fbodys=parent.document.form1.bodys.value;
	$.post("index.php",{m:"formsave",action:"bodys",bodys:getContentValue()});
	
}
function save(){
	setInterval(function(){
		clicksave();
	},10000)
}
save();