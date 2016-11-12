<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\User;
use App\Integral_history;
use App\Integral;
use App\Level;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\HelpService;
use App\Repositories\IntegralRepository;
use Illuminate\Support\Facades\Event;
use App\Events\Integral\Integrals;

class IntegralController extends Controller
{
	protected $help;
    protected $user;

	function __construct(HelpService $help,Request $request)
    {
        $user = User::where('token',$request->token)->first();
        if(!$user){
            throw new \App\Exceptions\Custom\UserUnauthorizedException();
        }
        $this->user = $user;
        $this->help = $help;
        $rule = [
            'token' => 'required',
        ];
        $help->validateParameter($rule);
    }

    public function integral_list(Request $request){
        $rule = [
            'page' => 'required',
        ];
        $this->help->validateParameter($rule);
        $data = DB::table('integral_history')
    	        	->where('uid',$this->user->uid)
    	            ->join('integral', function($join){$join
                    ->on('integral_history.integral_id', '=', 'integral.id');})
                    ->select('integral_history.updated_at', 'integral.obtain_type','integral.score')
                    ->get(); 
        $data = array_reverse($data);
        $page_count = ceil(count($data)/20);
        $begin = ($request->page-1)*20;

        if($request->page < $page_count){
            for ($i = $begin; $i < $request->page*20; $i++) { 
                $data2[] = $data[$i];
            }
        }elseif($request->page == $page_count){
            $all = count($data)-($request->page-1)*20;
            for ($i = $begin; $i < $begin+$all; $i++) { 
                $data2[] = $data[$i];
            }   
        }else{
            return ;
        }
        return [
            'code' => 200,
            'data' => $data2?:"",
        ];
    }

    public function integral_level(Request $request){
        $integralRepository = new IntegralRepository;
        return $integralRepository->check_level($this->user->integral);
    }

    public function integral_explain(Request $request){
        $level = Level::select('level','integral')->get();
        $integral = Integral::select('obtain_type','score')->orderBy('id', 'asc')->get();
        return [
            'code' => 200,
            'data' => [
                'level_explain' => $level,
                'integral_explain' => $integral,
            ]     
        ];
    }

    public function integral_share(Request $request){
        if(Event::fire(new Integrals("推荐给好友"))){
            throw new \App\Exceptions\Custom\RequestSuccessException();
        }else{
            throw new \App\Exceptions\Custom\RequestFailedException();
        } 
    }
}
