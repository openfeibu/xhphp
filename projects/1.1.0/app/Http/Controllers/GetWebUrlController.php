<?php

namespace App\Http\Controllers;

use App\Paper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HelpService;

class GetWebUrlController extends Controller
{

    protected $help;

    function __construct(HelpService $help)
    {
        $this->help = $help;
    }

    public function index(Request $request)
    {
        $rule = [
            'url_name' => 'required',
        ];
        $this->help->validateParameter($rule);

        $paper = Paper::where('name', $request->url_name)->first();
        return [
            'code' => 200,
            'detail' => "请求成功",
            "url" => config('app.web_url').$paper->url
        ];
    }
}
