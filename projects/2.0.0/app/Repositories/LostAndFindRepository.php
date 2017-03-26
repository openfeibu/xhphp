<?php

namespace App\Repositories;

use Illuminate\Http\Request;

use DB;
use Session;
use App\Loss;
use App\LossCategory;
use Illuminate\Http\Requests;

class LostAndFindRepository
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}
    public function create($data)
    {
        config(['database.default' => 'write']);
        return Loss::create($data);
        config(['database.default' => 'read']);
    }
    public function getCats()
    {
        return LossCategory::orderBy('sort','asc')->orderBy('cat_id','asc')->get();
    }
    public function getList($where)
    {
        return Loss::select(DB::raw('loss.college_id,loss.uid,loss.content,loss.mobile,loss.type,loss.img,loss.thumb,loss.created_at,loss.loss_id,loss_category.cat_name,loss_category.cat_id,user.nickname,user.avatar_url'))
                           ->where($where)
                           ->rightJoin('user','user.uid','=','loss.uid')
                           ->rightJoin('loss_category','loss_category.cat_id','=','loss.cat_id')
                           ->skip(20 * $this->request->page - 20)
                           ->take(20)
                           ->get();
    }
		public function delete($where)
		{
				return Loss::where($where)->delete();
		}
}
