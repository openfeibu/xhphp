<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\NotificationService;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    protected $helpService;

    protected $notificationService;

    function __construct(HelpService $helpService,
    					 UserService $userService,
                         NotificationService $notificationService)
    {
	    parent::__construct();
        $this->middleware('auth');

        $this->helpService = $helpService;
        $this->userService = $userService;
        $this->notificationService = $notificationService;
    }
    public function getNewTopicNotifications()
    {
	    $uid = $this->userService->getUser()->uid;
		$where = ['uid' => $uid ,'attr' => 'topic','read'=>0];
	    $notifications = $this->notificationService->getNewNotifications($where);
	    return [
			'code' => 200,
			'data' => $notifications ,
	    ];
    }
    public function hasNewTopicNotification()
    {
	    $uid = $this->userService->getUser()->uid;
		$where = ['uid' => $uid ,'attr' => 'topic','read'=>0];
	    $count = $this->notificationService->newTopicNotificationCount($where);
	    return [
			'code' => 200,
			'data' => $count ,
	    ];
    }
}