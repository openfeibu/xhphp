<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Input;
use App\User;
use App\Shop;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\GoodsCategoryService;
use App\Services\ShopService;
use App\Services\HelpService;
use App\Services\ImageService;
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
								ImageService $imageService,
								GoodsCategoryService $goodsCategoryService)
	{
		parent::__construct();
		$this->middleware('auth',['only' => ['store','update','uploadGoodsImage','addCat']]);
		$this->userService = $userService;
		$this->shopService = $shopService ;
		$this->goodsService = $goodsService ;
		$this->goodsCategoryService = $goodsCategoryService ;
		$this->helpService = $helpService;
		$this->imageService = $imageService;
		$this->fileUploadService = $fileUploadService;
	}

	public function store (Request $request)
    {
    	$user = $this->userService->getUser();
        $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);
    	$rules = [
        	'token' 	  	=> 'required',
        	'cat_id'		=> 'required|integer',
	        'goods_name'   	=> 'required|string|between:2,10',
	        'goods_img'    	=> 'required|string',
	        'goods_thumb'   => 'required|string',
	        'goods_price' 	=> 'required|numeric|min:0.01',
	        'goods_desc' 	=> 'required|string|max:255',
	        'goods_number' 	=> 'required|integer|min:0',
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
    	$existShopGoods = $this->goodsService->existShopGoods($shop->shop_id,$request->goods_name);
    	if($existShopGoods){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺已存在同名商品');
    	}

    	$isExistsCat = $this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_id' => $request->cat_id]);

		$this->goodsService->addGoods($user,$shop);

        throw new \App\Exceptions\Custom\RequestSuccessException('添加成功');
    }
    public function update (Request $request)
    {
    	$user = $this->userService->getUser();
        $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);

    	$rules = [
        	'token' 	  	=> 'required',
        	'cat_id'		=> 'sometimes|required|integer',
        	'goods_id'		=> 'required|integer',
	        'goods_name'   	=> 'sometimes|required|string|between:2,10',
	        'goods_img'    	=> 'sometimes|required|string',
	        'goods_thumb'   => 'sometimes|required|string',
	        'goods_price' 	=> 'sometimes|required|numeric|min:0.01',
	        'goods_desc' 	=> 'sometimes|required|string|max:255',
	        'goods_number' 	=> 'sometimes|required|integer|min:0',
	        'is_on_sale'    => 'sometimes|required|integer|in:0,1',
	    ];
	    $this->helpService->validateParameter($rules);

	    $goods =  $this->goodsService->isExistsGoods(['goods_id' => intval($request->goods_id),'shop_id' => $shop->shop_id]);

		if(isset($request->goods_name)){
			$this->helpService->validateData(trim($request->goods_name),"商品名称");
		}

		$isExistsCat = $this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_id' => $request->cat_id]);

	    $update = [
			'goods_name' 	=> isset($request->goods_name) ? $request->goods_name : $goods->goods_name,
			'goods_img'    	=> isset($request->goods_img) ? $request->goods_img : $goods->goods_img,
			'goods_thumb'   => isset($request->goods_thumb) ? $request->goods_thumb : $goods->goods_thumb,
	        'goods_price' 	=> isset($request->goods_price) ? $request->goods_price : $goods->goods_price,
	        'goods_desc' 	=> isset($request->goods_desc) ? $request->goods_desc : $goods->goods_desc,
	        'goods_number' 	=> isset($request->goods_number) ? $request->goods_number : $goods->goods_number,
	        'is_on_sale'	=> isset($request->is_on_sale) ? $request->is_on_sale : $goods->is_on_sale,
	        'cat_id'		=> isset($request->cat_id) ? $request->cat_id : $goods->cat_id,
	    ];

		$this->goodsService->update(['goods_id' => intval($request->goods_id),'shop_id' => $shop->shop_id],$update);

	    throw new \App\Exceptions\Custom\RequestSuccessException('更新成功');
    }
    public function delete (Request $request)
    {
	    $user = $this->userService->getUser();
        $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);
    	$rules = [
        	'goods_id'		=> 'required|integer',
	    ];
	    $this->helpService->validateParameter($rules);
	    $goods =  $this->goodsService->isExistsGoods(['goods_id' => intval($request->goods_id),'shop_id' => $shop->shop_id]);
    }
	public function getGoods (Request $request)
	{
		$rules = [
        	'goods_id'		=> 'required',
	    ];
	    $this->helpService->validateParameter($rules);
	    $goods = $this->goodsService->isExistsGoods(['goods_id' => intval($request->goods_id)],['goods_id','cat_id','shop_id','goods_name','goods_price','goods_click_count','goods_sale_count','goods_number','goods_price','goods_desc','goods_img','goods_thumb','created_at','is_on_sale']);
        return [
            'code' => 200,
            'data' => $goods
        ];

	}
	public function addCat (Request $request)
	{
		$rules = [
        	'token' 	  	=> 'required',
        	'cat_name'   	=> 'required|string|between:2,10',
        	'parent_id'     => 'sometimes|required|integer|min:0',
        	'sort' 			=> 'sometimes|required|integer|min:0|max:50',
        ];
		$user = $this->userService->getUser();
        $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);
		$this->helpService->validateData(trim($request->cat_name),"分类名称");

		$this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_name' => $request->cat_name]);
		/*$isExistsCat =  isset($request->parent_id) ? $this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_id' => $request->parent_id]);*/
		$this->goodsCategoryService->addCat([
			'cat_name'	=> $request->cat_name,
			'shop_id'	=> $shop->shop_id,
			'parent_id' => 0,
		]);

        throw new \App\Exceptions\Custom\RequestSuccessException('添加成功');
	}
	public function updateCat (Request $request)
	{
		$rules = [
        	'token' 	  	=> 'required',
        	'cat_id'		=> 'required|integer',
        	'cat_name'   	=> 'required|string|between:2,10',
        ];
		$user = $this->userService->getUser();
        $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);
		$this->helpService->validateData(trim($request->cat_name),"分类名称");

		$isExistsCat =  $this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_id' => $request->cat_id]);
		if($isExistsCat->cat_name != $request->cat_name){
			$this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_name' => $request->cat_name]);
		}
		/*$isExistsCat =  isset($request->parent_id) ? $this->goodsCategoryService->isExistsCat(['shop_id' => $shop->shop_id,'cat_id' => $request->parent_id]);*/
		$this->goodsCategoryService->updateCat(['cat_id' => $request->cat_id],['cat_name' => $request->cat_name]);

        throw new \App\Exceptions\Custom\RequestSuccessException('更新成功');
	}
    public function getCats(Request $request)
    {
        $rules = [
			'shop_id' => 'required|exists:shop,shop_id',
	    ];
        $this->helpService->validateParameter($rules);
        $categories = $this->goodsCategoryService->getCategories($request->shop_id);
        return [
			'code' => 200 ,
			'data' => $categories,
        ];
    }
    public function uploadGoodsImage(Request $request)
    {
         //上传头像文件
        $user = $this->userService->getUser();

        $shop = $this->shopService->isExistsShop(['uid' => $user->uid]);

        $images_url = $this->imageService->uploadAdminImages(Input::all(), 'goods',$shop->shop_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url['image_url'],
            'thumb_url' => $images_url['thumb_img_url'],
        ];
    }
    public function getShopGoodses (Request $request)
    {
    	$rules = [
			'shop_id' => 'sometimes|required|integer',
			'cat_id' => 'sometimes|required|integer',
	    ];
	    $this->helpService->validateParameter($rules);
		$user = $this->userService->getUserByToken();
		if(!isset($request->shop_id)){
			if(!$user){
				throw new \App\Exceptions\Custom\OutputServerMessageException('参数错误');
			}
	    	$shop = $this->shopService->isExistsShop(['uid' => $user->uid]);
	    	$shop_id = $shop->shop_id;
	    	sellerHandle($shop);
		}else{
			$shop = $this->shopService->isExistsShop(['shop_id' => $request->shop_id]);
			$shop_id = $request->shop_id;
		}
	    $categories = $this->goodsCategoryService->getCategories($shop_id);
	    $firstCate = $this->goodsCategoryService->getFirst($shop_id);
	    $cat_id = isset($request->cat_id) ? $request->cat_id : isset($firstCate->cat_id) ? $firstCate->cat_id : 0 ;
	    if(isset($request->cat_id)){
		    $cat_id = $request->cat_id;
	    }else if(isset($firstCate->cat_id)){
		     $cat_id = $firstCate->cat_id;
	    }
	    else{
		    $cat_id = 0;
	    }
	    $uid = $user ? $user->uid : 0;
	    $shopGoodses = $cat_id ? $this->goodsService->getShopGoodses(['goods.shop_id' =>$shop_id ,'goods.cat_id' => $cat_id],$uid) : [];
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
