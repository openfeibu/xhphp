<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
use App\GoodsCategory;
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
		$goods->shop_id = $shop->shop_id;
		$goods->cat_id = $this->request->cat_id;
		$goods->goods_name = trim($this->request->goods_name);
		$goods->goods_img = $this->request->goods_img;
		$goods->goods_thumb = $this->request->goods_thumb;
		$goods->goods_desc = trim($this->request->goods_desc);
		$goods->goods_number = $this->request->goods_number;
		$goods->goods_price = $this->request->goods_price;
		$goods->is_on_sale = $this->request->is_on_sale;
		$goods->weight = $this->request->weight;
		$goods->created_at = date('Y-m-d H:i:s');
		$goods->save();
		return $goods;
	}
	public function update ($where,$update)
	{
		return Goods::where($where)->update($update);
	}
	public function existShopGoods ($shop_id,$goods_name)
	{
		$existShopGoods = Goods::where('shop_id', $shop_id)->where('goods_name',$goods_name)->first();

		return $existShopGoods;

	}
	public function existGoods ($goods_id)
	{
		$existGoods = Goods::where('goods_id',$goods_id)->first();
		return $existGoods;

	}
	public function getShopGoodses ($where)
	{
		$ShopGoodsesList = Goods::select(DB::raw('goods_id,shop_id,goods.weight,goods_name,goods_price,goods_click_count,goods_sale_count,goods_number,goods_price,goods_desc,goods_img,goods_thumb,created_at,is_on_sale,CASE goods_number WHEN 0 THEN 0 ELSE 1 END AS is_goods_number'))
								->where('is_on_sale', 1)
								->where($where)
								->orderBy('is_goods_number','desc')
								->orderBy('sort','asc')
								->orderBy('goods_click_count', 'desc')
								->orderBy('goods_id', 'desc')
                           		->get();
        return $ShopGoodsesList;
	}
	public function getBusinessGoodses ($where)
	{
		$ShopGoodsesList = Goods::select(DB::raw('goods.goods_id,goods.shop_id,goods.weight,goods.goods_name,goods.goods_price,goods.goods_click_count,goods.goods_sale_count,goods.goods_number,goods.goods_price,goods.goods_desc,goods.goods_img,goods.goods_thumb,goods.created_at,goods.is_on_sale,goods_category.cat_name,goods_category.cat_id'))
								->leftJoin('goods_category','goods.cat_id','=','goods_category.cat_id')
								->where($where)
								->orderBy('goods_click_count', 'desc')
								->orderBy('goods_id', 'desc')
								->skip(20 * $this->request->page - 20)
                    			->take(20)
                           		->get();

        return $ShopGoodsesList;
	}
	public function count ($where)
	{
		return Goods::where($where)->count();
	}
	public function getBusinessGoods ($where)
	{
		$goods = Goods::select(DB::raw('goods.goods_id,goods.shop_id,goods.weight,goods.goods_name,goods.goods_price,goods.goods_click_count,goods.goods_sale_count,goods.goods_number,goods.goods_price,goods.goods_desc,goods.goods_img,goods.goods_thumb,goods.created_at,goods.is_on_sale,goods_category.cat_name,goods_category.cat_id'))
								->leftJoin('goods_category','goods.cat_id','=','goods_category.cat_id')
								->where($where)
                           		->first();

        return $goods;
	}
	public function getGoods ($where,$columns)
	{
		$goods = Goods::where($where)->first($columns);
		return 	$goods;
	}
	public function getGoodses ()
	{
		$goodses = Goods::select(DB::raw('goods_id,shop_id,goods.weight,goods_name,goods_price,goods_click_count,goods_sale_count,goods_number,goods_price,goods_desc,goods_img,goods_thumb,created_at,is_on_sale'))
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
	public function deGoodsNumber ($where = [],$number = 1)
	{
		return Goods::where($where)->decrement('goods_number',$number);
	}
	public function InGoodsNumber ($where = [],$number = 1)
	{
		return Goods::where($where)->increment('goods_number',$number);
	}
	public function inGoodsSale ($where = [],$number = 1)
	{
		return Goods::where($where)->increment('goods_sale_count',$number);
	}
	public function getCount ($where)
	{
		return Goods::where($where)->count();
	}
	public function delete ($where)
	{
		return Goods::where($where)->delete();
	}
	public function getTopGoodses($number)
	{
		return Goods::join('shop','shop.shop_id','=','goods.shop_id')
					->where('shop.shop_status',1)
					->where('goods.top',1)
					->take($number)
					->get(['goods.goods_thumb','goods.goods_name','goods.goods_price']);
	}
}
