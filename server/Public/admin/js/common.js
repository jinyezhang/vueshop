

	function newgdcode(obj,url) {
		//后面传递一个随机参数，否则在IE7和火狐下，不刷新图片
		obj.src = url+ '?nowtime=' + new Date().getTime();
	}





