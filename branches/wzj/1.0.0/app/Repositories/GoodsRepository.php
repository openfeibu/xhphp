<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
use Illuminate\Http\Request;

class GoodsRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function addGoods($user,$shop)
	{
		$goods = new Goods;
		$goods->setConnection('write');
		$goods->uid = $user->uid;
		$goods->shop_id = $shop->shop_id;		
		$goods->goods_name = trim($this->request->goods_name);                
		$goods->goods_img = $this->request->goods_img;
		$goods->goods_desc = trim($this->request->goods_desc);
		$goods->goods_number = $this->request->goods_number;
		$goods->goods_price = $this->request->goods_price;
		$goods->created_at = date('Y-m-d H:i:s');
		$goods->save();
	}
	public function existShopGoods ($shop_id)
	{
		$existShopGoods = Goods::where('shop_id', $shop_id)->where('goods_name',trim($this->request->goods_name))->first();		

		return $existShopGoods;
		
	}
	public function existGoods ($goods_id)
	{
		$existGoods = Goods::where('goods_id',$goods_id)->first();		
		return $existGoods;

	}
	public function getShopGoodses ()
	{
		$ShopGoodsesList = Goods::select(DB::raw('goods_id,shop_id,uid,goods_name,goods_price,goods_click_count,goods_sale_count,goods_number,goods_price,goods_desc,goods_img,created_at,is_on_sale'))
								->where('is_on_sale', 1)
								->where('shop_id',$this->request->shop_id)
								->orderBy('goods_sale_count', 'desc')
								->orderBy('goods_click_count', 'desc')
								->orderBy('goods_id', 'desc')
								->skip(20 * $this->request->page - 20)
                           		->take(20)
                           		->get();
        return $ShopGoodsesList;
	}
	public function getGoods ($goods_id)
	{
		$goods = Goods::where('goods_id',$goods_id)->first();
		return 	$goods;			
	}
	public function getGoodses ()
	{
		$goodses = Goods::select(DB::raw('goods_id,shop_id,uid,goods_name,goods_price,goods_click_count,goods_sale_count,goods_number,goods_price,goods_desc,goods_img,created_at,is_on_sale'))
						->where('is_on_sale', 1)
						->orderBy('top', 'desc')
						->orderBy('goods_sale_count', 'desc')
						->orderBy('goods_click_count', 'desc')
						->orderBy('goods_id', 'desc')
						->skip(20 * $this->request->page - 20)
                   		->take(20)
                   		->get();
        return $goodses;
	}
}