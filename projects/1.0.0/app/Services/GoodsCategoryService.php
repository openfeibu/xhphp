<?php

namespace App\Services;

use Log;
use Illuminate\Http\Request;
use App\Repositories\GoodsCategoryRepository;

class GoodsCategoryService
{
	protected $request;

	protected $goodsCategoryRepository;
	
	function __construct(Request $request,
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
}