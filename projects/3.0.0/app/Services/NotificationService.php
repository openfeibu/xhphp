<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\NotificationRepository;

class NotificationService
{

	protected $request;

	protected $userRepository;

	protected $notificationRepository;

	function __construct(Request $request,
						 UserRepository $userRepository,
						 NotificationRepository $notificationRepository
						 )
	{
		$this->request = $request;
		$this->userRepository = $userRepository;
		$this->notificationRepository = $notificationRepository;
	}
	public function store ($data)
	{
		return $this->notificationRepository->store($data);
	}
	public function getNewNotifications($where)
	{
		$notifications = $this->notificationRepository->getNewNotifications($where);
		$datas = array();
		foreach( $notifications as $key => $notification )
		{
			switch ($notification->type)
			{
				case 'topic_comment':
					$comment = \App\TopicComment::where('tcid',$notification->new_id)->first(['content']);
					$user = \App\User::where('uid',$notification->new_uid)->first(['avatar_url','nickname','openid','uid']);
					$topic =  \App\Topic::where('tid',$notification->top_id)->first(['content']);
					$data = [
						'uid' => $user->uid,
						'avatar_url' => $user->avatar_url,
						'nickname' => $user->nickname,
						'openid' => $user->openid,
						'tid' => $notification->top_id,
						'tcid' => $notification->new_id,
						'content' => $comment ? escape_content($comment->content) : '评论已删除',
						'object_content' => $topic ? escape_content($topic->content) : '话题已删除',
					];
					
					break;
				case 'comment_comment':
					$comment = \App\TopicComment::where('tcid',$notification->new_id)->first(['content']);
					$user = \App\User::where('uid',$notification->new_uid)->first(['avatar_url','nickname','openid','uid']);
					$object_comment = \App\TopicComment::where('tcid',$notification->object_id)->first(['content']);
					$data = [
						'uid' => $user->uid,
						'avatar_url' => $user->avatar_url,
						'nickname' => $user->nickname,
						'openid' => $user->openid,
						'tid' => $notification->top_id,
						'tcid' => $notification->new_id,
						'content' => $comment ? escape_content($comment->content) : '评论已删除',
						'object_content' => $object_comment ? escape_content($object_comment->content) : '评论已删除',
					];
					break;
				default:
					break;
			}
			$data['id'] = $notification->id;
			$data['created_at'] = $notification->created_at->format('Y-m-d H:i:s');
			$datas[] = $data;
		}
		//$this->notificationRepository->changeRead($where);
		return $datas;
	}
	public function newTopicNotificationCount($where)
	{
		return $this->notificationRepository->newTopicNotificationCount($where);
	}
}
