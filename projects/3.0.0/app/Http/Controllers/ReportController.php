<?php

namespace App\Http\Controllers;

use App\Accusation;
use App\Order;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\HelpService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
	protected $help;

    // protected $userService;

	function __construct(HelpService $help)
    {
	    parent::__construct();
        $this->middleware('auth', ['except' => ['reportCrash']]);

        $this->help = $help;

        // $this->userService = $userService;
    }

    public function report(Request $request, UserService $userService){

        $rule = [
            'oid' => 'required',
            'content' => 'required',
            'type' => 'sometimes|required',
        ];
        $this->help->validateParameter($rule);

        $user = $userService->getUser();

        $accusation = new  Accusation;
        $accusation->setConnection('write');
        $accusation->oid = $request->oid;
        $accusation->complainant_id = $user->uid;
        $accusation->content = $request->content;
        $accusation->type = $request->type?:'';
        $accusation->save();

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

    public function reportCrash(Request $request)
    {
        if (isset($request->token) and $request->token != 0) {
            $uid = \App\User::where('token', $request->token)->first()->uid;
        } else {
            $uid = 0;
        }
        $crash_log = new \App\CrashLog;
        $crash_log->uid = $uid;
        $crash_log->log = $request->log;
        $crash_log->save();

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }

}
