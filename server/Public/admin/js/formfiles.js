//================上传文件JS函数开始，需和jquery.form.js一起使用===============
//单个文件上传
function SingleUpload(repath, uppath, action,url) {
    var submitUrl=""+url+"?ReFilePath="+repath+"&UpFilePath="+uppath+"&action="+action;
    //开始提交
    $("#form1").ajaxSubmit({
        beforeSubmit: function(formData, jqForm, options){
            //隐藏上传按钮
            $("#"+repath).nextAll(".files").eq(0).hide();
            //显示LOADING图片
            $("#"+repath).nextAll(".uploading").eq(0).show();
        },
        success: function(data, textStatus) {
            if (data.msg == 1) {
                $("#"+repath).val(data.msbox);
            } else {
                alert(data.msbox);
            }
            $("#"+repath).nextAll(".files").eq(0).show();
            $("#"+repath).nextAll(".uploading").eq(0).hide();
        },
        error: function(data, status, e) {
            alert("上传失败，错误信息：" + e);
            $("#"+repath).nextAll(".files").eq(0).show();
            $("#"+repath).nextAll(".uploading").eq(0).hide();
        },
        url: submitUrl,
        type: "post",
        dataType: "json",
        timeout: 600000
    });
};
//===========================上传文件JS函数结束================================