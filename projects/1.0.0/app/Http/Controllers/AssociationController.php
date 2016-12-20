<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Input;
use Session;
use App\Http\Requests;
use App\Services\HelpService;
use App\Services\ImageService;
use App\Services\MessageService;
use App\Services\AssociationService;
use App\Http\Controllers\Controller;
use App\Services\PushService;
use App\Services\AssociationReviewService;


class AssociationController extends Controller
{

    protected $associationService;

    protected $helpService;

    protected $imageService;

    protected $messageService;

    protected $associationReviewService;

    function __construct(AssociationService $associationService,
                         HelpService $helpService,
                         ImageService $imageService,
                         MessageService $messageService,
                         AssociationReviewService $associationReviewService,
                         PushService $pushService)
    {
	    parent::__construct();
        $this->middleware('auth', ['except' => ['getActivityList', 'getInformationList', 'getProfile', 'getActivity', 'getInfomation', 'getHotActivities', 'getHotInformation','getAssociations','getAssociationActivity']]);

        $this->associationService = $associationService;
        $this->helpService = $helpService;
        $this->imageService = $imageService;
        $this->messageService = $messageService;
        $this->pushService = $pushService;
        $this->associationReviewService = $associationReviewService;
    }

    public function join(Request $request)
    {
        //检验请求参数
        $rule = [
            'name' => 'required|unique:association,aname',
            'avatar_url' => 'required',
            'certificate' => 'required',
            'causes' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //检验该用户是否有正在审核的申请
        $this->associationReviewService->isRequestAlreadyInProgress();

        //检验该用户是否已经担任社团负责人
        $this->associationService->isUserAlreadyBeenChief();

        $param = [
            'name' => $request->name,
            'avatar_url' => $request->avatar_url,
            'certificate' => $request->certificate,
            'causes' => $request->causes,
        ];
        //创建社团入驻申请
        $this->associationReviewService->createJoinRequest($param);

        //推送纸条
        $this->messageService->SystemMessage2CurrentUser("您好，$request->name申请入驻请求已提交，我们将尽快为您办理，请您耐心等待管理员的审核，一般情况下24小时内会反馈给您审核结果。");

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function uploadJoinFiles(Request $request)
    {
        //上传头像文件
        $images_url = $this->imageService->uploadImages(Input::all(), 'association');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url,
        ];
    }

    public function getActivityList(Request $request)
    {
        //检验请求参数
        $rule = [
            'page' => 'required|integer',
            'num' => 'sometimes|required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获得社团活动列表
        $activities = $this->associationService->activitiesList($request->page, $request->num);

        //增加社团活动浏览量
        $this->associationService->incrementViewCount($activities, 'activity');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $activities
        ];

    }

	public function getAssociationActivity(Request $request)
    {
        //检验请求参数
        $rule = [
			'association_id' => 'required',
            'page' => 'required|integer',
            'num' => 'sometimes|required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获得社团活动列表
        $activities = $this->associationService->getAssociationActivity($request->page, $request->num,$request->association_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $activities
        ];

    }

    public function getActivity(Request $request)
    {
        //检验请求参数
        $rule = [
            'activity_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获得社团活动列表
        $activity = $this->associationService->getActivity($request->activity_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $activity
        ];
    }

    public function getInformationList(Request $request)
    {
        //检验请求参数
        $rule = [
            'page' => 'required|integer',
            'num' => 'sometimes|required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获得社团资讯列表
        $information = $this->associationService->informationList($request->page, $request->num);

        //增加社团资讯浏览量
        $this->associationService->incrementViewCount($information, 'information');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $information
        ];
    }

    public function getInfomation(Request $request)
    {
        //检验请求参数
        $rule = [
            'information_id' => 'required|integer',
        ];
        $this->helpService->validateParameter($rule);

        //获得社团活动列表
        $information = $this->associationService->getInfomation($request->information_id);

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $information
        ];
    }

    public function createMessage(Request $request)
    {
        //检验请求参数
        $rule = [
            'content' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //频率限制

        //创建纸条
        $this->associationService->createMessage($request->content, '社团通知');

        //消息推送
        #todo

        throw new \App\Exceptions\Custom\RequestSuccessException();

    }

    public function createInformation(Request $request)
    {
        //检验请求参数
        $rule = [
            'token' => 'required',
            'title' => 'required',
            'content' => 'required',
            'img_url' => 'sometimes|required',
        ];
        $this->helpService->validateParameter($rule);

        //频率限制

        // if ($request->img_url) {
        //     //检验图片链接是否存在数据库
        //     $imgs = explode(',', $request->img_url);
        //     foreach ($imgs as $key => $img) {
        //         if (!in_array($img, Session::get('uploadImgUrl', ['nothing']))) {
        //             throw new \App\Exceptions\Custom\OutputServerMessageException('请重新上传图片');
        //         }
        //     }
        // }

        $param = [
            'title' => $request->title,
            'content' => $request->content,
            'img_url' => $request->img_url,
        ];
        //创建社团资讯
        $this->associationService->createInformation($param);
		
        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function uploadInformationImageFiles(Request $request)
    {
        //频率限制


        //上传资讯图片文件
        $images_url = $this->imageService->uploadImages(Input::all(), 'information');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url,
        ];
    }

    public function createActivity(Request $request)
    {
        //频率限制


        //检验请求参数
        $rule = [
			'aid' => 'required',
            'token' => 'required',
            'title' => 'required',
            'content' => 'required',
            'start_time' => 'required|date',
            'end_time' => 'required|after:now|after:start_time',
            'place' => 'required',
            'img_url' => 'sometimes|required',
        ];
        $this->helpService->validateParameter($rule);

		$associationsDetails = $this->associationService->getAssociationsDetails($request->aid,$request->token);
		
		if(!$associationsDetails){
			throw new \App\Exceptions\Custom\OutputServerMessageException('社团不存在');
		}
        $param = [
			'aid' => $request->aid,
            'title' => $request->title,
            'content' => $request->content,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'place' => $request->place,
            'img_url' => $request->img_url,
        ];
        //创建社团活动
        $this->associationService->createActivity($param);
        
		
        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function uploadActivityImageFiles(Request $request)
    {
        //频率限制


        //上传活动图片文件
        $images_url = $this->imageService->uploadImages(Input::all(), 'activity');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'url' => $images_url,
        ];
    }

    public function setProfile(Request $request)
    {
        //检验请求参数
        $rule = [
            'introduction' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //修改社团信息
        $this->associationService->updateInfo(['introduction' => $request->introduction]);

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function manageMember(Request $request)
    {
        //检验请求参数
        $rule = [
            'mobile_no' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        //添加成员到社团，更新社团成员人数统计

    }

    public function getHotActivities(Request $request)
    {
        //获得社团活动列表
        $activities = $this->associationService->getHotActivities();

        //增加社团活动浏览量
        $this->associationService->incrementViewCount($activities, 'activity');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $activities
        ];
    }

    public function getHotInformation(Request $request)
    {
        //获得社团资讯列表
        $information = $this->associationService->getHotInformation();

        //增加社团资讯浏览量
        $this->associationService->incrementViewCount($information, 'information');

        return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $information
        ];
    }
    public function getAssociations(Request $request)
    {
		//检验请求参数
        $rule = [
            'page' => 'required',
        ];
        $this->helpService->validateParameter($rule);

	    $associations = $this->associationService->getAssociations($request->page);
		$association_sum = $this->associationService->getAssociationNum();
		$activity_sum = $this->associationService->getActivityNum();
	    return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => [
				'association_sum' => $association_sum,
				'activity_sum' => $activity_sum,
				'associations' => $associations,
			]
        ];
    }

	//获取社团详情
	public function getAssociationsDetails(Request $request)
    {
		//检验请求参数
        $rule = [
            'association_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);
	    $associationsDetails = $this->associationService->getAssociationsDetails($request->association_id,$request->token);
	    $checkNewMember = $this->associationService->checkNewMember($request->association_id);
	   $associationsDetails->newMember = $checkNewMember == 200 ? true : false;
	     return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $associationsDetails
        ];
    }

    public function getMyAssociations(Request $request)
    {
        $associations = $this->associationService->getMyAssociations();
         return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $associations
        ];
    }

	/* 获取社团公告 */
	public function getAssociationNotice(Request $request)
    {
        $association_notice = $this->associationService->getAssociationNotice($request->association_id);
         return [
            'code' => 200,
            'detail' => '请求成功',
        ];
    }

	/* 获取社团成员列表 */
	public function getAssociationMember(Request $request)
    {
		$rule = [
            'page' => 'required',
        ];
        $this->helpService->validateParameter($rule);
		
        $association_member = $this->associationService->getAssociationMember($request->association_id,$request->page);
         return [
            'code' => 200,
            'detail' => '请求成功',
            'data' => $association_member
        ];
    }

	/* 更改成员等级 */
	public function updateMemberLevel(Request $request)
    {
		$rule = [
			'token' => 'required',
            'level' => 'required',
			'association_id' => 'required',
        ];
        $this->helpService->validateParameter($rule);

        $updateMemberLevel = $this->associationService->updateMemberLevel($request->association_id,$request->level,$request->uid);
		if($updateMemberLevel == 200){
			return [
				'code' => 200,
				'detail' => '请求成功',
			];
		}elseif($updateMemberLevel == 401){
			return [
				'code' => 401,
				'detail' => '无权限操作',
			];
		}else{
			return [
				'code' => 403,
				'detail' => '请求失败',
			];
		}
        
    }

	/* 社团成员加入 */
	public function joinAssociationMember(Request $request)
    {
		//检验请求参数
        $rule = [
			'association_id' => 'required',
			'token' => 'required',
            'ar_username' => 'required',
			'profession' => 'required',
			'mobile_no' => 'required',
			'causes' => 'required'
        ];
        $this->helpService->validateParameter($rule);
		
        $joinAssociationMember = $this->associationReviewService->joinAssociationMember($request->association_id,$request->ar_username,$request->profession,$request->causes,$request->mobile_no);
		if($joinAssociationMember == 401){
			return [
				'code' => 401,
				'detail' => '已经是社团的成员',
			];
		}
		$admins = $this->associationService->getAssociationAdmins($request->association_id);		
		foreach( $admins as $key => $admin )
		{
			$this->messageService->SystemMessage2SingleOne($admin->uid, '新成员加入,快去审核吧', $push = false, $type = '社团信息', $name = '社团');
		}
        return [
            'code' => 200,
            'detail' => '请求成功',
        ];
    }

	/* 踢人 */
	public function deleteMember(Request $request)
    {
		$rule = [
			'token' => 'required',
			'association_id' => 'required',
			'uid' => 'required'
        ];
        $this->helpService->validateParameter($rule);

        $deleteMember = $this->associationService->deleteMember($request->association_id,$request->uid);
		if($deleteMember == 200){
			return [
				'code' => 200,
				'detail' => '请求成功',
			];
		}elseif($deleteMember == 401){
			return [
				'code' => 401,
				'detail' => '无权限操作',
			];
		}else{
			return [
				'code' => 403,
				'detail' => '请求失败',
			];
		}
        
    }

	/* 发布公告 */
	public function releaseNotice(Request $request){
		$rule = [
			'token' => 'required',
			'association_id' => 'required',
			'notice' => 'required'
        ];
        $this->helpService->validateParameter($rule);
        
		$associationsDetails = $this->associationService->getAssociationsDetails($request->association_id,$request->token);
		
		if(!$associationsDetails){
			throw new \App\Exceptions\Custom\OutputServerMessageException('社团不存在');
		}
		
		$releaseNotice = $this->associationService->releaseNotice($request->association_id,$request->notice);
	
		$association_members = $this->associationService->getAssociationAllMemberUids($request->association_id);
		
		foreach( $association_members as $key => $association_member )
		{
			$name = $associationsDetails->aname.'社团公告';
			$this->messageService->SystemMessage2SingleOne($association_member->uid, $request->notice,true,'社团公告',$name);
        	$this->pushService->PushUserTokenDevice($name, $request->notice, $association_member->uid);
		}

		throw new \App\Exceptions\Custom\RequestSuccessException();
        
	}

	/* 检查是否有新公告 */
	public function checkNewNotice(Request $request){
		$rule = [
			'token' => 'required',
			'association_id' => 'required'
        ];
        $this->helpService->validateParameter($rule);

		$checkNewNotice = $this->associationService->checkNewNotice($request->association_id);
		$checkNewMember = $this->associationService->checkNewMember($request->association_id);
		if($checkNewMember == 400 || $checkNewNotice == 400){
			return [
				'code' => 400,
				'detail' => '不是管理员',
			];
		}else if($checkNewMember == 401 || $checkNewNotice == 401){
			return [
				'code' => 401,
				'detail' => '还没加入社团',
			];
		}
		
		if($checkNewNotice == 200 && $checkNewMember == 200){
			return [
				'code' => 200,
				'detail' => '有新公告和新待审核成员',
			];
		}else if($checkNewNotice == 200){
			return [
				'code' => 201,
				'detail' => '有新公告',
			];
		}else if($checkNewMember == 200){
			return [
				'code' => 202,
				'detail' => '有新待审核成员',
			];
		}else{
			return [
				'code' => 304,
				'detail' => '无更新数据',
			];
		}

	}

	/* 退出社团 */
	public function quitAssociation(Request $request){
		$rule = [
			'token' => 'required',
			'association_id' => 'required'
		];
		$this->helpService->validateParameter($rule);
		$quitAssociation = $this->associationService->quitAssociation($request->association_id);
		if($quitAssociation == 401){
			return [
				'code' => 401,
				'detail' => '不是社团的成员',
			];
		}else if($quitAssociation == 402){
			return [
				'code' => 402,
				'detail' => '社长不能退出',
			];
		}
        return [
            'code' => 200,
            'detail' => '请求成功',
        ];
	}

	/* 获取全部审核成员列表 */
	public function checkMemberList(Request $request){
		$rule = [
			'token' => 'required',
			'association_id' => 'required'
		];
		$this->helpService->validateParameter($rule);
		$checkMemberList = $this->associationService->checkMemberList($request->association_id);
        return [
            'code' => 200,
            'data' => $checkMemberList,
        ];
	}

	/* 成员审核 */
	public function checkMember(Request $request){
		$rule = [
			'token' => 'required',
			'association_id' => 'required',
			'uid' => 'required',
			'status' => 'required'
		];
		$this->helpService->validateParameter($rule);

		$checkMember = $this->associationService->checkMember($request->association_id,$request->uid,$request->status);
		if($checkMember == 304){
			return [
				'code' => 304,
				'detail' => '该用户已经在社团中',
			];
		}
        return [
            'code' => 200,
            'detail' => '请求成功',
        ];

	}
	
	public function deleteActivity(Request $request){
		$rule = [
			'token' => 'required',
			'actid' => 'required',
			'aid' => 'required',
		];
		$this->helpService->validateParameter($rule);
		
		$deleteActivity = $this->associationService->deleteActivity($request->actid,$request->aid);
		if($deleteActivity == 200){
			return [
				'code' => 200,
				'detail' => '请求成功',
			];
		}elseif($deleteActivity == 401){
			return [
				'code' => 401,
				'detail' => '无权限操作',
			];
		}else{
			return [
				'code' => 403,
				'detail' => '请求失败',
			];
		}
	}

}
