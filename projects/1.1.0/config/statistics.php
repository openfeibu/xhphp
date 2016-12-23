<?php

return [
	'user' =>[
		'name' => '用户模块',
		'interface' => [
			'register' => '注册',
			'login' => '登陆',
			'logout' => '注销',
			'resetPassword' => '重置密码',
			'changePassword' => '修改密码',
			'changeUserInfo' => '修改个人资料',
			'realNameAuth' => '实名认证',
			'getMyInfo' => '进入个人中心',
			'getOthersInfo' => '浏览他人资料',
			'uploadAvatarFile' => '更新头像',
			'getWallet' => '进入我的钱包',
			'walletAccount' => '浏览钱包明细',
			'bindAlipay' => '绑定支付宝',
			'changeAlipay' => '修改支付宝账号',
			'setPayPassword' => '设置支付密码',
			'changePayPassword' => '修改支付密码',
			'resetPayPassword' => '重置支付密码',
			'withdrawalsApply' => '提现申请',
		],
					
	],
	'order' =>[
		'name' => '任务模块',
		'interface' => [
			'getOrderList' => '进入任务列表',
			'getOrder' => '浏览任务',
			'createOrder' => '创建任务',
			'claimOrder' => '接任务',
			'getMyOrder' => '浏览已发任务',
			'getMyWork' => '浏览已接任务',
			'askCancel' => '取消任务',
			'agreeCancel' => '同意取消任务',
			'finishWork' => '完成任务',
			'confirmFinishWork' => '结算任务',	
		],
	],
	'association'=> [
		'name' => '社团模块',
		'interface' => [
			'join' => '申请入驻',
			'getActivityList' => '浏览活动列表',
			'getActivity' => '浏览活动详情',
			'getInformationList' => '浏览社团资讯',
			'getInfomation' => '浏览社团资讯详情',
			'createMessage' => '社长发布纸条',
			'createInformation' => '会长发布资讯',
			'createActivity' => '发布活动',
			'getProfile' => '浏览社团信息',
			'setProfile' => '设置社团信息',
			'getAssociations' => '浏览社团信息列表',
		],
	],
	'topic' => [
		'name' => '话题模块',
		'interface' => [
			'getTopic' => '浏览单个话题',
			'getMyTopic' => '浏览我的话题',
			'getMyComment' => '浏览我的评论',
			'getTopicList' => '浏览话题',
			'topic_getTopics' => '浏览话题',
			'createTopic' => '发话题',
			'deleteTopic' => '删除话题',
			'deleteComment' => '删除对话题的评论或对他人的回复',
			'undoDeleteTopic' => '撤销删除话题',
			'thumbUp' => '点赞',
			'search' => '话题搜索',
		],
	],
	'shop' => [
		'name' => '商城模块',
		'interface' => [
			'register' => '',
		],
	],
	'telecom' => [
		'name' => '电信模块',
		'interface' => [
			'queryRealName' => '实名认证',
			'telecomPackage' => '查看电信套餐',
			'telecomPackageStore' => '购买套餐',
			'getTelecomOrders' => '查看套餐',
			'getTransactorTelecomOrders' => '办理人查看套餐',
			'getTelecomOrdersCount' => '套餐统计'
		],
	],
	'other' => [
		'name' => '其他模块',
		'interface' => [
			'version' => '检查更新',
			'feedback' => '提交反馈',
			'integralList' => '查看积分明细',
			'integralLevel' => '查看积分等级',
			'integralExplain' => '查看积分说明',
			'integral_share' => '分享',
			'accusation' => '举报投诉',
		],
	],
];