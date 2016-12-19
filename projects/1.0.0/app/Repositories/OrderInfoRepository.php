<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
use App\OrderInfo;
use App\OrderGoods;
use Illuminate\Http\Request;

class OrderInfoRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function create(array $order_info)
	{
        try {
			return OrderInfo::create($order_info);
        } catch (Exception $e) {
        	throw new \App\Exceptions\Custom\RequestFailedException('无法创建订单');
        }
	}
	public function updateOrderInfo($order_sn,$update)
	{
		return OrderInfo::where('order_sn',$order_sn)->update($update);
	}
	public function getOrderInfos($page,$where,$type)
	{
		
		$order_infos = OrderInfo::select(DB::raw('shop.shop_id,shop.shop_name,shop.shop_img,order_info.created_at,order_info.order_id,order_info.pay_status,order_info.order_status,order_info.shipping_status,order_info.total_fee'))
							->leftJoin('shop', 'shop.shop_id', '=', 'order_info.shop_id')
							->where($where);
							
        if($type == 'waitpay'){
	        $order_infos = $order_infos->where('order_info.pay_status',0);
		}else{
			$order_infos = $order_infos->where('order_info.pay_status',1);
			switch ($type)
			{
				case 'beship':
					$order_infos = $order_infos->where('order_info.shipping_status',0);
					break;	
				case 'shipping':
					$order_infos = $order_infos->where('order_info.shipping_status',1);
					break;	
				case 'succ':
					$order_infos = $order_infos->where('order_info.shipping_status',2);
					break;		
				default:
					$order_infos = $order_infos->whereIn('order_info.order_status',['3','4']);
					break;
			}
		}
		return $order_infos->orderBy('order_info.order_id', 'desc')
                          	->skip(20 * $page - 20)
	                   	  	->take(20)
                          	->get();
	}
	public function getOrderInfo($order_id)
	{
		return OrderInfo::select(DB::raw('shop.shop_id,shop.shop_name,shop.shop_img,order_info.order_id,order_info.order_sn,order_info.pay_status,order_info.order_status,order_info.shipping_status,order_info.goods_amount,order_info.shipping_fee,order_info.total_fee,order_info.consignee,order_info.address,order_info.mobile,order_info.postscript,order_info.pay_time,order_info.created_at'))
							->leftJoin('shop', 'shop.shop_id', '=', 'order_info.shop_id')
							->where('order_info.order_id',$order_id)
                          	->first();
	}
	public function getOrderGoodses($order_id)
	{
		return OrderGoods::where('order_id',$order_id)->get();
	}
}