<?php

namespace App\Repositories;

use DB;
use App\Topic;
use App\TopicComment;
use App\TopicFavourite;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Exception;

class TopicRepository
{

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}
	
	/**
	 * 获取指定ID的话题部分信息
	 */
	public function getTopic ($tid,$columns = ['*'])
	{
		return Topic::where('tid',$tid)->first($columns);
	}
	
	/**
	 * 获取指定ID的话题信息
	 */
	public function getTopicByTopicID(array $param)
	{
		return Topic::select(DB::raw('topic.tid, topic.type, topic.content, topic.img,topic.thumb, topic.view_num, topic.comment_num, topic.favourites_count, topic.created_at,
									  user.openid, user.nickname, user.avatar_url, if(topic_favourite.id>0,1,0) as favorited'))
					->join('user', 'topic.uid', '=', 'user.uid')
					->leftJoin('topic_favourite', function ($join) use ($param) {
						if (isset($param['user_id'])) {
						    $join->on('topic.tid', '=', 'topic_favourite.tid')
						    	 ->where('topic_favourite.uid', '=', $param['user_id']);
						} else {
							$join->where('topic_favourite.tid', '=', 0);
						}
					})
					->where('topic.tid', $param['topic_id'])
					->whereNull('deleted_at')
					->where('admin_deleted', 0)
					->first();
	}

	/**
	 * 获取指定ID的话题评论信息
	 */
	public function getTopicCommentByTopicID(array $param)
	{
		return TopicComment::select(DB::raw('user.openid, user.nickname, user.avatar_url,
											 topic_comment.tcid, topic_comment.tid, topic_comment.cid, topic_comment.cid_username,
											 topic_comment.content, topic_comment.favourites_count, topic_comment.created_at'))
						   ->join('user', 'topic_comment.uid', '=', 'user.uid')
						   ->where('topic_comment.tcid', $param['comment_id'])
						   ->orderBy('tcid','asc')
						   ->first();
	}

	/**
	 * 获取用户的话题列表
	 */
	public function getUserTopic(array $param)
	{
		$user = User::select(DB::raw('uid,nickname,avatar_url'))->where('uid', $param['user_id'])->first();
		$topics = Topic::select(DB::raw('tid, type, content, img, view_num, comment_num, favourites_count, created_at, deleted_at,
									  if(deleted_at IS NULL,0,1) as is_deleted, admin_deleted'))
					->where('uid', $param['user_id'])
					->orderBy('created_at', 'desc')
					->skip(20 * $param['page'] - 20)
					->take(20)
					->get();
		
		return $topics;
	}

	/**
	 * 获取用户的评论列表
	 */
	public function getUserComment(array $param)
	{
		return TopicComment::select(DB::raw('tid, tcid, cid, cid_username, content, favourites_count, created_at, deleted_at,
										     if(deleted_at IS NULL,0,1) as is_deleted, admin_deleted'))
						   ->where('uid', $param['user_id'])
						   ->orderBy('created_at', 'desc')
						   ->skip(20 * $param['page'] - 20)
						   ->take(20)
						   ->get();
	}

	/**
	 * 获取话题列表
	 */
	public function getTopicList(array $param)
	{
		$topics = Topic::select(DB::raw("topic.tid, topic.type, topic.content, topic.img, topic.thumb,topic.view_num,
                                    topic.comment_num, topic.favourites_count, topic.created_at, user.openid, user.nickname,
                                    user.avatar_url, if(topic_favourite.id>0,1,0) as favorited"))
                           ->join('user', 'topic.uid', '=', 'user.uid')
                           ->leftJoin('topic_favourite', function ($join) use ($param) {
                               if (isset($param['user_id'])) {
                                    $join->on('topic.tid', '=', 'topic_favourite.tid')
                                   	 	 ->where('topic_favourite.uid', '=', $param['user_id']);
                               } else {
                               		$join->where('topic_favourite.tid', '=', 0);
                               }
                           })
                           ->whereNull('topic.deleted_at')
                           ->orderBy('topic.created_at', 'desc')
                           ->skip(20 * $param['page'] - 20)
                           ->take(20)
                           ->get();
		return $topics;
		 
	}

	/**
	 * 增加话题浏览量
	 */
	public function incrementViewCount($inListsID, $notInListsID)
	{
        config(['database.default' => 'write']);
        DB::table('topic')->whereIn('tid', $inListsID)->whereNotIn('tid', $notInListsID)->increment('view_num');
        config(['database.default' => 'read']);
	}

	/**
	 * 创建话题
	 */
	public function createTopic(array $param)
	{
		$topic = new Topic;
		$topic->setConnection('write');
		$topic->uid = $param['uid'];
		$topic->type = $param['type'];
		$topic->content = base64_encode($param['content']);
		$topic->img = $param['img'] ?: '';
		$topic->thumb = $param['thumb'] ?: '';
		$topic->save();
	}

	/**
	 * 更新话题状态
	 */
	public function updateTopicStatus(array $param)
	{
        config(['database.default' => 'write']);
		return Topic::where('uid', $param['uid'])
					->where('tid', $param['topic_id'])
					->where('admin_deleted', 0)
					->update(['deleted_at' => $param['deleted_at']]);
	}

	/**
	 * 获取话题评论列表
	 */
	public function getTopicCommentsList(array $param)
	{
		return  TopicComment::select(DB::raw("topic_comment.tcid, reviewer_user.openid, reviewer_user.nickname,
                                            reviewer_user.avatar_url, topic_comment.content, if(topic_favourite.id>0,1,0) as favorited,
                                            topic_comment.favourites_count, topic_comment.created_at, topic_comment.cid as be_review_id,
                                            topic_comment.cid_username as be_review_username"))
                            ->leftJoin('user as reviewer_user', 'topic_comment.uid', '=', 'reviewer_user.uid')
                            ->leftJoin('topic_favourite', function ($join) {
	                                $join->on('topic_comment.uid', '=', 'topic_favourite.uid')
	                                     ->on('topic_comment.tcid', '=', 'topic_favourite.tcid');
                              })
                            ->where('topic_comment.tid', $param['topic_id'])
                            ->whereNull('topic_comment.deleted_at')
                            ->where('topic_comment.admin_deleted', 0)
                            ->orderBy('topic_comment.created_at', 'asc')
                            ->skip(20 * $param['page'] - 20)
                            ->take(20)
                            ->get();
	}
	/**
	 * 获取话题全部评论列表
	 */
	public function getTopicAllCommentsList(array $param)
	{
		$topicComments = TopicComment::select(DB::raw("topic_comment.uid,topic_comment.tcid, reviewer_user.openid, reviewer_user.nickname,
                                            reviewer_user.avatar_url, topic_comment.content, if(topic_favourite.id>0,1,0) as favorited,
                                            topic_comment.favourites_count, topic_comment.created_at, topic_comment.cid as be_review_id,
                                            topic_comment.cid_username as be_review_username"))
                            ->leftJoin('user as reviewer_user', 'topic_comment.uid', '=', 'reviewer_user.uid')
                            ->leftJoin('topic_favourite', function ($join) {
	                                $join->on('topic_comment.uid', '=', 'topic_favourite.uid')
	                                     ->on('topic_comment.tcid', '=', 'topic_favourite.tcid');
                              })
                            ->where('topic_comment.tid', $param['topic_id'])
                            ->whereNull('topic_comment.deleted_at')
                            ->where('topic_comment.admin_deleted', 0)
                            ->orderBy('topic_comment.created_at', 'asc')
                            ->get();
		
		return $topicComments;
	}
	/**
	 * 检验评论是否存在
	 */
	public function getCommentByCommentId($comment_id)
	{
		return TopicComment::select(DB::raw('tcid, tid, cid,uid, cid_username, content, favourites_count, created_at'))
						   ->where('admin_deleted', 0)
						   ->where('tcid', $comment_id)
						   ->first();
	}

	/**
	 * 创建话题评论
	 */
	public function createComment2Topic(array $param)
	{
        Model::unguard();
		$comment = new TopicComment(['uid' => $param['user_id'], 'content' => base64_encode($param['comment']), 'cid' => $param['comment_id'], 'cid_username' => $param['cid_username']]);
		$topic = Topic::find($param['topic_id']);
        $comment->setConnection('write');
		$comment = $topic->comments()->save($comment);
		Model::reguard();

		//增加话题评论统计
		$topic->increment('comment_num');

		return $comment->tcid;
	}

	/**
	 * 删除评论
	 */
	public function deleteComment(array $param)
	{
		DB::transaction(function () use ($param) {
			$comment = TopicComment::where('tcid', $param['comment_id'])
								   ->where('uid', $param['user_id'])
								   ->whereNull('deleted_at')
								   ->where('admin_deleted', 0)
								   ->first();
			$comment->setConnection('write');
			$comment->delete();

			//减少话题评论统计
			$comment->topic->decrement('comment_num');
		});
	}

	/**
	 * 点赞
	 */
	public function thumbUp(array $param)
	{
		$favour = new TopicFavourite;
		$favour->uid = $param['user_id'];
		$favour->tid = $param['topic_id'];
		$favour->tcid = isset($param['topic_comment_id']) ? $param['topic_comment_id'] : 0;
		$favour->save();
		return $favour;
	}
	public function unThumbUp (array $param)
	{
		$favour = new TopicFavourite;
		$favour->uid = $param['user_id'];
		$favour->tid = $param['topic_id'];
		$favour->tcid = isset($param['topic_comment_id']) ? $param['topic_comment_id'] : 0;
		TopicFavourite::where('tid',$param['topic_id'])->where('uid',$param['user_id'])->delete();
		return $favour;
	}
	/**
	 * 检验是否已点赞
	 */
	public function isThumpUp(array $param)
	{
		return TopicFavourite::select(DB::raw('id'))
							 ->where('uid', $param['user_id'])
							 ->where('tid', $param['topic_id'])
							 ->where(function ($query) use ($param) {
							 	 $param['topic_comment_id']
						 			? $query->where('tcid', $param['topic_comment_id'])
						 			: true;
							   })
							 ->first();
	}
	public function thumbUpCount (array $param)
	{
		$topic = Topic::select('favourites_count')->where('tid', $param['topic_id'])->first();

		return  $topic->favourites_count;
	}
	public function inCount ($tid,$column,$number)
	{
		return Topic::where('tid',$tid)->increment($column,$number);
	}
}