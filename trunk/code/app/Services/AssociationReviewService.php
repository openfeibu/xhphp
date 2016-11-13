<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\AssociationReviewRepository;

class AssociationReviewService
{

	protected $request;

	protected $userRepository;

	protected $associationReviewRepository;

	function __construct(Request $request,
						 UserRepository $userRepository,
						 AssociationReviewRepository $associationReviewRepository)
	{
		$this->request = $request;
        $this->userRepository = $userRepository;
		$this->associationReviewRepository = $associationReviewRepository;
	}

	/**
	 * 检验该用户是否有正在审核的申请
	 */
	public function isRequestAlreadyInProgress()
	{
		$uid = $this->userRepository->getUser()->uid;
		$result =  $this->associationReviewRepository->getJoinRequestWhitStatus($uid, 'checking')->first();
		if ($result) {
			throw new \App\Exceptions\Custom\OutputServerMessageException('你已有正在审核中的社团入驻申请，请等待管理员审核后方可提交新的申请');
		}
	}

	/**
	 * 创建社团入驻申请
	 */
	public function createJoinRequest($param)
	{
		$param['uid'] = $this->userRepository->getUser()->uid;
		$result = $this->associationReviewRepository->createJoinRequest($param);
		return true;
	}
	
	public function joinAssociationMember($aid,$ar_username,$profession,$causes,$mobile_no){
		//获取当前用户的信息
		$uid = $this->userRepository->getUser()->uid;
		return  $this->associationReviewRepository->joinAssociationMember($aid,$uid,$ar_username,$profession,$causes,$mobile_no);
	}

}