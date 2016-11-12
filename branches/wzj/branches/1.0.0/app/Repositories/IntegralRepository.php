<?php 
namespace App\Repositories;

use Illuminate\Http\Request;
use App\Level;
use App\Integral_history;

class IntegralRepository{

	public function check_level($integral)
	{
        $level = Level::get();
        for ($i=1; $i <= count($level)+1; $i++) { 
            $L[$i] = Level::where('id',$i)->first();
        }

		if($integral >= $L[1]->integral && $integral < $L[2]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[2]->integral - $integral,
                    'level' => $L[1]->level
                ] 
            ];
        }elseif($integral >= $L[2]->integral && $integral < $L[3]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[3]->integral - $integral,
                    'level' => $L[2]->level
                ] 
            ];
        }elseif($integral >= $L[3]->integral && $integral < $L[4]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[4]->integral - $integral,
                    'level' => $L[3]->level
                ] 
            ];
        }elseif($integral >= $L[4]->integral && $integral < $L[5]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[5]->integral - $integral,
                    'level' => $L[4]->level
                ] 
            ];
        }elseif($integral >= $L[5]->integral && $integral < $L[6]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[6]->integral - $integral,
                    'level' => $L[5]->level
                ] 
            ];
        }elseif($integral >= $L[6]->integral && $integral < $L[7]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[7]->integral - $integral,
                    'level' => $L[6]->level
                ] 
            ];
        }elseif($integral >= $L[7]->integral && $integral < $L[8]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[8]->integral - $integral,
                    'level' => $L[7]->level
                ] 
            ];
        }elseif($integral >= $L[8]->integral && $integral < $L[9]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[9]->integral - $integral,
                    'level' => $L[8]->level
                ] 
            ];
        }elseif($integral >= $L[9]->integral && $integral < $L[10]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[10]->integral - $integral,
                    'level' => $L[9]->level
                ] 
            ];
        }elseif($integral >= $L[10]->integral && $integral < $L[11]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => $L[11]->integral - $integral,
                    'level' => $L[10]->level
                ] 
            ];
        }elseif($integral >= $L[11]->integral){
            return [
                'code' => 200,
                'data' => [
                    'integral' => $integral,
                    'upgrade' => "top",
                    'level' => "Lv10"
                ] 
            ];
        }
	}

    public function add_history($uid,$integral_id){
        $integral_history = new Integral_history;
        $integral_history->setConnection('write');
        $integral_history->uid = $uid;
        $integral_history->integral_id = $integral_id;
        $integral_history->save();
    }
}