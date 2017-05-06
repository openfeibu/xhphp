<?php

return [
	'real_name_url' => 'http://gzctsmrz.ews.m.jaeapp.com/web/real-order.action',
	'telecom_return_url' => config("app.web_url").'/html/telecom_order.html?device=android',
	'telecom_show_url'	=> config('app.url'),
	'order_return_url' => config("app.web_url").'/index.html#/work/all',
	'order_info_return_url' => config("app.web_url").'/shop/shop-paysucc.html',
	'order_show_url'	=> config('app.url'),
	'img_type' => [
		"jpg","gif","bmp","jpeg","png"
	],
	'shop_fee' => 0,
	'no_goods_img' => config('app.url').'/uploads/system/no_goods_image.png',
];