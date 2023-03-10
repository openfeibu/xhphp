<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Validator;
use App\Order;
use App\Advertisement;
use App\Http\Requests;
use App\Http\Controllers\Controller;


class HomeController extends Controller
{
	public function __construct ()
	{
		parent::__construct();

	}
	public function index ()
	{
		return view('welcome');
	}
    public function getADList(Request $request)
    {
        $ad = Advertisement::select(DB::raw('adid, ad_url, ad_image_url, title,description, rank, created_at'))
                           ->whereNull('deleted_at')
                           ->orderBy('rank', 'asc')
                           ->get();

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' =>  $ad
        ];
    }

    public function getExtra(Request $request)
    {
        $orderArr = Order::select(DB::raw('order.fee, order.status, order.updated_at, owner_user.nickname as owner_nickname,
                                        if(courier_user.nickname IS NULL,"",courier_user.nickname) as courier_nickname'))
                      ->join('user as owner_user', 'order.owner_id', '=', 'owner_user.uid')
                      ->leftJoin('user as courier_user', 'order.courier_id', '=', 'courier_user.uid')
                      ->where('order.admin_deleted', 0)
                      ->whereIn('order.status', ['new', 'accepted', 'completed'])
                      ->orderBy('order.created_at', 'desc')
                      ->orderBy('order.updated_at', 'desc')
                      ->take(20)
                      ->get()
                      ->toArray();
        $order = array();
        foreach( $orderArr as $key => $value )
        {
        	//$order[$key] = $value;
        	//$order[$key]['status'] = trans("common.task_status.$value[status]");
        	if($value['status'] == 'new'){
	        	$order[$key]['extra'] = $value['owner_nickname']."发布了一个新任务" ;
        	}else if($value['status'] == 'accepted'){
	        	$order[$key]['extra'] = $value['courier_nickname']."接了一个任务" ;
        	}else if($value['status'] == 'completed'){
	        	$order[$key]['extra'] = $value['courier_nickname']."完成了任务";
        	}

        }
        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $order,
        ];
    }
}
