<?php

namespace App\Http\Controllers\ShopAdmin;

use Illuminate\Http\Request;
use Input;
use App\Http\Requests;
use App\Services\ShopService;
use App\Services\UserService;
use App\Services\ImageService;
use App\Services\HelpService;
use App\Http\Controllers\ShopAdmin\Controller;

class UserController extends Controller
{

    public function __construct(UserService $userService,ShopService $shopService,ImageService $imageService,HelpService $helpService)
    {
	    parent::__construct($shopService);
	    $this->userService = $userService;
	    $this->imageService = $imageService;
	    $this->helpService = $helpService;
    }
	public function getUser ()
	{
		return [
			'code' => 200,
			'user' => $this->user,
			'shop' => $this->shop
		];
	}
	public function getShop ()
	{
		return [
			'code' => 200,
			'shop' => $this->shop
		];
	}
    public function updateShop (Request $request)
	{
		$rules = [
	        'shop_img'    => 'sometimes|required|string',
	        'description' => 'sometimes|required|string|max:255',
	       /* 'address' 	  => 'sometimes|required|string',*/
	        'min_goods_amount' 	  =>   'sometimes|numeric|min:0',
	        'shipping_fee' 	  => 'sometimes|numeric|min:0',
	        'shop_status' 	  => 'sometimes|numeric|in:1,3',
	    ];
	    $this->helpService->validateParameter($rules);
		sellerHandle($this->shop);
		$where = ['shop_id' => $this->shop->shop_id];
		$update = [
			'shop_img' => isset($request->shop_img) ? $request->shop_img : $this->shop->shop_img,
			'description' => isset($request->description) ? $request->description : $this->shop->description,
		/*	'address' => isset($request->address) ? $request->address : $this->shop->address,*/
			'min_goods_amount' => isset($request->min_goods_amount) ? $request->min_goods_amount : $this->shop->min_goods_amount,
			'shipping_fee' => isset($request->shipping_fee) ? $request->shipping_fee : $this->shop->shipping_fee,
			'shop_status' => isset($request->shop_status) ? $request->shop_status : $this->shop->shop_status,
		];
		if(isset($request->open_time) && isset($request->close_time)){
			if(strtotime($request->open_time) > strtotime($request->close_time) ){
				throw new \App\Exceptions\Custom\OutputServerMessageException('开店时间不能大于关店时间');
			}
			$update['open_time'] =  $request->open_time;
			$update['close_time'] = $request->close_time;
		}
	    $this->shopService->update($where,$update);
	    $shop = $this->shopService->getShop($where);
	    return [
			'code' => 200,
			'shop' => $shop,
	    ];
	}
	public function uploadShopImage (Request $request)
    {
         //上传商品图片
        $images_url = $this->imageService->uploadImages(Input::all(), 'shop');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url['image_url'],
            'thumb_url' => $images_url['thumb_img_url'],
        ];
    }
}
