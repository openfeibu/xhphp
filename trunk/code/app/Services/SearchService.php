<?php

namespace App\Services;

use Illuminate\Http\Request;

class SearchService
{

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}


}