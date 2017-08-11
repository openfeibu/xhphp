<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\GoodsRepository;
use App\Repositories\GoodsCategoryRepository;

class GoodsCategoryService
{
	protected $request;

	protected $goodsCategoryRepository;

	function __construct(Request $request,
						 GoodsRepository $goodsRepository,
						 UserRepository $userRepository,
						 GoodsCategoryRepository $goodsCategoryRepository)
	{
		$this->request = $request;
		$this->goodsCategoryRepository = $goodsCategoryRepository;
	}
	public function getCategories($shop_id)
	{
		return $this->goodsCategoryRepository->getCategories($shop_id);
	}
	public function getFirst($shop_id)
	{
		return $this->goodsCategoryRepository->getFirst($shop_id);
	}
	public function getCat ($where)
	{
		return $this->goodsCategoryRepository->isExistsCat($where,['*']);
	}
	public function getCats ($where)
	{
		return $this->goodsCategoryRepository->getCats($where);
	}
	public function isExistsCat ($where,$columns = ['*'])
	{
		$cat = $this->goodsCategoryRepository->isExistsCat($where,$columns);
		if(isset($where['cat_name']) && $cat){
			throw new \App\Exceptions\Custom\OutputServerMessageException('已存在该分类');
		}
		if(!isset($where['cat_name']) && !$cat){
			throw new \App\Exceptions\Custom\OutputServerMessageException('分类不存在');
		}
		return $cat;
	}
	public function addCat ($data)
	{
		return $this->goodsCategoryRepository->addCat($data);
	}
	public function updateCat ($where = [],$update = [])
	{
		return $this->goodsCategoryRepository->updateCat($where,$update);
	}
	public function delete ($where)
	{
		return $this->goodsCategoryRepository->delete($where);
	}
	public function getCatCount ($where)
	{
		return $this->goodsCategoryRepository->getCatCount($where);
	}
}
