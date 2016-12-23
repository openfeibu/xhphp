<?php

return [
	'shop_type' => [
		'1' => '学生',
		'2'	=> '商家',
	] ,
	'shop_status' => [
		'0'	=> '待审核',
		'1' => '正常',
		'2' => '审核不通过',
		'3' => '关闭',
	],
	'pay_name'  => [		
		'1' => '支付宝支付', 
		'2' => '微信支付',
		'3' => '余额支付',
		'4' => '系统返现',
		'5' => '校汇系统',
	],
	'trade_type' => [
		'ReleaseTask' => '发布任务',
		'AcceptTask'  => '接受任务',	
		'Withdrawals' => '提现',
		'WithdrawalsFail' => '提现失败',
		'CancelTask'  => '取消任务',
		'Shopping'	  => '购物',	
		'CancelOrder'  => '取消订单',
		'Shop' => '商店收入',
		'TelecomOrder' => '电信套餐',
		'FreeOrder' => '首单返现',
	],
	// user   wallet 字段
	'wallet_type' => [
		'1'  => '收入',
		'-1' => '支出',
	],
	// apply_wallet  status 提现申请状态
	'apply_wallet_status' => [
		'success' => '完成',
		'wait' => '待操作',
		'failed' => '申请失败'
	],
	// order_info   telecom_order  pay_status 订单支付状态
	'pay_status' => [
		'0' => '待付款',
		'1' => '已支付',
	],
	'shipping_status' => [
		'0' => '待发货',
		'1' => '发货中',
		'2' => '已收货',
		'3' => '已取消'
	],
	'order_status' => [
		'buyer' => [
			'0' => '未确认',
			'1' => '已确认',
			'2' => '已收货',
			'3' => '退款中',
			'4' => '已退款',
		],
		'seller' => [
			'0' => '未确认',
			'1' => '已确认',
			'2' => '已收款',
			'3' => '退款中',
			'4' => '已退款',
		],
		/*'buyer' => [
			'waitpay' => '待付款',
			'paysucc' => '待发货',
			'cancelling' => '退款中',
			'cancelled' => '已退款',
			'shipping' => '发货中',
			'succ' => '已完成',
		],
		'seller' => [
			'waitpay' => '未付款',
			'paysucc' => '待发货',
			'cancelling' => '待退款',
			'cancelled' => '已退款',
			'shipping' => '发货中',
			'succ' => '已收款',
		],*/
		
		
	],
    // trade_account 交易状态
	'trade_status' => [
		'success' => '支付成功',
		'refunding' => '退款中',
		'refunded' => '已退款',
		'income' => '已存入钱包',
		'cashing' => '提现中' ,
		'cashed' => '已提现',
		'cashfail' => '提现失败',
	],
	'task_type' => [
		'personal' => '个人',
		'business' => '商家',
	],
	//任务状态
	'task_status' => [
		'waitpay' => '待支付',
		'new' => '可接单',
		'cancelling' => '退款中',
		'cancelled' => '已退款',
		'accepted' => '已接单',
		'finish' => '待结算',
		'completed' => '已结算',
	],
	//社团成员等级
	'association_level' => [
		'0' => '成员',
		'1' => '会长',
		'2' => '管理员',
		'3' => '管理员',
	],
	//申请状态 
	'apply_status' => [
		'success' => '已转账',
		'wait' => '待转账',
		'failed' => '申请失败',
	],
	'telecom_real_name_status' => [
		'0' => '未实名',
		'1' => '已实名',
		'2' => '实名认证中',
		'3' => '全部',
	],
];