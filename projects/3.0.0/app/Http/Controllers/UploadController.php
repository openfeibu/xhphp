<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Input;
use Validator;
use App\Services\HelpService;
use App\Services\ImageService;

class UploadController extends Controller
{
   	protected $helpService;

	protected $shopService;

	protected $goodsService;

	protected $fileUploadService;

	protected $userService;

	protected $goodsCategoryService;

	public function __construct (HelpService $helpService ,
								 ImageService $imageService)
	{
		parent::__construct();
		$this->middleware('auth',['only' => ['store']]);
		$this->helpService = $helpService;
		$this->imageService = $imageService;
	}

	public function uploadFile (Request $request)
    {
        $file_url = $this->imageService->uploadFile(Input::all(), 'feedback',0);

		return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $file_url,
        ];
	}
}
