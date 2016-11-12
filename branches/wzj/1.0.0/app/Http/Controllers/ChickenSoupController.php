<?php

namespace App\Http\Controllers;

use App\ChickenSoup;
use App\Http\Controllers\Controller;
use App\Repositories\ChickenSoupRepository;
use Illuminate\Http\Request;
use App\Services\HelpService;

class ChickenSoupController extends Controller
{
    protected $chickenSoupRepository;

	function __construct(ChickenSoupRepository $chickenSoupRepository)
    {
	    parent::__construct();

        $this->chickenSoupRepository = $chickenSoupRepository;
    }

    public function chickenSoupList(Request $request,HelpService $help){
		
		//检验请求参数
        $rule = [
            'page' => 'required',
        ];
        $help->validateParameter($rule);
		
        $chickenSoupList = $this->chickenSoupRepository->chickenSoupList($request->page);
		return [
			'code' => 200,
			'data' => $chickenSoupList
		];
    }
	
	public function chickenSoupDetail(Request $request,HelpService $help){
		//检验请求参数
        $rule = [
            'csid' => 'required',
        ];
        $help->validateParameter($rule);
		
		$chickenSoupDetail = $this->chickenSoupRepository->chickenSoupDetail($request->csid);
		return [
			'code' => 200,
			'data' => $chickenSoupDetail
		];
	}
}
