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

    }

    public function index(Request $request){
        $rule = [

            'id' => 'required',
        ];
        $help->validateParameter($rule);
        $paper = Paper::where('id',$request->id)->first();
        return [
            'code' => 200,
            'data' => $paper->papers
        ];
    }
    public function getAnnouncement(Request $request)
    {
        $paper = Paper::where('name','announcement')->first();
        return [
            'code' => 200,
            'data' => $paper,
        ];
    }
}
