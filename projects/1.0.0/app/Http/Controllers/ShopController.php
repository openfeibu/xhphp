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
		$this->middleware('auth', ['only' => ['store']]);
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

    public function getShopList (Request $request)
    {
	    $rules = [
			'page' => 'required|string|digits:1',
			'token' => 'sometimes|required|string',
	    ];	    
	    $this->helpService->validateParameter($rules);    		
		$user = $this->userService->getUser(); 
		$uid = isset($user->uid) ? $user->uid : 0;
		$shops = $this->shopService->getShops($uid);
		if($user){
			$cart_count = $this->cartService->getCount(['uid' => $user->uid]);
		}
        return [
			'code' => 200 ,
			'cart_count' => isset($cart_count) ? $cart_count : 0,
			'shops' => $shops,
			
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
	    $is_collect =  $this->shopService->collect($request->shop_id,$user->uid);
	    return [
			'code' => 200,
			'is_collect' => $is_collect,
		];
    }
}
