<?php

namespace App\Services;

use Session;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\MessageRepository;
use App\Repositories\AssociationRepository;
use App\Services\MessageService;
use App\Association;
use DB;

class AssociationService
{

	protected $request;

    protected $messageRepository;

    protected $userRepository;

	protected $messageService;

	function __construct(Request $request,
						AssociationRepository $associationRepository,
						MessageRepository $messageRepository,
						UserRepository $userRepository,
						MessageService $messageService)
	{
		$this->request = $request;
		$this->associationRepository = $associationRepository;
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
		$this->messageService = $messageService;
	}


	/**
	 * 获得社团活动列表
	 */
	public function activitiesList($page, $num)
	{
		return $this->associationRepository->getActivitiesList($page, $num);
	}

	/**
	 * 获得单个社团所有活动
	 */
	public function getAssociationActivity($page, $num, $aid)
	{
		return $this->associationRepository->getAssociationActivity($page, $num, $aid);
	}

	/**
	 * 获得单个社团活动
	 */
	public function getActivity($activity_id)
	{
		return $this->associationRepository->getActivities($activity_id);
	}

	/**
	 * 获得社团资讯列表
	 */
	public function informationList($page, $num)
	{
		return $this->associationRepository->getInfomationList($page, $num);
	}

	/**
	 * 获得单个社团资讯
	 */
	public function getInfomation($information_id)
	{
		return $this->associationRepository->getInfomation($information_id);
	}

	/**
	 * 增加社团活动/资讯浏览量
	 */
	public function incrementViewCount($lists, $type)
	{
		switch ($type) {
			case 'activity':
				$id = 'actid';
				break;

			case 'information':
				$id = 'iid';
				break;

			default:
				throw new \App\Exceptions\Custom\RequestFailedException();
				break;
		}
		//提取数组中的活动/资讯ID
		$lists_id = $lists->lists($id)->toArray();

		//提取Session记录
		$lists_tag = Session::get($type . '_tag', []);
		if ($lists_tag) {
            $lists_tag = array_unique(array_merge($lists_id, $lists_tag));
        } else {
            $lists_tag = [];
        }

		//增加阅读量
		$this->associationRepository->incrementViewCount($lists_id, $lists_tag, $type, $id);

		//记录Session
		Session::put($type . '_tag', $lists_id);
	}

	/**
	 * 创建纸条信息
	 */
	public function createMessage($content, $type)
	{
		try {
			//获取当前用户的信息
			$uid = $this->userRepository->getUser()->uid;

			//获取当前用户担任负责人的社团信息
			$association = $this->getAssociationByUserID($uid);

			//获取社团对应的所有现任成员的信息
			$association_member = $this->getMemberByAssociationIDWithoutDeleted($association->aid);

			$msg = [];
			foreach ($association_member->lists('uid') as $member_id) {
			    $msg[] = [
			        'uid_receiver' => $member_id,
			        'aid_sender' => $association->aid,
			        'type' => $type,
			        'name' => $association->aname,
			        'content' => $content,
			        'created_at' => date('Y-m-d H:i:s'),
			    ];
			}

			//创建消息
			$this->messageRepository->message2AllMember($msg);
		} catch (Exception $e) {
			throw new \App\Exceptions\Custom\RequestFailedException();
		}
	}

	/**
	 * 创建社团资讯
	 */
	public function createInformation($param)
	{
		try {
			//获取当前用户的信息
			$uid = $this->userRepository->getUser()->uid;

			//获取当前用户担任负责人的社团信息
			$association = $this->getAssociationByUserID($uid);

			//创建资讯
			$this->associationRepository->createInformation($association->aid, $uid, $param);
		} catch (Exception $e) {
			throw new \App\Exceptions\Custom\RequestFailedException();
		}
	}

	/**
	 * 创建社团活动
	 */
	public function createActivity($param)
	{
		try {
			//获取当前用户的信息
			$uid = $this->userRepository->getUser()->uid;


			//创建活动
			$this->associationRepository->createActivity($uid, $param);
		} catch (Exception $e) {
			throw new \App\Exceptions\Custom\RequestFailedException();
		}
	}

	/**
	 * 获取当前用户担任负责人的社团信息
	 */
	public function getAssociationByUserID($user_id)
	{
		$association = $this->associationRepository->getAssociationByUserID($user_id);
		if (!$association) {
			throw new \App\Exceptions\Custom\UserNotAssociationChiefException();
		}
		return $association;
	}

	/**
	 * 检验当前用户是否已经担任社团负责人
	 */
	public function isUserAlreadyBeenChief()
	{
		//获取当前用户的信息
		$uid = $this->userRepository->getUser()->uid;

		//查询当前用户是否已经担任社团负责人
		$association = $this->associationRepository->getAssociationByUserID($uid);
		if ($association) {
			throw new \App\Exceptions\Custom\OutputServerMessageException('你已经是社团的负责人了，不能再申请入驻其它社团。请让其他负责人申请。');
		}

		return true;
	}

	/**
	 * 获取社团对应的所有现任成员的信息
	 */
	public function getMemberByAssociationIDWithoutDeleted($association_id)
	{
		return $this->associationRepository->getMemberByAssociationIDWithoutDeleted($association_id);
	}

	/**
	 * 更新社团信息
	 */
	public function updateInfo($param)
	{
		try {
			//获取当前用户的信息
			$uid = $this->userRepository->getUser()->uid;

			//获取当前用户担任负责人的社团信息
			$association = $this->getAssociationByUserID($uid);

			//更新头像
		    $this->associationRepository->updateInfo($param);
		    return true;
		} catch (Exception $e) {
		    throw new \App\Exceptions\Custom\RequestFailedException();
		}
	}

	/**
	 * 获得热门活动
	 */
	public function getHotActivities()
	{
		return $this->associationRepository->getHotActivities();
	}

	/**
	 * 获得热门资讯
	 */
	public function getHotInformation()
	{
		return $this->associationRepository->getHotInformation();
	}

	/*获取社团列表*/
	public function getAssociations($page)
	{
		$associations = $this->associationRepository->getAssociations($page);
		$token = isset($this->request->token) ? $this->request->token : '';
		$user = $this->userRepository->getUserByToken($token);
		$uid = isset($user->uid) ? $user->uid : 0;
		foreach( $associations as $key => $association )
		{
			$association->mylevel = $this->getAssociationMemberLevel($association->aid,$uid);
		}
		return $associations;
	}

	/* 获得所有社团总数 */
	public function getAssociationNum()
	{
		return $this->associationRepository->getAssociationNum();
	}
	/* 获得所有社团活动总数 */
	public function getActivityNum()
	{
		return $this->associationRepository->getActivityNum();
	}

	/*获取社团详情*/
	public function getAssociationsDetails($aid,$token)
	{
		if(!empty($token)){
			$uid = $this->userRepository->getUser()->uid;
		}else{
			$uid = "";
		}
		$associationsDetail = $this->associationRepository->getAssociationsDetails($aid,$uid);
		if(isSet($associationsDetail->level)){
			$associationsDetail->association_level = trans('common.association_level.'.$associationsDetail->level);
		}else{
			$associationsDetail->level = -1;
		}
		if(!isSet($associationsDetail->uid)){
			$associationsDetail->uid = 0;
		}
		if(!isSet($associationsDetail->association_level)){
			$associationsDetail->association_level = "";
		}

		return $associationsDetail;
	}

	/*获取我加入的社团列表*/
	public function getMyAssociations()
	{
		$uid = $this->userRepository->getUser()->uid;
		$myAssociations =  $this->associationRepository->getMyAssociations($uid);
		if(!empty($myAssociations)){
			foreach($myAssociations as $k=>$myAssociation){
				if(isSet($myAssociation->level)){
					$myAssociation->association_level = trans('common.association_level.'.$myAssociation->level);
				}else{
					$myAssociation->level = -1;
				}
			}
		}

		return $myAssociations;
	}

	/*获取我加入的社团的公告*/
	public function getAssociationNotice($aid)
	{
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->getAssociationNotice($aid,$uid);
	}

	public function checkNewNotice($aid){
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->checkNewNotice($uid,$aid);
	}

	public function checkNewMember($aid){
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->checkNewMember($uid,$aid);
	}

	/*获取社团的成员*/
	public function getAssociationMember($aid,$page)
	{
		$associationMembers = $this->associationRepository->getAssociationMember($aid,$page);
		foreach($associationMembers as $k=>$associationMember){
			if(isSet($associationMember->level)){
				$associationMember->association_level = trans('common.association_level.'.$associationMember->level);
			}else{
				$associationMember->level = -1;
			}
		}
		return $associationMembers;
	}
	/*获取社团所有成员*/
	public function getAssociationAllMemberUids ($aid)
	{
		$associationMembers = $this->associationRepository->getAssociationAllMemberUids($aid);
		return $associationMembers;
	}
	/*更新成员等级*/
	public function updateMemberLevel($aid,$level,$uid)
	{
		$token_uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->updateMemberLevel($aid,$level,$uid,$token_uid );
	}
	/*踢人*/
	public function deleteMember($aid,$uid)
	{
		$token_uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->deleteMember($aid,$uid,$token_uid );
	}

	public function releaseNotice($aid,$notice){
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->releaseNotice($aid,$notice,$uid);
	}

	public function quitAssociation($aid){
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->quitAssociation($aid,$uid);
	}

	public function checkMemberList($aid){
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->checkMemberList($aid,$uid);
	}

	public function checkMember($aid,$uid,$status){

		$checkMember = $this->associationRepository->checkMember($aid,$uid,$status);
		if($checkMember == 200){
			$association = Association::select(DB::raw('aname'))
									->where('aid',$aid)
									->first();
			$content = "您加入的社团：".$association->aname."申请已经通过审核！";
			$this->messageService->SystemMessage2SingleOne($uid, $content, $push = false, $type = '系统通知', $name = '系统');
		}else if($checkMember == 403){
			$association = Association::select(DB::raw('aname'))
									->where('aid',$aid)
									->first();
			$content = "您加入的社团：".$association->aname."申请审核未通过，请重试！";
			$this->messageService->SystemMessage2SingleOne($uid, $content, $push = false, $type = '系统通知', $name = '系统');
		}
		return $checkMember;
	}

	public function deleteActivity($actid,$aid){
		$uid = $this->userRepository->getUser()->uid;
		return $this->associationRepository->deleteActivity($actid,$uid,$aid);
	}
	public function getAssociationAdmins ($aid)
	{
		return $this->associationRepository->getAssociationAdmins($aid);
	}
	public function getAssociationMemberLevel($aid,$uid)
	{
		$data = $this->associationRepository->getAssociationMemberLevel($aid,$uid);
		if($data){
			return $data->level;
		}
		return -1;
	}
}
