<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<link href="/Public/admin/css/admin.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/Public/admin/js/jquery.js" ></script>
<script type="text/javascript" src="/Public/admin/js/jquery.form.js" ></script>
<script type="text/javascript" src="/Public/admin/js/formfiles.js" ></script>
<script type="text/javascript" src="/ckeds/ckeditor.js"></script>
<script type="text/javascript">
var count=1;
function check(pUrl)
{
    var specAttrid=document.getElementsByName("spec_attrid[]");
    var specAttrName=document.getElementsByName("spec_attrname[]");
	if (document.form1.title.value.match(/^\s*$/)){
		alert ("标题名称不能为空");
		document.form1.title.focus();
		return false;
	}

    for(var i=0;i<specAttrid.length;i++){
        var specNull=true;
        var specVal=document.getElementsByName("spec_val_"+specAttrid[i].value+"[]");
        for(var j=0;j<specVal.length;j++){
            if(!specVal[j].value.match(/^\s*$/)){
                specNull=false;
                break;
            }
        }
        if(specNull){
            alert("请输入"+specAttrName[i].value+"！");
            specVal[0].focus();
            break;
        }
    }
    if(specNull==false) {
        document.getElementById("button").disabled = true;
        document.form1.action = pUrl;
        document.form1.submit();
    }

}
$(function(){
    var strhtml="";
    $("#btn").click(function(){
        count++;
        strhtml='<div><div class="imgh"></div><input id="photo'+(count)+'" class="inputleft htinputcss" name="photo'+(count)+'" value="" type="text"><a class="files" href="javascript:void(0);"><input id="FileUpload'+(count)+'" onchange="SingleUpload(\'photo'+(count)+'\',\'FileUpload'+(count)+'\',\'images\',\'/hadmin.php/Home/GoodsList/upload\')" name="FileUpload'+(count)+'" type="file"></a><span class="uploading">正在上传，请稍候...</span><input type="button" value="删除" class="delbtn2" onclick="delimg(this)" /></div>';
        $("#show").append(strhtml);
        $("#imgnum").val(count);
    });


    //产品规格
    var specMain=$("#spec-main");
    specMain.on("click",".spec-checkbox",function(){
        var counts=$(this).parent().parent().find(".spec-list").length;
        var attrid=$(this).attr("data-id");
        if($(this).attr("checked")=='checked'){
            var html=appendStr(attrid);
            $(this).parent().parent().append(html);
        }else{
            if(counts>1) {
                $(this).parent().remove();
            }
        }
    });

    specMain.on("keyup",".spec-input",function(){
        var nextCount=$(this).parent().next().length;
        var attrid=$(this).attr("data-id");
        if(nextCount==0 && $(this).val()!=''){
            $(this).prev().attr("checked","checked");
            var html=appendStr(attrid);
            $(this).parent().parent().append(html);
        }
    });


});

function appendStr(name){
    var str='<div class="spec-list"><input type="checkbox" class="spec-checkbox" value="1" /><input type="text" name="spec_val_'+name+'[]" class="spec-input" value="" placeholder="请输入值" /></div>';
    return str;
}

//删除图片
function delimg(btn){
    $("#imgnum").val(--count);
    var nextEle=$(btn).parent().nextAll().find(".inputleft");
    if(nextEle.attr("id")!=undefined){
        $(btn).parent().nextAll().each(function(){
            var index=$(this).index()+1;
            $(this).find(".inputleft").attr({"id":"photo"+index,"name":"photo"+index});
            $(this).find("input[type='file']").attr({"id":"FileUpload"+index,"name":"FileUpload"+index});
            $(this).find("input[type='file']").attr("onchange",'SingleUpload(\'photo'+(index)+'\',\'FileUpload'+(index)+'\',\'images\',\'/hadmin.php/Home/GoodsList/upload\')');
        });
    }
    $(btn).parent().remove();
}
</script>
</head>

<body>
<div id="spacemenu">
	<div id="leftarrow"></div>
    <div id="return1"><a href="/hadmin.php/Home/GoodsList/manage?id=<?php echo ($id); ?>">返回上一级</a></div>
</div>
<div class="alterdiv"></div>
<div id="posdiv">
	<table width="94%" border="0" cellspacing="0" cellpadding="0" class="gray" align="center">
  <tr>
    <td height="46">您当前的位置：栏目管理 >> <?php echo ($cname); ?> </td>
  </tr>
</table>
</div>
<div id="maindiv">
<form id="form1" name="form1" method="post" action="">
<input name="imgnum" id="imgnum" type="hidden" value="1" />
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="green">
  <tr>
    <td width="28%" height="40" align="right"><font color="#FF0000">*</font> 标题：</td>
    <td width="72%" colspan="2"><input type="text" name="title" id="title" class="htinputcss" /></td>
  </tr>
  <tr>
  <td height="40" align="right">价格：</td>
  <td colspan="2"><input type="text" name="price" id="price" class="htinputcss" style="width:50px;" value="0" /> 元</td>
  </tr>
    <tr>
        <td height="40" align="right">运费：</td>
        <td colspan="2"><input type="text" name="freight" id="freight" class="htinputcss" style="width:50px;" value="0" /> 元</td>
    </tr>
    <?php if(is_array($oneattr)): $i = 0; $__LIST__ = $oneattr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo[ftype] == 'select'): ?><tr>
                <td height="40" align="right"><input name="sattrid[]" type="hidden" value="<?php echo ($vo[id]); ?>"><input type="hidden" name="sftype[]" value="<?php echo ($vo[ftype]); ?>"><?php echo ($vo[name]); ?>：</td>
                <td colspan="2">
                    <select name="sval[]">
                        <option value="">请选择</option>
                        <?php if(is_array($vo[sub])): $i = 0; $__LIST__ = $vo[sub];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><option value="<?php echo ($sub[id]); ?>"><?php echo ($sub[name]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select></td>
            </tr>
            <?php elseif($vo[ftype] == 'checkbox'): ?>
            <tr>
                <td height="40" align="right"><input name="cattrid[]" type="hidden" value="<?php echo ($vo[id]); ?>"><input type="hidden" name="cftype[]" value="<?php echo ($vo[ftype]); ?>"><?php echo ($vo[name]); ?>：</td>
                <td colspan="2">
                    <?php if(is_array($vo[sub])): $i = 0; $__LIST__ = $vo[sub];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><label><input type="checkbox" name="cval<?php echo ($vo[id]); ?>[]" value="<?php echo ($sub[id]); ?>" /><?php echo ($sub[name]); ?></label>&nbsp;&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
                </td>
            </tr><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    <?php if($specTotal > 0): ?><tr>
        <td height="40" align="right"><font color="#FF0000">*</font> 产品规格：</td>
        <td>
            <div id="spec-main" class="spec-main">
                <?php if(is_array($specData)): $i = 0; $__LIST__ = $specData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="hidden" name="spec_attrid[]" value="<?php echo ($vo['id']); ?>" />
                    <input type="hidden" name="spec_attrname[]" value="<?php echo ($vo['name']); ?>" />
                <div class="spec-row">
                    <div class="spec-title"><?php echo ($vo['name']); ?></div>
                    <div class="spec-content">
                        <div class="spec-list">
                            <input type="checkbox" data-id="<?php echo ($vo['id']); ?>" class="spec-checkbox" value="1" />
                            <input type="text" name="spec_val_<?php echo ($vo['id']); ?>[]" class="spec-input" value="" placeholder="请输入值" data-id="<?php echo ($vo['id']); ?>" />
                        </div>
                    </div>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </td>
    </tr><?php endif; ?>
    <tr>
        <td height="40" align="right">图片上传：</td>
        <td width="30%"><div>
            <input id="photo1" class="inputleft htinputcss" name="photo1" value="" type="text"><a class="files" href="javascript:void(0);"><input id="FileUpload1" onchange="SingleUpload('photo1','FileUpload1','images','/hadmin.php/Home/GoodsList/upload')" name="FileUpload1" type="file"></a><span class="uploading">正在上传，请稍候...</span>
        </div>
            <div id="show"></div>
        </td>
        <td valign="top"><input type="button" name="btn" id="btn" value="添加下一张" class="orderbtn" /></td>
    </tr>
  <tr>
    <td height="40" align="right">内容：</td>
    <td colspan="2"><textarea name="bodys" id="bodys" class="ckeditor" cols="45" rows="5"></textarea></td>
  </tr>
  <tr>
    <td height="40" align="right">&nbsp;</td>
    <td><input type="button" name="button" id="button" value="提交" class="addbtn" onclick="check('/hadmin.php/Home/GoodsList/goods?id=<?php echo ($id); ?>&action=add')" />&nbsp;&nbsp;
      <input type="reset" name="button2" id="button2" value="重置" class="resbtn" /></td>
  </tr>
</table>
</form>
</div>
</body>
</html>