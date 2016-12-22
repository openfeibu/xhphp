<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
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
	
	public function __construct (HelpService $helpService ,
								 FileUploadService $fileUploadService)
	{
		parent::__construct();
		$this->middleware('auth',['only' => ['store']]);
		$this->helpService = $helpService; 
		$this->fileUploadService = $fileUploadService;
	}
	
	public function upload (Request $request)
    {	  
	}
}