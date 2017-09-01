<?php

namespace App\Services;

use DB;
use Session;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\TopicRepository;

class TopicService
{

	protected $request;

	protected $topicRepository;

	protected $userRepository;

	function __construct(Request $request,
						 TopicRepository $topicRepository,
						 UserRepository $userRepository)
	{
		$this->request = $request;
		$this->topicRepository = $topicRepository;
		$this->userRepository = $userRepository;
	}

	/**
	 * 获取指定ID的话题部分信息
	 */
	public function getTopic ($tid,$columns = ['*'])
	{
		$topic = $this->topicRepository->getTopic($tid,$columns = ['*']);
		$topic->content = escape_content($topic->content);
		$topic->imgs = handle_img($topic->img);
		$topic->thumbs = handle_img($topic->thumb);
		return $topic;
	}

	/**
	 * 获取指定ID的话题评论信息
	 */
	public function getTopicByTopicID(array $param)
	{
		$token = isset($this->request->token) ? $this->request->token : '';
		$user = $this->userRepository->getUserByToken($token);
		if ($user) {
			$param['user_id'] = $user->uid;
		}
		$topic = $this->topicRepository->getTopicByTopicID($param);
		$topic->content = escape_content($topic->content);
		$topic->imgs = handle_img($topic->img);
		$topic->thumbs = handle_img($topic->thumb);
		return $topic;
	}

	/**
	 * 获取当前用户的话题
	 */
	public function getCurrentUserTopic(array $param)
	{
		$user = $this->userRepository->getUser();
		$param['user_id'] = $this->userRepository->getUser()->uid;
		$topics =  $this->topicRepository->getUserTopic($param);
		foreach($topics as $k=>$topic){
			$topic['content'] = escape_content($topic['content']);
			$topic->created_at_desc = friendlyDate($topic->created_at->format('Y-m-d H:i:s'));
			$topics[$k]['nickname'] = $user->nickname;
			$topics[$k]['avatar_url'] = $user->avatar_url;
			$topicComments = $this->getTopicAllCommentsList(['topic_id'=> $topic->tid]  );
			$topics[$k]['comment'] = $topicComments;
			$topic->imgs = handle_img($topic->img);
			$topic->thumbs = handle_img($topic->thumb);
		}
		return $topics;
	}
	/**
	 * 获取当前用户的话题（不含评论）
	 */
	public function getMyTopics(array $param)
	{
		$user = $this->userRepository->getUser();
		$param['user_id'] = $this->userRepository->getUser()->uid;
		$topics =  $this->topicRepository->getUserTopic($param);
		foreach($topics as $k=>$topic){
			$topic['content'] = escape_content($topic['content']);
			$topic->created_at_desc = friendlyDate($topic->created_at->format('Y-m-d H:i:s'));
			$topics[$k]['nickname'] = $user->nickname;
			$topics[$k]['avatar_url'] = $user->avatar_url;
			$topics[$k]['openid'] = $user->openid;
			$topic->imgs = handle_img($topic->img);
			$topic->thumbs = handle_img($topic->thumb);
		}
		return $topics;
	}

	/**
	 * 获取当前用户的评论
	 */
	public function getCurrentUserComment(array $param)
	{
		$param['user_id'] = $this->userRepository->getUser()->uid;
		$comments = $this->topicRepository->getUserComment($param);
		foreach ($comments as $key => $comment) {
			$comment->content = escape_content($comment->content);
			$created_at = $comment->created_at->format('Y-m-d H:i:s');
			$comment->created_at_desc = friendlyDate($created_at);
			if($comment->cid){
				$object_comment = \App\TopicComment::where('tcid',$comment->cid)->first(['content']);
				$comment->object_content = $object_comment ? escape_content($object_comment->content) : '话题已删除';
			}else if($comment->tcid)
			{
				$topic =  \App\Topic::where('tid',$comment->tid)->first(['content']);
				$comment->object_content = $topic ? escape_content($topic->content) : '话题已删除';
			}
		}
		return $comments;
	}

	/**
	 * 获取话题列表
	 */
	public function getTopicList(array $param)
	{
		$user = DB::table('user')->where('token', $this->request->token)->first();
		if ($user) {
			$param['user_id'] = $user->uid;
		}
		$topics = $this->topicRepository->getTopicList($param);
		foreach($topics as $k=>$topic){
			$topic->content = escape_content($topic->content);
			$topic->created_at_desc = friendlyDate($topic->created_at->format('Y-m-d H:i:s'));
			$topic->imgs = handle_img($topic->img);
			$topic->thumbs = handle_img($topic->thumb);
			$topicComments = $this->getTopicAllCommentsList(['topic_id'=> $topic->tid]);
			$topics[$k]['comment'] = $topicComments;
		}
		//var_dump($topics);exit;
		return $topics;
	}
	public function getTopics (array $param,$where = [])
	{
		$user = DB::table('user')->where('token', $this->request->token)->first();
		if ($user) {
			$param['user_id'] = $user->uid;
		}
		$topics = $this->topicRepository->getTopicList($param,$where);
		foreach($topics as $k=>$topic){
			$topic['content'] = escape_content($topic['content']);
			$topic->created_at_desc = friendlyDate($topic->created_at->format('Y-m-d H:i:s'));
			$topic->imgs = handle_img($topic->img);
			$topic->thumbs = handle_img($topic->thumb);
		}
		return $topics;
	}
	/**
	 * 增加话题浏览量
	 */
	public function incrementViewCount(array $topics)
	{
		//提取Session记录
        $topicList_tag = Session::get('topicList_tag', []);

		//增加阅读量
		$this->topicRepository->incrementViewCount($topics, $topicList_tag);

		$topicList_tag = array_unique(array_merge($topics, $topicList_tag));
		//记录Session
		Session::put('topicList_tag', $topicList_tag);

	}

	/**
	 * 创建话题
	 */
	public function createTopic(array $param)
	{
		$param['uid'] = $this->userRepository->getUser()->uid;
		$this->topicRepository->createTopic($param);
	}


	/**
	 * 更新话题状态
	 */
	public function updateTopicStatus(array $param)
	{
		$param['uid'] = $this->userRepository->getUser()->uid;
        $result = $this->topicRepository->updateTopicStatus($param);
        if (!$result) {
        	throw new \App\Exceptions\Custom\FoundNothingException();
        }
        return true;
	}

	/**
	 * 获取话题评论
	 */
	public function getTopicComment(array $param)
	{
		$topicComment =  $this->topicRepository->getTopicComment($param);
		$topicComment->content = escape_content($topicComment->content);
		$created_at = $topicComment->created_at->format('Y-m-d H:i:s');
		$topicComment->created_at_desc = friendlyDate($created_at);
		return $topicComment;
	}
	/**
	 * 获取话题评论列表
	 */
	public function getTopicCommentsList(array $param)
	{
		$topicComments =  $this->topicRepository->getTopicCommentsList($param);
		foreach($topicComments as $key=>$topicComment){
			$topicComment['content'] = escape_content($topicComment['content']);
			$created_at = $topicComment->created_at->format('Y-m-d H:i:s');
			$topicComment->created_at_desc = friendlyDate($created_at);
		}
		return $topicComments;
	}
	/**
	 * 获取话题评论列表
	 */
	public function getTopicAllCommentsList(array $param)
	{
		$topicComments =  $this->topicRepository->getTopicAllCommentsList($param);
		foreach($topicComments as $key=>$topicComment){
			$topicComment['content'] = escape_content($topicComment['content']);
			$created_at = $topicComment->created_at->format('Y-m-d H:i:s');
			$topicComment->created_at_desc = friendlyDate($created_at);
		}
		return $topicComments;
	}
	/**
	 * 检验评论是否存在
	 */
	public function isCommentIdExist($comment_id)
	{
		$result = $this->topicRepository->getCommentByCommentId($comment_id);
		if (!$result) {
			throw new \App\Exceptions\Custom\OutputServerMessageException('评论不存在');
		}
		return $result;
	}

	/**
	 * 创建话题评论
	 */
	public function createComment2Topic(array $param)
	{
		//获取当前用户信息
		$param['user_id'] = $this->userRepository->getUser()->uid;

		//获取被评论者的用户名
		$param['cid_username'] = $param['comment_id']
								 ? $this->topicRepository->getTopicCommentByTopicID(['comment_id' => $param['comment_id']])->nickname
								 : '';

		return $this->topicRepository->createComment2Topic($param);
	}

	/**
	 * 删除话题评论
	 */
	public function deleteComment(array $param)
	{
		$param['user_id'] = $this->userRepository->getUser()->uid;

		return $this->topicRepository->deleteComment($param);
	}

	/**
	 * 给话题/话题评论点赞
	 */
	public function thumbUp(array $param)
	{
		$param['user_id'] = $this->userRepository->getUser()->uid;

		//检验是否已点赞
		$isThumpUp = $this->topicRepository->isThumpUp($param);

		if ($isThumpUp) {
        	//throw new \App\Exceptions\Custom\OutputServerMessageException('你已点过赞了');
        	$favour = $this->topicRepository->unThumbUp($param);

        	//增加话题点赞量统计
			if ($param['topic_comment_id']) {
				$favour->topicComment->decrement('favourites_count');
			} else {
				$favour->topic->decrement('favourites_count');
			}
			return -1;
		}
		else
		{
			$favour = $this->topicRepository->thumbUp($param);

			//增加话题点赞量统计
			if ($param['topic_comment_id']) {
				$favour->topicComment->increment('favourites_count');
			} else {
				$favour->topic->increment('favourites_count');
			}
			return 1;
		}

	}
	public function thumbUpCount (array $param)
	{
		return $this->topicRepository->thumbUpCount($param);
	}
	/* 获取话题数量 */
	public function getCount($where = [])
	{
		return $this->topicRepository->getCount($where);
	}
}
