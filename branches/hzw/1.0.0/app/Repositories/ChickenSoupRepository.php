<?php

namespace App\Repositories;

use DB;
use App\ChickenSoup;
use Illuminate\Http\Request;

class ChickenSoupRepository
{
	public function chickenSoupList($page){
		return ChickenSoup::select(DB::raw("csid,chicken_soup.uid,if(user.nickname IS NOT NULL,user.nickname,'系统') as nickname ,title,background_url,view_num,chicken_soup.created_at"))
					->leftjoin('user','chicken_soup.uid','=','user.uid')
					->whereNull('deleted_at')
					->where('status',1)
					->orderBy('csid','desc')
					->orderBy('view_num','desc')
					->skip(10 * $page - 10)
				    ->take(10)
				    ->get();
	}
	
	public function chickenSoupDetail($csid){
		$chickenSoup = ChickenSoup::select(DB::raw("csid,chicken_soup.uid,if(user.nickname IS NOT NULL,user.nickname,'系统') as nickname ,title,content,view_num,chicken_soup.created_at"))
					->leftjoin('user','chicken_soup.uid','=','user.uid')
					->whereNull('deleted_at')
					->where('status',1)
					->where('csid',$csid)
					->first();
		if($chickenSoup){
			ChickenSoup::whereNull('deleted_at')
					->where('status',1)
					->where('csid',$csid)
					->update(['view_num'=>$chickenSoup->view_num+1]);
		}
		return $chickenSoup;
	}
}