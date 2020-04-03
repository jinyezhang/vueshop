
function unselectall()
{
	var columns=document.getElementById("columns");
	var index=document.getElementsByName("del[]");
	var   checkNum=0; 
    for (var i=0;i<index.length;i++){
		if(index[i].checked){
			checkNum++;
		}
	}
	if(checkNum==0){
		columns.disabled="disabled";
	}else{
		columns.disabled="";
	}
	
}

function CheckAll(form)
{
	var columns=document.getElementById("columns");
	var chkall=document.getElementById("chkAll");
  for (var i=0;i<form.elements.length;i++)
    {
    var e = form.elements[i];
    if (e.Name != "chkAll"&&e.disabled!=true)
       e.checked = form.chkAll.checked;
	   if(e.checked){
		   columns.disabled="";
		}else{
			columns.disabled="disabled";
		}
    }
}

function cusCheckAll(form)
{
	var customer=document.getElementById("customer");
	var chkall=document.getElementById("chkAll");
  for (var i=0;i<form.elements.length;i++)
    {
    var e = form.elements[i];
    if (e.Name != "chkAll"&&e.disabled!=true)
       e.checked = form.chkAll.checked;
	   if(e.checked){
		   customer.disabled="";
		}else{
			customer.disabled="disabled";
		}
    }
}

function cusselectall()
{
	var customer=document.getElementById("customer");
	var index=document.getElementsByName("del[]");
	var   checkNum=0; 
    for (var i=0;i<index.length;i++){
		if(index[i].checked){
			checkNum++;
		}
	}
	if(checkNum==0){
		customer.disabled="disabled";
	}else{
		customer.disabled="";
	}
	
}