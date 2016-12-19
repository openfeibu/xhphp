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
use App\Services\GoodsCategoryService;
use App\Services\ShopService;
use App\Services\HelpService;
use App\Services\FileUploadService;

class GoodsController extends Controller
{
   	protected $helpService;

	protected $shopService;

	protected $goodsService;

	protected $fileUploadService;

	protected $userService;

	protected $goodsCategoryService;
	
	public function __construct (UserService $userService,
								ShopService $shopService,
								GoodsService $goodsService,
								HelpService $helpService ,
								FileUploadService $fileUploadService,
								GoodsCategoryService $goodsCategoryService)
	{
		parent::__construct();
		$this->middleware('auth',['only' => ['store']]);
		$this->userService = $userService;
		$this->shopService = $shopService ;
		$this->goodsService = $goodsService ;
		$this->goodsCategoryService = $goodsCategoryService ;
		$this->helpService = $helpService; 
		$this->fileUploadService = $fileUploadService;
	}
	
	public function store (Request $request)
    {	    
    	$user = $this->userService->getUser();  
        $shop = Shop::where('uid', $user->uid)->first(); 	
    	if(!$shop->shop_id){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('请先添加店铺');
    	}	   
    	$rules = [
        	'token' 	  	=> 'required',
	        'goods_name'   	=> 'required|string|between:4,30',
	        'goods_img'    	=> 'required|string',
	        'goods_price' 	=> 'required|numeric|min:0.01',
	        'goods_desc' 	=> 'required|string|max:255',
	        'goods_number' 	=> 'required|string|digits:0',
	    ];	    
    	$this->helpService->validateParameter($rules);  

    	$this->helpService->validateData(trim($request->goods_name),"商品名称"); 	
    	
    	switch ($shop->shop_status)
    	{
    		case 0:
		        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺 '.$shop->shop_name.' 在审核中不能添加商品');
    			break;	
    		case 2:
		        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺 '.$shop->shop_name.' 审核不通过不能添加商品，请重新申请开店');
    			break;		
    		case 3:
		        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺 '.$shop->shop_name.' 已关闭不能添加商品');
    			break;
		}
    	$existShopGoods = $this->goodsService->existShopGoods($shop->shop_id);
    	if($existShopGoods){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺已存在该商品');
    	}
		$this->goodsService->addGoods($user,$shop);		
	
        throw new \App\Exceptions\Custom\RequestSuccessException('添加成功');
    }
    public function getShopGoodses (Request $request)
    {
    	$rules = [
			'shop_id' => 'required|integer',
			'cat_id' => 'sometimes|required|integer',
	    ];
	    $this->helpService->validateParameter($rules);	       
	    $shop = $this->shopService->getShop($request->shop_id);  
	    
	    if($shop->shop_status != 1){
		    throw new \App\Exceptions\Custom\OutputServerMessageException('店铺'.trans('common.shop_status'.$shop->shop_status));
    		break;	
	    }
	    $categories = $this->goodsCategoryService->getCategories($request->shop_id);
	    $firstCate = $this->goodsCategoryService->getFirst($request->shop_id);
	    $cat_id = isset($request->cat_id) ? $request->cat_id : isset($firstCate->cat_id) ? $firstCate->cat_id : 0 ;
	    if(isset($request->cat_id)){
		    $cat_id = $request->cat_id;
	    }else if(isset($firstCate->cat_id)){
		     $cat_id = $firstCate->cat_id;
	    }
	    else{
		    $cat_id = 0;
	    }
	    $user = $this->userService->getUser();  
	    $uid = $user ? $user->uid : 0;
	    $shopGoodses = $cat_id ? $this->goodsService->getShopGoodses(['goods.shop_id' =>$request->shop_id ,'goods.cat_id' => $cat_id],$uid) : [];	
        return [
			'code' => 200 ,
			'categories' => $categories,
			'shop' => $shop,
			'goodes' => $shopGoodses,
        ];
      	
    }
    public function getGoodses(Request $request)
    {
	    $rules = [
			'page' => 'required|string|digits:1',
	    ];	    
	    $this->helpService->validateParameter($rules);  
	    $goodses = $this->goodsService->getGoodses($request->page);
	    return [
			'code' => 200 ,
			'goodses' => $goodses
        ];
    }
}
