<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Input;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\ImageService;
use App\Services\LostAndFindService;
use App\Http\Controllers\Controller;

class LostAndFindController extends Controller
{
    protected $userService;

	protected $imageService;

	protected $helpService;
    
    protected $lostAndFindService;

	public function __construct (UserService $userService,
                                ImageService $imageService,
								HelpService $helpService,
                                LostAndFindService $lostAndFindService)
	{
		parent::__construct();
		$this->middleware('auth', ['only' => ['create','uploadImage']]);
		$this->helpService = $helpService;
        $this->userService = $userService;
        $this->imageService = $imageService;
        $this->lostAndFindService = $lostAndFindService;
	}
    
    public function getList(Request $request)
    {
        $rules = [
			'page'  => 'required',
			'type'  => 'required|in:lose,found'
	    ];
	    $this->helpService->validateParameter($rules);
        $where = ['loss.type' => $request->type];
        $loss = $this->lostAndFindService->getList($where);
        
        return [
            'code'      => 200,
            'detail'    => '提交成功',
            'data'      => $loss
        ];
    }
    public function create(Request $request)
    {
        //检验请求参数
        $rule = [
            'type'      => 'required|in:lose,found',
            'cat_id'    => 'required|exists:loss_category,cat_id',
            'mobile'    => 'required|regex:/^1[34578][0-9]{9}$/',
            'content'   => 'required|string|between:1,120',
            'img'       => 'sometimes',
            'thumb'     => 'sometimes',
            
        ];
        $this->helpService->validateParameter($rule);
        
        $loss = $this->lostAndFindService->create();
        
        return [
            'code'      => 200,
            'detail'    => '提交成功',
            'data'      => $loss
        ];
    }
    public function uploadImage(Request $request)
    {
        $images_url = $this->imageService->uploadThumbImages(Input::all(), 'loss');      
        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url['image_url'],
            'thumb_url' => $images_url['thumb_img_url'],
        ];
    }
    public function getCats()
    {
        $cats = $this->lostAndFindService->getCats();
        return [
            'code' => 200,
            'data' => $cats
        ];
    }
}
