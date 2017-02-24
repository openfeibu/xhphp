<?php

namespace App\Repositories;

use DB;
use Session;
use App\Shop;
use App\GoodsCategory;
use Illuminate\Http\Request;

class GoodsCategoryRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function getCategories($shop_id)
	{
		return GoodsCategory::where('shop_id',$shop_id)
								->orderBy('sort', 'asc')
								->orderBy('cat_id', 'asc')
                           		->get();
	}
	public function getFirst($shop_id)
	{
		return GoodsCategory::where('shop_id',$shop_id)
								->orderBy('sort', 'asc')
								->orderBy('cat_id', 'asc')
                           		->first();
	}
	public function isExistsCat ($where,$columns)
	{
		$cat = GoodsCategory::where($where)->first($columns);
		return 	$cat;		
	}
	public function addCat ($data)
	{
		config(['database.default' => 'write']);
		return GoodsCategory::create($data);
	}
	public function updateCat ($where,$update)
	{
		config(['database.default' => 'write']);
		return GoodsCategory::where($where)->update($update);
	}
	public function delete ($where)
	{
		return GoodsCategory::where($where)->delete();
	}
}
