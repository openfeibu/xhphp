<?php

namespace App\Repositories;

use DB;
use Session;
use App\User;
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
	public function update ($where,$data)
	{
		config(['database.default' => 'write']);
		return Shop::where($where)->update($data);
	}
	public function getShops()
	{
		$shopList = Shop::select(DB::raw('shop.shop_id,shop.uid,college.cid,college.name as college_name,shop.address,shop.shop_name,shop.shop_img,shop.description,shop.shop_favorite_count,shop.shop_click_count,shop.shipping_fee,shop.created_at,shop.shop_status'))
						->leftJoin('college', 'college.cid', '=', 'shop.college_id')
						->whereIn('shop_status', [1,3])
						->skip(20 * $this->request->page - 20)
						->orderBy('shop_status','asc')
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
		if($shop){
			$user = User::where('uid',$shop->uid)->first(['mobile_no']);
			$shop->mobile_no = $user->mobile_no;
		}
		return 	$shop;			
	}
	public function collect ($shop_id,$uid)
	{
		config(['database.default' => 'write']);
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
		return  CollectShop::select(DB::raw('shop.shop_id,shop.college_id,shop.uid,shop.shipping_fee,college.cid,college.name as college_name,shop.address,shop.shop_name,shop.shop_img,shop.description,shop.shop_favorite_count,shop.shop_click_count,shop.created_at,shop.shop_status'))
		 			->rightjoin('shop', function ($join) {
			            $join->on('shop.shop_id', '=', 'collect_shops.shop_id')->whereIn('shop.shop_status',[1,3]);
			        })
			        ->leftJoin('college', 'college.cid', '=', 'shop.college_id')
			        ->where('collect_shops.uid',$uid)
			        ->skip(20 * $this->request->page - 20)
	                ->take(20)
                    ->get();
	}
	public function inSale ($where = [],$number)
	{
		return Shop::where($where)->increment('sale_count',$number);
	}
	public function inIncome ($where = [],$number)
	{
		return Shop::where($where)->increment('income',$number);
	}
	
}