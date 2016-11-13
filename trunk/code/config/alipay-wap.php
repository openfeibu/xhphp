<?php
return [
	
	

	// 签名方式
	'sign_type' => 'RSA',
	
	'service' => "alipay.wap.create.direct.pay.by.user",

	'payment_type' => 1,
	
	// 异步通知连接。
	'notify_url' => 'http://xxx',


	//字符编码格式 目前支持 gbk 或 utf-8
	'input_charset' => strtolower('utf-8'),

	//ca证书路径地址，用于curl中ssl校验
	//请保证cacert.pem文件在当前文件夹目录中
	'cacert'    => getcwd().'/cacert.pem',

	//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
	'transport'    => 'http',
	
];
