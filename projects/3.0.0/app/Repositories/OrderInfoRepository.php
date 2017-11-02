<?php

namespace App\Repositories;

use DB;
use Session;
use App\Cart;
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
	        config(['database.default' => 'write']);
			$order_info = OrderInfo::create($order_info);
			$goodses = 	Cart::select(DB::raw("$order_info->order_id as order_id ,goods_name,goods_price,goods_sn,goods_id,goods_number,goods_desc"))->where('shop_id',$order_info->shop_id)->where('uid',$order_info->uid)->get()->toArray();
			foreach ($goodses as $key => $goods) {
				$goodses[$key]['goods_price'] = handleGoodsPrice($goods['goods_price']);
			}
        	OrderGoods::insert($goodses);
        	//Cart::where('shop_id',$order_info->shop_id)->where('uid',$order_info->uid)->delete();
        	return $order_info;
        } catch (Exception $e) {
        	throw new \App\Exceptions\Custom\RequestFailedException('无法创建订单');
        }
	}
	public function updateOrderInfo($order_sn,$update)
	{
		return OrderInfo::where('order_sn',$order_sn)->update($update);
	}
	public function updateOrderInfoById($order_id,$update)
	{
		return OrderInfo::where('order_id',$order_id)->update($update);
	}
	public function updateOrderInfoByWhere($where,$update)
	{
		return OrderInfo::where($where)->update($update);
	}

	public function getOrderInfos($where,$type,$num = 20)
	{

		$order_infos = OrderInfo::select(DB::raw('shop.shop_id,shop.shop_name,shop.shop_img,order_info.*'))
							->leftJoin('shop', 'shop.shop_id', '=', 'order_info.shop_id')
							->where($where);

        if($type == 'waitpay'){
	        $order_infos = $order_infos->where('order_info.pay_status',0);
		}else{
			$order_infos = $order_infos->where('order_info.pay_status',1);
			switch ($type)
			{
				case 'all':
					break;
				case 'beship':
					$order_infos = $order_infos->where('order_info.shipping_status',0)->where('order_info.order_status',1);
					break;
				case 'shipping':
					$order_infos = $order_infos->where('order_info.shipping_status',1)->where('order_info.order_status',1);
					break;
				case 'succ':
					$order_infos = $order_infos->where('order_info.shipping_status',2)->where('order_info.order_status',2);
					break;
				default:
					$order_infos = $order_infos->whereIn('order_info.order_status',['3','4']);
					break;
			}
		}
		return $order_infos->orderBy('order_info.order_id', 'desc')
                          	->skip($num * $this->request->page - $num)
	                   	  	->take($num)
                          	->get();
	}
	public function getOrderInfo($order_id)
	{
		return OrderInfo::select(DB::raw('shop.shop_id,shop.shop_name,shop.shop_img,order_info.order_id,order_info.order_sn,order_info.pay_status,order_info.order_status,order_info.shipping_status,order_info.goods_amount,order_info.shipping_fee,order_info.total_fee,order_info.consignee,order_info.address,order_info.mobile,order_info.postscript,order_info.pay_time,order_info.user_coupon_id,order_info.shipping_adjust_fee,order_info.raise_fee,order_info.created_at'))
							->leftJoin('shop', 'shop.shop_id', '=', 'order_info.shop_id')
							->where('order_info.order_id',$order_id)
                          	->first();
	}
	public function getOrderGoodses($order_id,$columns)
	{
		return OrderGoods::where('order_id',$order_id)->get($columns);
	}
	public function getOrderInfoGoodses ($where,$columns)
	{
		return OrderGoods::where($where)->get($columns);
	}
	public function getShopOrderInfo ($order_id,$shop_id)
	{
		return OrderInfo::select(DB::raw('shop.shop_id,shop.shop_name,shop.shop_img,order_info.order_id,order_info.order_sn,order_info.pay_status,order_info.order_status,order_info.shipping_status,order_info.goods_amount,order_info.shipping_fee,order_info.total_fee,order_info.consignee,order_info.address,order_info.mobile,order_info.postscript,order_info.pay_time,order_info.created_at'))
							->leftJoin('shop', 'shop.shop_id', '=', 'order_info.shop_id')
							->where('order_info.order_id',$order_id)
							->where('order_info.shop_id',$shop_id)
                          	->first();
	}
	public function isExistsOrderInfo ($where ,$columns)
	{
		return OrderInfo::where($where)->first($columns);
	}
	public function destroy ($where)
	{
		return OrderInfo::where($where)->delete();
	}
	public function getAllOrderInfos($where)
	{
		return OrderInfo::select(DB::raw('OrderInfo.*'))
							->where($where)
                          	->get();
	}

}
