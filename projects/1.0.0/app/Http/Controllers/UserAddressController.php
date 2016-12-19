<?php

namespace App\Http\Controllers;

use DB;
use App\Cart;
use App\OrderGoods;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\HelpService;
use App\Services\UserAddressService;

class UserAddressController extends Controller
{
	protected $helpService;
	
	protected $user;
	
	public function __construct (UserService $userService,
								 HelpService $helpService,
								 UserAddressService $userAddressService)
	{
		parent::__construct();
		$this->middleware('auth');
		$this->userService = $userService;
		$this->helpService = $helpService;
		$this->userAddressService = $userAddressService;
	 	$this->user = $this->userService->getUser(); 
	}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    public function index()
    {
       	$rules = [
        	'token' 	=> 'required',
        ];
        $this->helpService->validateParameter($rules);
        $user_addresses = $this->userAddressService->getUserAddresses(['uid' => $this->user->uid]);

        return [
			'code' => 200,
			'user_addresses' => $user_addresses
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
        	'token' 	=> 'required',
        	'consignee' => "required|string",
        	'address' => "required|string",
        	'mobile' => "required|regex:/^1[34578][0-9]{9}/",
        	'is_default' => 'sometimes|required|in:0,1',
    	];
    	$this->helpService->validateParameter($rules);

    	$userAddress = [
    		'uid' => $this->user->uid,
			'consignee' => $request->consignee,
			'address' => $request->address,
			'mobile' => $request->mobile,
			'is_default' => isset($request->is_default) ? $request->is_default : 0,
    	];
    	$user_address = $this->userAddressService->store($userAddress);
    	return [
			'code' => 200,
			'user_address' => $user_address,
    	];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $rules = [
        	'token' 	=> 'required',
        	'address_id' => "required|integer",
        ];
        $this->helpService->validateParameter($rules);
        $user_address = $this->userAddressService->getUserAddress(['address_id' => $request->address_id,'uid' => $this->user->uid]);
        if(!$user_address){
			throw new \App\Exceptions\Custom\OutputServerMessageException('收货地址不存在');
		}
        return [
			'code' => 200,
			'user_address' => $user_address
        ];
    }
	public function getDefault(Request $request)
    {
        $rules = [
        	'token' 	=> 'required',
        ];
        $this->helpService->validateParameter($rules);
        $user_address = $this->userAddressService->getUserAddress(['uid' => $this->user->uid]);
        return [
			'code' => 200,
			'user_address' => $user_address
        ];
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $rules = [
        	'token' 	=> 'required',
        	'address_id' => "required|integer",
        	'consignee' => "required|string",
        	'address' => "required|string",
        	'mobile' => "required|regex:/^1[34578][0-9]{9}/",
        	'is_default' => 'required|in:0,1',
    	];
    	$this->helpService->validateParameter($rules);
		if($request->is_default == 1){
	    	$this->userAddressService->update(['uid' => $this->user->uid],['is_default' => 0]);
    	}
    	$userAddress = [
			'consignee' => $request->consignee,
			'address' => $request->address,
			'mobile' => $request->mobile,
			'is_default' => $request->is_default ,
    	];
    	$where = ['address_id' => $request->address_id,'uid' => $this->user->uid];
    	$this->userAddressService->update($where,$userAddress);
    	throw new \App\Exceptions\Custom\RequestSuccessException("更新成功");
    }
	
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $rules = [
        	'token' 		=> 'required',
        	'address_id' 		=> 'required|integer',
    	];
    	$this->helpService->validateParameter($rules);
    	$where = ['address_id' => $request->address_id,'uid' => $this->user->uid ];
    	$this->userAddressService->destroy($where);
    	throw new \App\Exceptions\Custom\RequestSuccessException('删除成功');
    }
}
