<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use App\Shop;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Services\HelpService;
use App\Services\CartService;
use App\Services\FileUploadService;

class ShopController extends Controller
{
	protected $helpService;

	protected $shopService;

	protected $goodsService;

	protected $fileUploadService;

	protected $userService;

	protected $cartService;

	public function __construct (UserService $userService,
								ShopService $shopService,
								GoodsService $goodsService,
								HelpService $helpService ,
								CartService $cartService,
								FileUploadService $fileUploadService)
	{
		parent::__construct();
		$this->middleware('auth', ['only' => ['store','myShop','update']]);
		$this->userService = $userService;
		$this->shopService = $shopService ;
		$this->goodsService = $goodsService ;
		$this->helpService = $helpService;
		$this->cartService = $cartService;
		$this->fileUploadService = $fileUploadService;
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    	$user = $this->userService->getUser();
        $shop = Shop::where('uid', $user->uid)->first();
        if($shop->shop_id){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('已拥有店铺 -- '.$shop->shop_name);
        }
        $rules = [
        	'token' 	  => 'required',
	        'shop_name'   => 'required|unique:shop|between:4,30',
	        'shop_img'    => 'required|string',
	        'description' => 'required|string|max:255',
	    ];
	    $this->helpService->validateParameter($rules);

	    $this->helpService->validateData($request->shop_name,"商店名称");

	    $exitShop = $this->shopService->existShop();

	    if($exitShop){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('已存在同名店铺');
	    }

        $this->shopService->addShop($user);

        throw new \App\Exceptions\Custom\RequestSuccessException('提交成功，等待审核');
    }
	public function update (Request $request)
	{
		$rules = [
        	'token' 	  => 'required',
	        'shop_img'    => 'sometimes|required|string',
	        'description' => 'sometimes|required|string|max:255',
	       /* 'address' 	  => 'sometimes|required|string',*/
	        'min_goods_amount' 	  =>   'sometimes|numeric|min:0',
	        'shipping_fee' 	  => 'sometimes|numeric|min:0',
	        'shop_status' 	  => 'sometimes|numeric|in:1,3',
	    ];
	    $this->helpService->validateParameter($rules);
	    $user = $this->userService->getUser();
	    $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);
		sellerHandle($shop);
		$where = ['shop_id' => $shop->shop_id];
		$update = [
		/*	'shop_img' => isset($request->shop_img) ? $request->shop_img : $shop->shop_img,*/
			'description' => isset($request->description) ? $request->description : $shop->description,
		/*	'address' => isset($request->address) ? $request->address : $shop->address,*/
			'min_goods_amount' => isset($request->min_goods_amount) ? $request->min_goods_amount : $shop->min_goods_amount,
			'shipping_fee' => isset($request->shipping_fee) ? $request->shipping_fee : $shop->shipping_fee,
			'shop_status' => isset($request->shop_status) ? $request->shop_status : $shop->shop_status,
		];

	    $this->shopService->update($where,$update);
	    throw new \App\Exceptions\Custom\RequestSuccessException('更新成功');
	}
    public function getShopList (Request $request)
    {
	    $rules = [
			'page' => 'required',
			'token' => 'sometimes|required|string',
	    ];
	    $this->helpService->validateParameter($rules);
	    if(isset($request->token)){
		    $user = $this->userService->getUserByToken($request->token);
		}
		$uid = isset($user->uid) ? $user->uid : 0;
		$shops = $this->shopService->getShops($uid);
		if(isset($user->uid)){
			$cart_count = $this->cartService->getCount(['uid' => $user->uid]);
		}
        return [
			'code' => 200 ,
			'cart_count' => isset($cart_count) ? $cart_count : 0,
			'shops' => $shops,

        ];
    }
    public function myShop ()
    {
    	$user = $this->userService->getUser();
	    $shop = $this->shopService->getShop(['uid' => $user->uid]);
	    return [
			'code' => 200,
			'shop' => $shop
	    ];
    }

    public function uploadShopImg (Request $request)
    {

    }
    public function collect (Request $request)
    {
    	$rules = [
			'token' => 'required|string',
			'shop_id' => 'required|exists:shop,shop_id',
	    ];
	    $this->helpService->validateParameter($rules);
	    $user = $this->userService->getUser();
	    $is_collect = $this->shopService->collect($request->shop_id,$user->uid);
	    return [
			'code' => 200,
			'is_collect' => $is_collect,
		];
    }
    public function userCollects (Request $request)
	{
		$rules = [
			'page' => 'required|integer|min:1',
		];
		$this->helpService->validateParameter($rules);
		$user = $this->userService->getUser();
		$shops = $this->shopService->userCollects($user->uid);
		 return [
			'code' => 200,
			'shops' => $shops,
		];
	}
}
