<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
use App\CollectShop;
use Illuminate\Http\Request;

class ShopRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function model()
    {
        return Shop::class;
    }
	public function addShop($user)
	{
		$shop = new Shop;
		$shop->setConnection('write');
		$shop->uid = $user->uid;
		$shop->shop_name = trim($this->request->shop_name);                
		$shop->shop_img = $this->request->shop_img;
		$shop->description = trim($this->request->description);
		$shop->created_at = date('Y-m-d H:i:s');
		$shop->save();		
	}
	
	public function getShops()
	{
		$shopList = Shop::select(DB::raw('shop.shop_id,shop.uid,college.cid,college.name as college_name,shop.address,shop.shop_name,shop.shop_img,shop.description,shop.shop_favorite_count,shop.shop_click_count,shop.created_at'))
						->leftJoin('college', 'college.cid', '=', 'shop.college_id')
						->where('shop_status', 1)
						->skip(20 * $this->request->page - 20)
						->orderBy('shop_favorite_count','desc')
						->orderBy('shop_click_count','desc')
						->orderBy('shop_id', 'desc')
                        ->take(20)
                        ->get();
        return $shopList;
	}
	public function existShop ()
	{
		$shop = Shop::where('shop_name',trim($this->request->shop_name))->first();		
		if($shop){
			return true;
		}else{
			return false;
		}
	}
	public function getShop ($where,$columns)
	{
		$shop = Shop::where($where)->first($columns);
		return 	$shop;			
	}
	public function collect ($shop_id,$uid)
	{
		CollectShop::create([
			'uid' => $uid,
			'shop_id' => $shop_id,
		]);
		Shop::where('shop_id',$shop_id)->increment('shop_favorite_count');
		return true;
	}
	public function unCollect ($shop_id,$uid)
	{
		CollectShop::where('shop_id',$shop_id)->where('uid',$uid)->delete();
		Shop::where('shop_id',$shop_id)->decrement('shop_favorite_count');
		return true;
	}
	public function isCollect ($shop_id,$uid)
	{
		return CollectShop::where('shop_id',$shop_id)->where('uid',$uid)->first(['id']);
	}
	public function userCollects ($uid)
	{
		return  CollectShop::select(DB::raw('shop.shop_id,shop.college_id,shop.uid,college.cid,college.name as college_name,shop.address,shop.shop_name,shop.shop_img,shop.description,shop.shop_favorite_count,shop.shop_click_count,shop.created_at'))
		 			->leftjoin('shop', function ($join) {
			            $join->on('shop.shop_id', '=', 'collect_shops.shop_id')->where('shop.shop_status','=',1);
			        })
			        ->leftJoin('college', 'college.cid', '=', 'shop.college_id')
			        ->where('collect_shops.uid',$uid)
			        ->skip(20 * $this->request->page - 20)
	                ->take(20)
                    ->get();
	}
}