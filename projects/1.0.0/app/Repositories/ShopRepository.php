<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\Goods;
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
	public function getShop ($shop_id,$columns)
	{
		$shop = Shop::where('shop_status', 1)
					->where('shop_id',$shop_id)									
					->first($columns);
		return 	$shop;			
	}

}