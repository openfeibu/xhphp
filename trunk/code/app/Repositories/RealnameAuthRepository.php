<?php

namespace App\Repositories;

use Log;
use App\RealnameAuth;
use Illuminate\Http\Request;

class RealnameAuthRepository
{

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 保存身份证照片凭证到数据库
	 */
	public function saveVoucher(array $param)
	{
		Log::error($param);
		$auth = new RealnameAuth;
		$auth->uid = $param['uid'];
		$auth->pic1 = isset($param['pic1']) ? $param['pic1'] : '';
		$auth->pic2 = isset($param['pic2']) ? $param['pic2'] : '';
		$auth->name = $param['name'];
		$auth->ID_Number = $param['id_number'];
		$auth->save();
	}
}