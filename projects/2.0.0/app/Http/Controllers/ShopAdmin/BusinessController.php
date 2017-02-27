<?php

namespace App\Http\Controllers\ShopAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\ShopAdmin\Controller;
use Auth;

class BusinessController extends Controller
{

    public function __construct()
    {
	    parent::__construct();
        $this->middleware('guest:business');
    }

    public function index()
    {
        $admin = Auth::guard('business')->user();
        return $admin->nickname;
    }
	
}