<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Version;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\HelpService;

class VersionController extends Controller
{
	protected $helpService;

	public function __construct (HelpService $helpService)
	{
		parent::__construct();
		$this->helpService = $helpService;
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $require)
    {
	    $rules = [
			'platform' => 'required|string',
	    ];
	    $this->helpService->validateParameter($rules);
        $version = Version::select(DB::raw('id,code,name,detail,download,new_download,compulsion'))->where('platform',$require->platform)->orderBy('id','DESC')->first();
        return [
			'code' => 200,
			'data' => $version ? $version : []
        ];
    }


}
