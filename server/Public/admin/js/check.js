// JavaScript Document
function check(){
	if (document.loginform.username.value=="账号"){
		alert ("请输入您的账号");
		document.loginform.username.focus();
		return false;
	}
	
	
	if (document.loginform.password.value=="******"){
		alert ("请输入您的密码");
		document.loginform.password.focus();
		return false;
	}
	
	if (document.loginform.vdcode.value.match(/^\s*$/)){
		alert ("请您输入验证码");
		document.loginform.vdcode.focus();
		return false;
	}
	
	
}

