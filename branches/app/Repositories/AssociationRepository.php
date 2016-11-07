<?php

namespace App\Repositories;

use DB;
use App\Message;
use App\Activity;
use App\Information;
use App\Association;
use App\AssociationMember;
use App\AssociationReview;
use App\AssociationNotice;
use Illuminate\Http\Request;
use App\Services\MessageService;

class AssociationRepository
{
	protected static $association;

	protected $request;

	protected $messageService;

	function __construct(Request $request,MessageService $messageService)
	{
		$this->request = $request;
		$this->messageService = $messageService;
	}

	public function getAssociation()
	{
		return self::$association;
	}

	/**
	 * 获得社团活动列表
	 */
	public function getActivitiesList($page, $num)
	{
		return Activity::select(DB::raw("activity.actid, activity.aid, association.aname, association.avatar_url, activity.title, activity.content,
	                                    activity.start_time, activity.end_time, activity.place, activity.view_num, activity.created_at,
	                                    activity.img_url"))
	                   ->join('association', 'activity.aid', '=', 'association.aid')
                       ->whereNull('deleted_at')
	                   ->orderBy('activity.created_at', 'desc')
	                   ->skip(10 * $page - 10)
	                   ->take($num ?: 10)
	                   ->get();
	}

	/**
	 * 获得单个社团所有活动
	 */
	 public function getAssociationActivity($page, $num, $aid)
	{
		return Activity::select(DB::raw("activity.actid, activity.aid, association.aname, association.avatar_url, activity.title, activity.content,
	                                    activity.start_time, activity.end_time, activity.place, activity.view_num, activity.created_at,
	                                    activity.img_url"))
	                   ->join('association', 'activity.aid', '=', 'association.aid')
                       ->whereNull('deleted_at')
					   ->where('activity.aid',$aid)
	                   ->orderBy('activity.created_at', 'desc')
	                   ->skip(10 * $page - 10)
	                   ->take($num ?: 10)
	                   ->get();
	}

	/**
	 * 获得单个社团活动
	 */
	public function getActivities($activity_id)
	{
		return Activity::select(DB::raw("activity.actid, activity.aid, association.aname, association.avatar_url, activity.title, activity.content,
	                                    activity.start_time, activity.end_time, activity.place, activity.view_num, activity.created_at,
	                                    activity.img_url"))
	                   ->join('association', 'activity.aid', '=', 'association.aid')
                       ->where('activity.actid', $activity_id)
	                   ->whereNull('deleted_at')
	                   ->first();
	}

	/**
	 * 获得社团资讯列表
	 */
	public function getInfomationList($page, $num)
	{
		return Information::select(DB::raw("information.iid, association.aid, association.aname, association.avatar_url, information.title, information.content,
                                       information.view_num, information.created_at, information.img_url"))
                          ->join('association', 'information.aid', '=', 'association.aid')
                          ->whereNull('information.deleted_at')
                          ->orderBy('information.created_at', 'desc')
                          ->skip(10 * $page - 10)
	                   	  ->take($num ?: 10)
                          ->get();
	}

	/**
	 * 获得单个社团资讯
	 */
	public function getInfomation($information_id)
	{
		return Information::select(DB::raw("information.iid, association.aid, association.aname, association.avatar_url, information.title, information.content,
                                       information.view_num, information.created_at, information.img_url"))
                          ->join('association', 'information.aid', '=', 'association.aid')
                          ->where('information.iid', $information_id)
                          ->whereNull('information.deleted_at')
                          ->first();
	}


	/**
	 * 增加社团活动/资讯浏览量 iid actid
	 */
	public function incrementViewCount($inListsID, $notInListsID, $table_name, $table_id)
	{
        config(['database.default' => 'write']);
        DB::table($table_name)->whereIn($table_id, $inListsID)->whereNotIn($table_id, $notInListsID)->increment('view_num');
        config(['database.default' => 'read']);
	}

	/**
	 * 根据用户ID获取其担任负责人的社团信息
	 */
	public function getAssociationByUserID($user_id)
	{
		self::$association = Association::select(DB::raw('association.aid, association.aname, association.avatar_url, association_member.uid'))
				                        ->leftJoin('association_member', 'association.aid', '=', 'association_member.aid')
				                        ->where('association_member.uid', $user_id)
				                        ->whereIn('association_member.level', [1,2,3])
				                        ->whereNull('association_member.deleted_at')
				                        ->first();
		return self::$association;
	}

	/**
	 * 根据社团ID获取其下所有成员的信息(过滤掉已被删除的成员)
	 */
	public function getMemberByAssociationIDWithoutDeleted($association_id)
	{
		return AssociationMember::select(DB::raw('association_member.aid, association.aname, association_member.uid,
												  association_member.level'))
								->join('association', 'association_member.aid', '=', 'association.aid')
								->whereNull('association_member.deleted_at')
								->where('association.aid', $association_id)
								->get();
	}

	/**
	 * 根据社团ID获取其下所有成员的信息(不过滤掉已被删除的成员)
	 */
	public function getMemberByAssociationIDWithDeleted($association_id)
	{
		return AssociationMember::select(DB::raw('association_member.aid, association.aname, association_member.uid,
												  association_member.level'))
								->join('association', 'association_member.aid', '=', 'association.aid')
								->where('association.aid', $association_id)
								->get();
	}

	/**
	 * 创建资讯
	 */
	public function createInformation($association_aid, $user_id, $param)
	{
		$ann = new Information;
		$ann->setConnection('write');
		$ann->aid = $association_aid;
		$ann->uid = $user_id;
		$ann->title = $param['title'];
		$ann->content = $param['content'];
		$ann->img_url = $param['img_url'] ?: '';
		$ann->save();
	}

	/**
	 * 创建活动
	 */
	public function createActivity($user_id, $param)
	{
		$act = new Activity;
		$act->setConnection('write');
		$act->aid = $param['aid'];
		$act->uid = $user_id;
		$act->title = $param['title'];
		$act->content = $param['content'];
		$act->start_time = $param['start_time'];
		$act->end_time = $param['end_time'];
		$act->place = $param['place'];
		$act->img_url = $param['img_url'] ?: '';
		$act->save();
	}

	/**
	 * 设置社团信息
	 */
	public function setProfile($introduction)
	{
		self::$association->setConnection('write');
        self::$association->introduction = $introduction;
        self::$association->save();
	}

	/**
	 * 更新社团信息
	 */
	public function updateInfo(array $info)
	{
		$updateUser = false;
		$userArray = ['avatar_url', 'introduction'];

		$association = self::$association;
		foreach ($userArray as $key => $value) {
			if (!empty($info[$value])) {
				$association->$value = $info[$value];
				$updateUser = true;
			}
		}
		!$updateUser or $association->save();

	}

	/**
	 * 获得热门活动
	 */
	public function getHotActivities()
	{
		return Activity::select(DB::raw("activity.actid, activity.aid, association.aname, association.avatar_url, activity.title, activity.content,
	                                    activity.start_time, activity.end_time, activity.place, activity.view_num, activity.created_at,
	                                    if(activity.img_url IS NULL,'',activity.img_url) as img_url"))
	                   ->join('association', 'activity.aid', '=', 'association.aid')
                       ->whereNull('deleted_at')
	                   ->orderBy('activity.created_at', 'desc')
	                   ->take(3)
	                   ->get();
	}

	/**
	 * 获得热门资讯
	 */
	public function getHotInformation()
	{
		return Information::select(DB::raw("information.iid, association.aid, association.aname, association.avatar_url, information.title, information.content,
                                       information.view_num, information.created_at, if(information.img_url IS NULL,'',information.img_url) as img_url"))
                          ->join('association', 'information.aid', '=', 'association.aid')
                          ->whereNull('information.deleted_at')
                          ->orderBy('information.created_at', 'desc')
	                   	  ->take(3)
                          ->get();
	}

	public function getAssociations($page)
	{
		return  Association::select(DB::raw('association.aid,aname, avatar_url, member_number, leader,label,count(activity.aid) as activity_count,introduction,if(association_member.uid IS NOT NULL,association_member.uid,0) as uid'))
						->leftJoin('association_member','association.aid','=','association_member.aid')
						->leftJoin('activity','association.aid','=','activity.aid')
						->whereNull('activity.deleted_at')
						->groupBy('association.aid')
						->skip(20 * $page - 20)
						->take(20)
                        ->get();

	}

	/* 获得所有社团总数 */
	public function getAssociationNum()
	{
		return DB::table('association')->count();
	}
	
	/* 获得所有社团活动总数 */
	public function getActivityNum()
	{
		return DB::table('activity')->whereNull('deleted_at')->count();
	}

	public function getAssociationsDetails($aid,$uid)
	{
		$ar_uid = AssociationMember::select(DB::raw('uid'))
						->where('association_member.uid', $uid)
						->where('association_member.aid',$aid)
						->whereNull('deleted_at')
						->first();

		if(!empty($ar_uid->uid)){
			return $associations = Association::select(DB::raw('association.aid,association.aname, association.avatar_url,if(background_url IS NOT NULL,background_url,"") as background_url, member_number, leader,count(activity.actid) as activity_count,introduction,association_member.uid,association_member.level,label,if(association_notice.anid IS NOT NULL,association_notice.anid,0) as anid,if(association_notice.notice IS NOT NULL,association_notice.notice,"") as notice,if(notice_user.nickname IS NOT NULL,notice_user.nickname,"") as sendNotice_nickname,if(association_notice.created_at IS NOT NULL,association_notice.created_at,"") as notice_created_at,if(association_notice.anid IS NOT NULL,MAX(association_notice.anid),0) as max_anid'))
				->leftJoin('activity','association.aid','=','activity.aid')
				->leftJoin('association_member','association.aid','=','association_member.aid')
				->leftJoin('association_notice','association.aid','=','association_notice.aid')
				->leftJoin('user as notice_user','association_notice.uid','=','notice_user.uid')
				->whereNull('activity.deleted_at')
				->where('association.aid',$aid)
				->where('association_member.uid',$ar_uid->uid)
				->groupBy('anid')
				->orderBy('association_notice.anid', 'desc')
				->first();
		}else{
			return $associations = Association::select(DB::raw('association.aid,association.aname, association.avatar_url,if(background_url IS NOT NULL,background_url,"") as background_url, member_number, leader,count(activity.actid) as activity_count,introduction,label,if(association_notice.anid IS NOT NULL,association_notice.anid,0) as anid,if(association_notice.notice IS NOT NULL,association_notice.notice,"") as notice,if(user.nickname IS NOT NULL,user.nickname,"") as sendNotice_nickname,if(association_notice.created_at IS NOT NULL,association_notice.created_at,"") as notice_created_at,if(association_notice.anid IS NOT NULL,MAX(association_notice.anid),0) as max_anid'))
				->leftJoin('activity','association.aid','=','activity.aid')
				->leftJoin('association_notice','association.aid','=','association_notice.aid')
				->leftJoin('user','association_notice.uid','=','user.uid')
				->whereNull('activity.deleted_at')
				->where('association.aid',$aid)
				->groupBy('anid')
				->orderBy('association_notice.anid', 'desc')
				->first();
		}
	}

	public function getMyAssociations($user_id)
	{
		$associationMember = AssociationMember::select(DB::raw('association_member.aid,association_member.uid,association.aname, association.avatar_url,association.background_url,association.member_number, association.leader,association_member.level, association.introduction,count(activity.actid) as activity_count,label'))
								->join('association', 'association_member.aid', '=', 'association.aid')
								->leftJoin('activity','association_member.aid','=','activity.aid')
							    ->where('association_member.uid', $user_id)
								->whereNull('association_member.deleted_at')
								->whereNull('activity.deleted_at')
								->groupBy('association_member.aid')
	                            ->get();
		/*if(empty($associationMember[0]->aid)){
			return [];
		}*/
		return $associationMember;
	}

	public function getAssociationNotice($aid,$uid )
	{
		AssociationMember::where('aid',$aid)
							->where('uid',$uid)
							->update([
								'notice_view_at'=>date("Y-m-d H:i:s")
							]);
		/* return AssociationNotice::select(DB::raw('association_notice.anid,association_notice.aid,association_notice.uid,association_notice.notice,user.nickname'))
								->leftJoin('user','association_notice.uid','=','user.uid')
								->where('association_notice.aid',$aid)
								->orderBy('association_notice.anid', 'desc')
	                            ->first(); */
	}

	/* 检查是否有新公告 */
	public function checkNewNotice($uid,$aid){
		$associationMember = AssociationMember::select(DB::raw('notice_view_at,level'))
												->where('aid',$aid)
												->where('uid',$uid)
												->whereNull('association_member.deleted_at')
												->first();
		if($associationMember){
			if($associationMember->level == 0){
				return 400;
			}
			$last_strtotime = strtotime($associationMember->notice_view_at);

			$associationNotice = AssociationMember::select(DB::raw('association_notice.created_at'))
									->leftJoin('association_notice', 'association_member.aid', '=', 'association_notice.aid')
									->where('association_member.aid',$aid)
									->where('association_member.uid',$uid)
									->whereNull('association_member.deleted_at')
									->orderBy('association_notice.created_at', 'desc')
									->first();
			$new_strtotime = strtotime($associationNotice->created_at);
			if($new_strtotime > $last_strtotime){
				return 200;
			}
		}else{
			return 401;
		}
		return;
	}
	
	

	public function getAssociationMember($aid,$page)
	{
		$associationMembers = AssociationMember::select(DB::raw('association_member.amid,association_member.aid,association_review.uid as uid,user.nickname,association_review.ar_username as realname,user.avatar_url,association_member.level,association_review.mobile_no,member_number,leader,MAX(association_review.id) as max_arid'))
								->leftJoin('user', 'association_member.uid', '=', 'user.uid')
								->leftJoin('association_review', 'association_member.uid', '=', 'association_review.uid')
								->leftJoin('association', 'association_member.aid', '=', 'association.aid')
								->where('association_member.aid',$aid)
								->where('association_review.status','passed')
								->whereNull('association_member.deleted_at')
								->groupBy('association_review.uid')
								->orderBy('association_member.level','desc')
	                            ->skip(20 * $page - 20) 
								->take(20)
								->get();
		foreach($associationMembers as $k=>$associationMember){
			if($associationMember->level == 1){
				$temArr[] = $associationMember;
				unset($associationMembers[$k]);
			}else{
				$Member[] = $associationMember;
			}
		}
		$resArr=array_merge($temArr,$Member); 
		return $resArr;
	}
	public function getAssociationAllMemberUids($aid)
	{
		return AssociationMember::select(DB::raw('association_review.uid as uid'))
								->leftJoin('user', 'association_member.uid', '=', 'user.uid')
								->leftJoin('association_review', 'association_member.uid', '=', 'association_review.uid')
								->leftJoin('association', 'association_member.aid', '=', 'association.aid')
								->where('association_member.aid',$aid)
								->where('association_review.status','passed')
								->whereNull('association_member.deleted_at')
								->groupBy('association_review.uid')
								->orderBy('association_member.amid','asc')
								->get();
	}
	public function updateMemberLevel($aid,$level,$uid,$token_uid)
	{
		$token_associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$token_uid)
											->whereNull('association_member.deleted_at')
											->first();
		$associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$uid)
											->whereNull('association_member.deleted_at')
											->first();
		$memberManage = AssociationMember::where('aid',$aid)
											->where('level',2)
											->orwhere('level',3)
											->whereNull('association_member.deleted_at')
											->get();
		if(count($memberManage) == 2){
			return 402;
		}elseif($token_associationMember->level != 1 && $associationMember->level == 1){
			return 401;
		}elseif($token_associationMember->level == 2 && $associationMember->level == 2){
			return 401;
		}
		$associationMember->level = $level;
		
		if($associationMember->save()){
			return 200;
		}else{
			return 403;
		}
	}
	public function deleteMember($aid,$uid,$token_uid )
	{
		$token_associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$token_uid)
											->first();
		$associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$uid)
											->first();
		if($token_associationMember->level != 1 && $associationMember->level == 1){
			return 401;
		}elseif($token_associationMember->level == 2 && $associationMember->level == 2){
			return 401;
		}
		$associationMember->deleted_at = date("Y-m-d H:i:s");
	
		$association = Association::where('aid',$aid)->first();
		$association->member_number--;
		
		if($associationMember->save() && $association->save()){
			return 200;
		}
		return ;
	}

	public function releaseNotice($aid,$notice,$uid){
		$associationNotice = new AssociationNotice;
		$associationNotice->aid = $aid;
		$associationNotice->uid = $uid;
		$associationNotice->notice = $notice;
		
		if($associationNotice->save()){
			return true;
		}else{
			throw new \App\Exceptions\Custom\OutputServerMessageException('请求失败');
		}
	}

	public function quitAssociation($aid,$uid){
		$associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$uid)
											->whereNull('deleted_at')
											->first();
		
		if(!$associationMember){
			return 401;
		}else if($associationMember->level == 1){
			return 402;
		}
		$associationMember->deleted_at = date("Y-m-d H:i:s");
		$associationMember->save();

		$association = Association::where('aid',$aid)->first();
		$association->member_number--;
		$association->save();

		$associationMembers = AssociationMember::where('aid',$aid)
											->where('level','!=',0)
											->whereNull('deleted_at')
											->get();
		$associationReview = AssociationReview::where('aid',$aid)
												->where('uid',$uid)
												->first();
		$content = "成员：".$associationReview->ar_username." 已退出".$association->aname."社团";
		/* 纸条推送 */
		foreach($associationMembers as $k => $v){
			$this->messageService->SystemMessage2SingleOne($v->uid, $content);
		}
	}

	public function checkMemberList($aid,$uid){
		AssociationMember::where('aid',$aid)
							->where('uid',$uid)
							->update([
								'member_view_at'=>date("Y-m-d H:i:s")
							]);
		
		$associationReviews = AssociationReview::select(DB::raw('MAX(id) as maxid,uid'))
										->where('aid',$aid)
										->groupBy('uid')
										->where('status','checking')
										->get();
		$ass = [];					
		foreach($associationReviews as $k=>$associationReview){
			$associationMember = AssociationMember::where('uid',$associationReview->uid)
											->where('aid',$aid)
											->whereNull('deleted_at')
											->first();
			if(!$associationMember){
				$ass[] = AssociationReview::select(DB::raw('association_review.uid,association_review.id,aid,causes,ar_username,profession,association_review.mobile_no,status,association_review.created_at,user.avatar_url'))
									->leftJoin('user', 'association_review.uid', '=', 'user.uid')
									->where('id',$associationReview->maxid)
									->first();
			}		
		}
		return $ass;
	}
	
	/* 检查是否有新审核成员 */
	public function checkNewMember($uid,$aid){
		$associationMember = AssociationMember::select(DB::raw('member_view_at,level'))
												->where('aid',$aid)
												->where('uid',$uid)
												->whereNull('association_member.deleted_at')
												->first();
		if($associationMember){
			if($associationMember->level == 0){
				return 400;
			}
			$checkMemberList = $this->checkMemberList($aid,$uid);
			if(!empty($checkMemberList)){
				return 200;
			}
		}else{
			return 401;
		}
		
	}

	public function checkMember($aid,$uid,$status){
		/* 通过审核 */
		$associationReview = AssociationReview::where('aid',$aid)
											->where('uid',$uid)
											->where('status',"checking")
											->orderBy('id', 'desc')
											->first();
		$associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$uid)
											->whereNull('association_member.deleted_at')
											->first();
		if($associationMember){
			$associationReview->status = 'failed';
			$associationReview->save();
			return 304;
		}
		if($status == 0){
			$associationReview->status = 'passed';
			$associationReview->save();

			$associationMember = new  AssociationMember;
			$associationMember->aid = $aid;
			$associationMember->uid = $uid;
			$associationMember->save();

			$association = Association::where('aid',$aid)->first();
			$association->member_number++;
			$association->save();
		}else if($status == 1){
			$associationReview->status = 'failed';
			$associationReview->save();
		}
		return 200;
	}
	
	public function deleteActivity($actid,$uid,$aid){
		$associationMember = AssociationMember::where('aid',$aid)
											->where('uid',$uid)
											->whereNull('association_member.deleted_at')
											->first();
		if($associationMember->level == 0){
			return 401;
		}
		$activity = Activity::select(DB::raw("actid"))
							->where('actid',$actid)
							->whereNull('deleted_at')
							->first();
		$activity->deleted_at = date("Y-m-d H:i:s");
		if($activity->save()){
			return 200;
		}
		return;
	}

}