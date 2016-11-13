<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\HelpService;
use App\Services\MessageService;
use App\Http\Controllers\Controller;

class MessageController extends Controller
{
    protected $helpService;

    protected $messageService;

    function __construct(HelpService $helpService,
                         MessageService $messageService)
    {
	    parent::__construct();
        $this->middleware('auth');

        $this->helpService = $helpService;
        $this->messageService = $messageService;
    }
    public function getMessageList(Request $request)
    {
        //检验请求参数
        $rule = [
            'token' => 'required',
            'page' => 'required|integer',
            'num' => 'sometimes|required|integer',
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'page' => $request->page,
            'num' => $request->num,
        ];
        //获取纸条列表
        $message = $this->messageService->getMessageList($param);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $message,
        ];
    }
}
