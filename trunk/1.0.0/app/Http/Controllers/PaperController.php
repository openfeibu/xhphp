<?php

namespace App\Http\Controllers;

use App\Paper;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HelpService;

class PaperController extends Controller
{

    protected $help;

    function __construct(HelpService $help)
    {
        $this->help = $help;
        $rule = [

            'id' => 'required',
        ];
        $help->validateParameter($rule);
    }

    public function index(Request $request){
        $paper = Paper::where('id',$request->id)->first();
        return [
            'code' => 200,
            'data' => $paper->papers
        ];
    }

}
