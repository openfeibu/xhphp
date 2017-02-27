<?php

namespace App\Http\Controllers\ShopAdmin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\ShopAdmin\Controller;
use Auth;

class BusinessController extends Controller
{

    
    public function index()
    {
        return view('business.index');
    }
	
}