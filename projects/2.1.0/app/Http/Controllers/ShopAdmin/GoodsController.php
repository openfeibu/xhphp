<?php

namespace App\Http\Controllers\ShopAdmin;

use Input;
use App\User;
use App\Shop;
use Validator;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\ShopAdmin\Controller;
use App\Services\UserService;
use App\Services\GoodsService;
use App\Services\GoodsCategoryService;
use App\Services\ShopService;
use App\Services\HelpService;
use App\Services\ImageService;
use App\Services\CartService;
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
								GoodsService $goodsService,
								CartService $cartService,
								ShopService $shopService,
								HelpService $helpService ,
								FileUploadService $fileUploadService,
								ImageService $imageService,
								GoodsCategoryService $goodsCategoryService)
	{
		parent::__construct($shopService);
		$this->userService = $userService;
		$this->goodsService = $goodsService ;
		$this->cartService = $cartService;
		$this->goodsCategoryService = $goodsCategoryService ;
		$this->helpService = $helpService;
		$this->imageService = $imageService;
		$this->fileUploadService = $fileUploadService;
	}
	public function store (Request $request)
    {

    	$rules = [
        	'cat_id'		=> 'required|integer',
	        'goods_name'   	=> 'required|string|between:2,10',
	        'goods_img'    	=> 'required|string',
	        'goods_thumb'   => 'required|string',
	        'goods_price' 	=> 'required|numeric|min:0.01',
	        'goods_desc' 	=> 'required|string|max:255',
	        'goods_number' 	=> 'required|integer|min:0',
			'is_on_sale'    => 'required|in:0,1',
	    ];
    	$this->helpService->validateParameter($rules);

    	$this->helpService->validateData(trim($request->goods_name),"商品名称");

		if($this->shop->shop_type == 2 && !isset($request->weight) && !$request->weight)
		{
			 throw new \App\Exceptions\Custom\OutputServerMessageException('重量不能为空');
		}
    	switch ($this->shop->shop_status)
    	{
    		case 0:
		        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺 '.$this->shop->shop_name.' 在审核中不能添加商品');
    			break;
    		case 2:
		        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺 '.$this->shop->shop_name.' 审核不通过不能添加商品，请重新申请开店');
    			break;
		}
		/*
    	$existShopGoods = $this->goodsService->existShopGoods($this->shop->shop_id,$request->goods_name);
    	if($existShopGoods){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('店铺已存在同名商品');
    	}
    	*/
    	$isExistsCat = $this->goodsCategoryService->isExistsCat(['shop_id' => $this->shop->shop_id,'cat_id' => $request->cat_id]);

		$goods = $this->goodsService->addGoods($this->user,$this->shop);

        $goods = $this->goodsService->getBusinessGoods(['goods_id' => $goods->goods_id]);

		return [
			'code' => 200,
			'detail' => '添加成功',
			'goods' => $goods
		];
    }
	public function goodses(Request $request)
	{
		$rules = [
			'page' => 'required|integer',
			'cat_id' => 'sometimes|required',
	    ];
	    $this->helpService->validateParameter($rules);

    	$shop_id = $this->shop->shop_id;
    	sellerHandle($this->shop);
	    $categories = $this->goodsCategoryService->getCategories($shop_id);
	    if(isset($request->cat_id) && $request->cat_id){
		    $shopGoodses = $this->goodsService->getBusinessGoodses(['goods.shop_id' => $shop_id ,'goods.cat_id' => $request->cat_id]);
		    $count = $this->goodsService->count(['goods.shop_id' => $shop_id ,'goods.cat_id' => $request->cat_id]);
	    }
	    else{
		    $shopGoodses = $this->goodsService->getBusinessGoodses(['goods.shop_id' => $shop_id ]);
		    $count = $this->goodsService->count(['goods.shop_id' => $shop_id ]);
	    }

        return [
			'code' => 200 ,
			'count' => $count,
			'categories' => $categories,
			'goods' => $shopGoodses,
        ];
	}
	public function update (Request $request)
    {
    	$rules = [
        	'cat_id'		=> 'sometimes|required',
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

		if($this->shop->shop_type == 2 && !isset($request->weight) && !$request->weight)
		{
			 throw new \App\Exceptions\Custom\OutputServerMessageException('重量不能为空');
		}

	    $goods =  $this->goodsService->isExistsGoods(['goods_id' => intval($request->goods_id),'shop_id' => $this->shop->shop_id]);

		if(isset($request->goods_name)){
			$this->helpService->validateData(trim($request->goods_name),"商品名称");
		}

		if(isset($request->cat_id))
		{
			$isExistsCat = $this->goodsCategoryService->isExistsCat(['shop_id' => $this->shop->shop_id,'cat_id' => $request->cat_id]);
		}

	    $update = [
			'goods_name' 	=> isset($request->goods_name) ? $request->goods_name : $goods->goods_name,
			'goods_img'    	=> isset($request->goods_img) ? $request->goods_img : $goods->goods_img,
			'goods_thumb'   => isset($request->goods_thumb) ? $request->goods_thumb : $goods->goods_thumb,
	        'goods_price' 	=> isset($request->goods_price) ? $request->goods_price : $goods->goods_price,
	        'goods_desc' 	=> isset($request->goods_desc) ? $request->goods_desc : $goods->goods_desc,
	        'goods_number' 	=> isset($request->goods_number) ? $request->goods_number : $goods->goods_number,
	        'is_on_sale'	=> isset($request->is_on_sale) ? $request->is_on_sale : $goods->is_on_sale,
	        'cat_id'		=> isset($request->cat_id) ? $request->cat_id : $goods->cat_id,
			'weight' 		=> isset($request->weight) ? $request->weight : $goods->weight,
	    ];

		$this->goodsService->update(['goods_id' => intval($request->goods_id),'shop_id' => $this->shop->shop_id],$update);

		$goods = $this->goodsService->getBusinessGoods(['goods_id' => $goods->goods_id]);

		return [
			'code' => 200,
			'goods' => $goods
		];

    }
    public function cats (Request $request)
    {
	    $cats = $this->goodsCategoryService->getCategories($this->shop->shop_id);
	    $count = $this->goodsCategoryService->getCatCount(['shop_id' => $this->shop->shop_id]);
	    return [
	    	'code' 	=> 200,
	    	'count'	=> $count,
	    	'cats' 	=> $cats
	    ];
    }
	public function addCat (Request $request)
	{
		$rules = [
        	'cat_name'   	=> 'required|string|between:2,10',
        	'parent_id'     => 'sometimes|required|integer|min:0',
        	'sort' 			=> 'sometimes|required|integer|min:0|max:50',
        ];
        $this->helpService->validateParameter($rules);
		$this->helpService->validateData(trim($request->cat_name),"分类名称");

		$this->goodsCategoryService->isExistsCat(['shop_id' => $this->shop->shop_id,'cat_name' => $request->cat_name]);

		$cat = $this->goodsCategoryService->addCat([
			'cat_name'	=> $request->cat_name,
			'shop_id'	=> $this->shop->shop_id,
			'parent_id' => 0,
		]);
		$cat->cat_id = $cat->id;
        return [
			'code' => 200,
			'cats'  => $cat,
       	];
	}
	public function updateCat (Request $request)
	{
		$rules = [
        	'cat_id'		=> 'required|integer',
        	'cat_name'   	=> 'required|string|between:2,10',
        ];
        $this->helpService->validateParameter($rules);
		$this->helpService->validateData(trim($request->cat_name),"分类名称");

		$isExistsCat =  $this->goodsCategoryService->isExistsCat(['shop_id' => $this->shop->shop_id,'cat_id' => $request->cat_id]);
		if($isExistsCat->cat_name != $request->cat_name){
			$this->goodsCategoryService->isExistsCat(['shop_id' => $this->shop->shop_id,'cat_name' => $request->cat_name]);
		}
		$this->goodsCategoryService->updateCat(['cat_id' => $request->cat_id],['cat_name' => $request->cat_name]);

		$cat = $this->goodsCategoryService->getCat(['cat_id' => $request->cat_id]);
       	return [
			'code' => 200,
			'cats'  => $cat,
       	];
	}
	/*
	public function getCats(Request $request)
    {
        $categories = $this->goodsCategoryService->getCategories($this->shop->shop_id);
        return [
			'code' => 200 ,
			'data' => $categories,
        ];
    }
	*/
	public function deleteCat (Request $request)
	{
		$rules = [
        	'cat_id'		=> 'required|integer|exists:goods_category,cat_id,shop_id,'.$this->shop->shop_id,
        ];
        $this->helpService->validateParameter($rules);
        $goods = $this->goodsService->getGoods(['cat_id' => $request->cat_id,'shop_id' => $this->shop->shop_id],['goods_id']);
        if(count($goods)){
	        throw new \App\Exceptions\Custom\OutputServerMessageException('该分类下存在商品');
        }

        $this->goodsCategoryService->delete(['cat_id' => $request->cat_id,'shop_id' => $this->shop->shop_id]);

        throw new \App\Exceptions\Custom\RequestSuccessException('删除成功');
	}
	public function uploadGoodsImage (Request $request)
    {
         //上传商品图片
        $images_url = $this->imageService->uploadAdminImages(Input::all(), 'goods',$this->shop->shop_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url['image_url'],
            'thumb_url' => $images_url['thumb_img_url'],
        ];
    }
    public function delete (Request $request)
    {
    	$rules = [
        	'goods_id'		=> 'required|integer|exists:goods,goods_id,shop_id,'.$this->shop->shop_id,
        ];
        $this->helpService->validateParameter($rules);
		$where = ['goods_id' => $request->goods_id,'shop_id' => $this->shop->shop_id];
		$this->goodsService->delete($where);
		$this->cartService->removeCarts($where);
		throw new \App\Exceptions\Custom\RequestSuccessException('删除成功');
    }
	public function getGoods (Request $request)
	{
		$rules = [
        	'goods_id'		=> 'required',
	    ];
	    $this->helpService->validateParameter($rules);
	    $goods = $this->goodsService->isExistsGoods(['goods_id' => intval($request->goods_id)],['goods_id','weight','cat_id','shop_id','goods_name','goods_price','goods_click_count','goods_sale_count','goods_number','goods_price','goods_desc','goods_img','goods_thumb','created_at','is_on_sale']);
        return [
            'code' => 200,
            'data' => $goods
        ];

	}
}
