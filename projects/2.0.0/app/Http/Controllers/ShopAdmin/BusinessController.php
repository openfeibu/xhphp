<?php

namespace App\Http\Controllers\ShopAdmin;

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
        return view('business.index');
    }
}
