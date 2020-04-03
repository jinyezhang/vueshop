// JavaScript Document
function   moveOP(url){
if   (confirm( "确认要移动吗？")){ 
document.form1.action=url; 
document.form1.submit(); 
} 
}

function  allotCustomer(url){
if   (confirm( "确认要分配吗？")){ 
document.form1.action=url; 
document.form1.submit(); 
} 
}

function  unCustomer(url){
if   (confirm( "确认要撤销吗？")){ 
document.form1.action=url; 
document.form1.submit(); 
} 
}