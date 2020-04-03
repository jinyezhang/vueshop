<?php
return array(
	array('/^token$/','Token/index','',array('ext'=>'','method'=>'get')),
	array('/^column\/([0-9]+)$/','Index/column?id=:1','',array('ext'=>'','method'=>'get')),
	
	//快讯分页
	array('/^home\/news\/([0-9]+)\/([0-9]+)$/','home/news/index?cid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//首页幻灯片
	array('/^slide\/([0-9]+)\/([0-9]+)$/','Index/slide?offset=:1&length=:2','',array('ext'=>'','method'=>'get')),
	
	
	//article文章页面
	array('/^article\/([0-9]+)\/([0-9]+)$/','News/article?id=:1&aid=:2','',array('ext'=>'','method'=>'get')),
	
	//搜索页面
	array('/^search\/_(.*)\/([0-9]+)$/','Search/index?kwords=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//获取app版本号及信息
	array('/^appinfo$/','AppInfo/index','',array('ext'=>'','method'=>'get')),
	
	//获取用户信息
	array('/^userinfo\/([0-9]+)$/','user/myinfo/userInfo?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//获取出行人信息
	array('/^user\/tperson\/([0-9]+)$/','User/tperson/index?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//删除出行人
	array('/^user\/tperson\/del\/([0-9]+)\/([0-9]+)$/','User/tperson/del?uid=:1&id=:2','',array('ext'=>'','method'=>'get')),
	
	//删除发布活动
	array('/^seller\/activity\/del\/([0-9]+)\/([0-9]+)$/','seller/activity/del?uid=:1&id=:2','',array('ext'=>'','method'=>'get')),
	
	//获取发布活动列表
	array('/^seller\/activity\/actlist\/([0-9]+)\/([0-9]+)$/','seller/activity/actlist?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//获取发布活动详情
	array('/^seller\/activity\/actdesc\/([0-9]+)\/([0-9]+)$/','seller/activity/actdesc?uid=:1&id=:2','',array('ext'=>'','method'=>'get')),
	
	//删除发布活动图片
	array('/^seller\/activity\/delimage\/([0-9]+)$/','seller/activity/delimage?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//获取验证信息
	array('/^user\/verify\/info\/([0-9]+)$/','user/verify/info?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//小熊快讯名称
	array('/^home\/news\/name\/([0-9]+)$/','home/news/name?cid=:1','',array('ext'=>'','method'=>'get')),
	
	//小熊导航
	array('/^home\/news\/menu\/([0-9]+)$/','home/news/menu?cid=:1','',array('ext'=>'','method'=>'get')),
	
	//小熊快讯详情
	array('/^home\/news\/article\/([0-9]+)\/([0-9]+)$/','home/news/article?cid=:1&aid=:2','',array('ext'=>'','method'=>'get')),
	
	//活动分类数据
	array('/^home\/activity\/([0-9]+)\/([0-9]+)$/','home/activity/index?actclassid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//活动详情
	array('/^home\/activity\/desc\/([0-9]+)\/(.*)$/','home/activity/desc?actid=:1&action=:2','',array('ext'=>'','method'=>'get')),
	
	//筛选
	array('/^home\/activity\/screen\/(.*)$/','home/activity/screen?action=:1','',array('ext'=>'','method'=>'get')),
	
	//选择活动数据
	array('/^home\/activity\/choicedata\/([0-9]+)$/','home/activity/choicedata?pid=:1','',array('ext'=>'','method'=>'get')),
	
	//选择数量
	array('/^home\/activity\/choicenum\/([0-9]+)$/','home/activity/choicenum?dateid=:1','',array('ext'=>'','method'=>'get')),
	
	//选择数量
	array('/^home\/activity\/calendar\/([0-9]+)$/','home/activity/calendar?pid=:1','',array('ext'=>'','method'=>'get')),
	
	//选择时间
	array('/^home\/activity\/choicedate\/([0-9]+)$/','home/activity/choicedate?dateid=:1','',array('ext'=>'','method'=>'get')),
	
	//我的优惠券
	array('/^home\/order\/mycoupon\/([0-9]+)$/','home/order/mycoupon?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//会员待付款订单页面
	array('/^user\/myorder\/pending\/([0-9]+)\/([0-9]+)$/','user/myorder/pending?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//会员未出行订单页面
	array('/^user\/myorder\/paid\/([0-9]+)\/([0-9]+)$/','user/myorder/paid?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//已完成订单
	array('/^user\/myorder\/fulfil\/([0-9]+)\/([0-9]+)$/','user/myorder/fulfil?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//订单详情
	array('/^user\/myorder\/desc\/([0-9]+)\/([0-9]+)$/','user/myorder/desc?uid=:1&ordernum=:2','',array('ext'=>'','method'=>'get')),
	
	//我的优惠券（已使用和未使用）
	array('/^user\/coupon\/([0-9]+)\/([0-9]+)$/','user/coupon/index?uid=:1&isused=:2','',array('ext'=>'','method'=>'get')),
	
	//我的优惠券（已过期）
	array('/^user\/coupon\/expired\/([0-9]+)$/','user/coupon/expired?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//收藏活动
	array('/^home\/activity\/fav\/([0-9]+)\/([0-9]+)$/','home/activity/fav?uid=:1&actid=:2','',array('ext'=>'','method'=>'get')),
	
	//是否收藏活动
	array('/^home\/activity\/isfav\/([0-9]+)\/([0-9]+)$/','home/activity/isfav?uid=:1&actid=:2','',array('ext'=>'','method'=>'get')),
	
	//我的收藏
	array('/^user\/fav\/([0-9]+)\/([0-9]+)$/','user/fav/index?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//取消订单
	array('/^user\/myorder\/clearorder\/([0-9]+)\/([0-9]+)$/','user/myorder/clearorder?uid=:1&ordernum=:2','',array('ext'=>'','method'=>'get')),
	
	//商家待付款订单
	array('/^seller\/myorder\/pending\/([0-9]+)\/([0-9]+)$/','seller/myorder/pending?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家全部订单
	array('/^seller\/myorder\/([0-9]+)\/([0-9]+)$/','seller/myorder/index?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家已完成订单
	array('/^seller\/myorder\/fulfil\/([0-9]+)\/([0-9]+)$/','seller/myorder/fulfil?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家已付款订单
	array('/^seller\/myorder\/paid\/([0-9]+)\/([0-9]+)$/','seller/myorder/paid?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家改变确认订单状态
	array('/^seller\/myorder\/changestatus\/([0-9]+)\/([0-9]+)$/','seller/myorder/changestatus?uid=:1&ordernum=:2','',array('ext'=>'','method'=>'get')),
	
	//商家订单详情
	array('/^seller\/myorder\/desc\/([0-9]+)\/([0-9]+)$/','seller/myorder/desc?uid=:1&ordernum=:2','',array('ext'=>'','method'=>'get')),
	
	//财务统计
	array('/^seller\/finance\/([0-9]+)\/([0-9]+)$/','seller/finance/index?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//财务统计总收入
	array('/^seller\/finance\/income\/([0-9]+)$/','seller/finance/income?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家订单数量
	array('/^seller\/index\/ordercount\/([0-9]+)$/','seller/index/ordercount?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//商家发布活动数量
	array('/^seller\/index\/actcount\/([0-9]+)$/','seller/index/actcount?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//出行人详情
	array('/^user\/tperson\/desc\/([0-9]+)\/([0-9]+)$/','user/tperson/desc?uid=:1&tpid=:2','',array('ext'=>'','method'=>'get')),
	
	//评价信息
	array('/^home\/reviews\/([0-9]+)\/([0-9]+)$/','home/reviews/index?targetid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//我的评价
	array('/^user\/reviews\/([0-9]+)\/([0-9]+)$/','user/reviews/index?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家我的评论
	array('/^seller\/reviews\/([0-9]+)\/([0-9]+)$/','seller/reviews/index?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//商家我的评论数量
	array('/^seller\/reviews\/reviewscount\/([0-9]+)$/','seller/reviews/reviewscount?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//我的私人定制
	array('/^user\/customized\/([0-9]+)\/([0-9]+)$/','user/customized/index?uid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//私人定制详情
	array('/^user\/customized\/desc\/([0-9]+)\/([0-9]+)$/','user/customized/desc?uid=:1&cusid=:2','',array('ext'=>'','method'=>'get')),
	
	//商家信息
	array('/^home\/shop\/info\/([0-9]+)$/','home/shop/info?targetid=:1','',array('ext'=>'','method'=>'get')),
	
	//商家活动展示
	array('/^home\/shop\/([0-9]+)\/([0-9]+)$/','home/shop/index?targetid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//我的消息
	array('/^user\/pushmsg\/([0-9]+)$/','user/pushmsg/index?page=:1','',array('ext'=>'','method'=>'get')),
	
	//我的消息详情
	array('/^user\/pushmsg\/desc\/([0-9]+)$/','user/pushmsg/desc?msgid=:1','',array('ext'=>'','method'=>'get')),
	
	//单篇信息
	array('/^home\/public\/singleinfo\/([0-9]+)$/','home/public/singleinfo?cid=:1','',array('ext'=>'','method'=>'get')),
	
	//评价后的服务项
	array('/^home\/reviews\/serviced\/([0-9]+)$/','home/reviews/serviced?targetid=:1','',array('ext'=>'','method'=>'get')),
	
	//剩余限制人数
	array('/^user\/myorder\/limitman\/([0-9]+)\/([0-9]+)$/','user/myorder/limitman?uid=:1&ordernum=:2','',array('ext'=>'','method'=>'get')),
	
	//注册题库
	array('/^home\/faq\/([0-9]+)$/','home/faq/index?page=:1','',array('ext'=>'','method'=>'get')),
	
	//是否回答过
	array('/^home\/faq\/isfaq\/([0-9]+)$/','home/faq/isfaq?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//留言
	array('/^home\/message\/([0-9]+)\/([0-9]+)$/','home/message/index?actid=:1&page=:2','',array('ext'=>'','method'=>'get')),
	
	//获取宝宝信息
	array('/^user\/myinfo\/getbaby\/([0-9]+)$/','user/myinfo/getbaby?uid=:1','',array('ext'=>'','method'=>'get')),
	
	//推荐活动页面导航
	array('/^home\/activity\/nav\/([0-9]+)$/','home/activity/nav?page=:1','',array('ext'=>'','method'=>'get')),
	
);