<?php

namespace App\Repositories;

use DB;
use App\AssociationReview;
use App\AssociationMember;
use Illuminate\Http\Request;

class AssociationReviewRepository
{

	protected $request;
	
	function __construct(Request $request)
	{
		$this->request = $request;
		
	}

	/**
	 * 根据用户ID查找其处于$statuc状态的社团入驻申请
	 */
	public function getJoinRequestWhitStatus($user_id, $status)
	{
		return AssociationReview::select(DB::raw('aid, aname, causes, created_at'))
								->where('uid', $user_id)
								->whereIn('status', is_array($status) ? $status : [$status])
								->get();
	}

	/**
	 * 创建社团入驻申请
	 */
	public function createJoinRequest($param)
	{
		$review = new AssociationReview;
		$review->setConnection('write');
		$review->uid = $param['uid'];
		$review->aname = $param['name'];
		$review->avatar_url = $param['avatar_url'];
		$review->certificate = $param['certificate'];
		$review->causes = $param['causes'];
		$review->save();
	}
	
	public function joinAssociationMember($aid,$uid,$ar_username,$profession,$causes,$mobile_no){
		$associationMember = AssociationMember::select(DB::raw('aid'))
											->where('aid',$aid)
											->where('uid',$uid)
											->whereNull('deleted_at')
											->first();
		if($associationMember){
			return 401;
		}
		$associationReview = AssociationReview::select(DB::raw('id'))
											->where('aid',$aid)
											->where('uid',$uid)
											->where('status',"checking")
											->whereNull('deleted_at')
											->first();
		if($associationReview){
			$associationReview->deleted_at = date("Y-m-d H:i:s");
			$associationReview->save();
		}
		$review = new AssociationReview;
		$review->setConnection('write');
		$review->aid = $aid;
		$review->uid = $uid;
		$review->ar_username = $ar_username;
		$review->profession = $profession;
		$review->mobile_no = $mobile_no;
		$review->causes = $causes;
		$review->save();
	}
}