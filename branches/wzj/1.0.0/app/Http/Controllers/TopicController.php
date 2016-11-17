<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use Input;
use Session;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\UserService;
use App\Services\TopicService;
use App\Services\ImageService;
use App\Services\PushService;
use App\Services\MessageService;
use App\Services\NotificationService;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;

class TopicController extends Controller
{
    protected $helpService;

    protected $userService;

    protected $topicService;

    protected $imageService;

	protected $pushService;
	
	protected $messageService;

	protected $notificationService;
	
    function __construct(HelpService $helpService,
                         UserService $userService,
                         TopicService $topicService,
                         NotificationService $notificationService,
                         MessageService $messageService,
                         ImageService $imageService,
                         PushService $pushService)
    {
	    parent::__construct();
        $this->middleware('auth', ['except' => ['getTopic', 'getTopicList', 'getTopicCommentsList', 'search']]);
        $this->helpService = $helpService;
        $this->userService = $userService;
        $this->topicService = $topicService;
        $this->imageService = $imageService;
        $this->pushService = $pushService;
        $this->messageService = $messageService;
        $this->notificationService = $notificationService;
    }
    /**
     * 获取单个话题
     */
    public function getTopic(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'topic_id' => $request->topic_id,
        ];
        //获取话题信息
        $topic = $this->topicService->getTopicByTopicID($param);

        return[
            'code' => 200,
            'detail' => '请求成功',
            'data' => $topic
        ];
    }

    /**
     * 获取我的话题
     */
    public function getMyTopic(Request $request)
    {
        //检验请求参数
        $rule = [
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'page' => $request->page,
        ];
        //获取我的话题
        $topics = $this->topicService->getCurrentUserTopic($param);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $topics
        ];
    }

    /**
     * 获取我的评论
     */
    public function getMyComment(Request $request)
    {
        //检验请求参数
        $rule = [
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'page' => $request->page,
        ];
        //获取我的话题
        $comments = $this->topicService->getCurrentUserComment($param);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $comments
        ];
    }

    /**
     * 获取话题列表
     */
    public function getTopicList(Request $request)
    {
        //检验请求参数
        $rule = [
            'page' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获得话题列表
        $topics = $this->topicService->getTopicList(['page' => $request->page]);

        //增加话题浏览量
        //$this->topicService->incrementViewCount($topics->lists('tid')->toArray());

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $topics
        ];
    }

    /**
     * 发布话题
     */
    public function createTopic(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_type' => 'required|exists:topic_type,type',
            'topic_content' => 'required|string|between:1,140',
            'img' => 'sometimes|required',
            'thumb' => 'sometimes|required',
        ];
        $this->helpService->validateParameter($rule);

        //频率限制

        // if ($request->img) {
        //     //检验图片链接是否存在数据库
        //     $imgs = explode(',', $request->img);
        //     foreach ($imgs as $key => $image) {
        //         if (!in_array($image, Session::get('uploadImgUrl', ['nothing']))) {
        //             throw new \App\Exceptions\Custom\OutputServerMessageException('请重新上传图片');
        //         }
        //     }
        // }

        $param = [
            'type' => $request->topic_type,
            'content' => $request->topic_content,
            'img' => isset($request->img) ? $request->img : '',
            'thumb' => isset($request->thumb) ? $request->thumb : '',
        ];
        //创建话题
        $this->topicService->createTopic($param);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * 上传话题图片
     */
    public function uploadImage(Request $request)
    {
        //频率限制


        //上传资讯图片文件
        $images_url = $this->imageService->uploadTopicImages(Input::all(), 'topic');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url['image_url'],
            'thumb_url' => $images_url['thumb_img_url'],
        ];
    }

    /**
     * 删除我的话题
     */
    public function deleteTopic(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_id' => 'required|exists:topic,tid,deleted_at,NULL',
        ];
        $this->helpService->validateParameter($rule);

        $topic = $this->topicService->getTopicByTopicID(['topic_id' => $request->topic_id]);
        if ($topic->deleted_at) {
            throw new \App\Exceptions\Custom\OutputServerMessageException('该话题已删除');
        }
        if ($topic->admin_deleted) {
            throw new \App\Exceptions\Custom\OutputServerMessageException('该话题已被管理员删除');
        }

        $param = [
            'topic_id' => $request->topic_id,
            'deleted_at' => date('Y-m-d H:i:s'),
        ];
        //删除话题
        $this->topicService->updateTopicStatus($param);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * 撤销删除我的话题
     */
    public function undoDeleteTopic(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_id' => 'required|exists:topic,tid',
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'topic_id' => $request->topic_id,
            'deleted_at' => NULL,
        ];
        //撤销删除话题
        $this->topicService->updateTopicStatus($param);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    /**
     * 获取话题评论列表
     */
    public function getTopicCommentsList(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_id' => 'required|exists:topic,tid,deleted_at,NULL',
            'page' => 'required|digits_between:1,100',
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'topic_id' => $request->topic_id,
            'page' => $request->page,
        ];
        //获取话题评论
        $comments = $this->topicService->getTopicCommentsList($param);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $comments,
        ];
    }

    public function comment(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_id' => 'required|exists:topic,tid,deleted_at,NULL',
            'topic_comment' => 'required|between:1,140',
            'comment_id' => 'sometimes|required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //检验评论是否存在
        $request->comment_id == 0 or $old_comment = $this->topicService->isCommentIdExist($request->comment_id);

		if(!$request->comment_id){
			$topic = $this->topicService->getTopic($request->topic_id,['uid']);
			$to_uid = $topic->uid;
			$type = "topic_comment";
		}else{
			$to_uid = $old_comment->uid;
			$type = "comment_comment";
		}
		
        $param = [
            'topic_id' => $request->topic_id,
            'comment' => $request->topic_comment,
            'comment_id' => $request->comment_id ?: 0,
        ];
        //创建评论
        $id = $this->topicService->createComment2Topic($param);
        $comments = $this->topicService->getTopicAllCommentsList($param);
		//透传给话题者/被评论者
		$user = $this->userService->getUser();
		if($to_uid != $user->uid)
		{
			$notificatin_data = [
				'uid' => $to_uid,
				'top_id' => $request->topic_id,
				'top_uid' => $this->userService->getUser()->uid,
				'object_id' => $request->comment_id ? $request->comment_id : 0 ,
				'object_uid' => $request->comment_id ? $to_uid : 0,
				'new_id' => $id,
				'new_uid' => $user->uid,
				'type' => $type,
				'attr' => 'topic',
			];		
			$this->notificationService->store($notificatin_data);
			$content = [
				'refresh' => 1,
				'target' => 'topic',
				'data' => '你有新消息' 
			];
			$this->pushService->PushUserTokenDevice('校汇', json_encode($content), $to_uid,2);
		}
        return [
            'code' => 200,
            'detail' => '请求成功',
            'comment_id' => $id,
            'data' => $comments,
        ];
    }

    public function deleteComment(Request $request)
    {
        $uid = $this->userService->getUser()->uid;
        //检验请求参数
        $rule = [
            'comment_id' => 'required|exists:topic_comment,tcid,uid,' . $uid,
        ];
        $this->helpService->validateParameter($rule);

        $param = [
            'comment_id' => $request->comment_id,
        ];
        $this->topicService->deleteComment($param);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function thumbUp(Request $request)
    {
        //检验请求参数
        $rule = [
            'topic_id' => 'required|exists:topic,tid,deleted_at,NULL',
            'comment_id' => 'sometimes|required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //检验评论是否存在
        $request->comment_id == 0 or $this->topicService->isCommentIdExist($request->comment_id);

        $param = [
            'topic_id' => $request->topic_id,
            'topic_comment_id' => isset($request->comment_id) ? $request->comment_id : 0,
        ];
        //点赞
        $isthumb = $this->topicService->thumbUp($param);
		$thumbUpCount =  $this->topicService->thumbUpCount($param);
		return [
			'code' => 200,
			'isthumb' => $isthumb,
			'data' => $thumbUpCount
		];
      
    }
	
}
