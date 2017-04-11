<?php

namespace App\Http\Controllers\ShopAdmin\Mobile;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\CommonController;
use Auth;

class BusinessController extends CommonController
{

	public function __construct ()
	{
		$this->middleware('business:business');
	}
    public function index()
    {
        return view('business.mobile.index');
    }
	public function order()
    {
        return view('business.mobile.order');
    }
	public function product()
    {
        return view('business.mobile.product');
    }
}
