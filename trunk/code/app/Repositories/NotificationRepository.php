<?php

namespace App\Repositories;

use DB;
use App\Notification;
use Illuminate\Http\Request;

class NotificationRepository
{

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}
	public function store ($data)
	{
		return Notification::create($data);
	}
	public function getNewNotifications($where)
	{
		return Notification::where($where)->orderBy('id','desc')->get();		
	}
	public function changeRead ($where)
	{
		return Notification::where($where)->update(['read' => 1]);	
	}
	public function newTopicNotificationCount($where)
	{
		return Notification::where($where)->count();		
	}
}