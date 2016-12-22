<?php 

if (!function_exists('escape_content')) {
	function escape_content($content)
	{
		if ($content == base64_encode(base64_decode($content))){
			if(preg_match("/^\d*$/",$content)){
				return $content;
			}elseif(preg_match("/^[a-z]+$/",$content)){
				return $content;
			}else{	
				return base64_decode($content);
			} 
		}	
		return $content;
	}
}
if (!function_exists('round_route')) {
	function round_route()
	{
		return [
			'topic_getTopicList',
			'topic_getTopics',
			'topic_getTopicCommentsList',
			/*'shop_getShopGoodses',*/
		];
	}
}
if (!function_exists('check_refund_order_info')) {
	function check_refund_order_info($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=0 || $order_status >=2 ){
			return false;
		}
		return true;
	}
}
if (!function_exists('dtime')) {
	function dtime()
	{
		return date('Y-m-d H:i:s');
	}
}
if (!function_exists('seller_check_refund_order_info')) {
	function seller_check_refund_order_info($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=0 || $order_status !=3 ){
			return false;
		}
		return true;
	}
}
if (!function_exists('check_confirm_order_info')) {
	function check_confirm_order_info ($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=1 || $order_status >=2 ){
			return false;
		}
		return true;
	}
}
if (!function_exists('sellerHandle')) {
	function sellerHandle($shop){
		if($shop->shop_status == 4){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺已经被管理员关闭，禁止所有操作');
		}else if($shop->shop_status == 0){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺审核中，禁止所有操作');
		}
		else if($shop->shop_status == 2){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺未通过审核，禁止所有操作');
		}
	}
}
if (!function_exists('buyerHandle')) {
	function buyerHandle($shop){
		if($shop->shop_status == 4){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺已经被管理员关闭，禁止所有操作');
		}else if($shop->shop_status == 0){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺审核中，禁止所有操作');
		}
		else if($shop->shop_status == 2){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺未通过审核，禁止所有操作');
		}else if($shop->shop_status == 3){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺暂停营业');
		}
	}
}
