<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\HelpService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
	protected $help;
    protected $userRepository;

	function __construct(HelpService $help,UserService $userService)
    {
	    parent::__construct();
	    $this->middleware('auth', ['except' => 'feedback']);

        $this->help = $help;
        $this->userService = $userService;
    }

    public function feedback(Request $request){
		$rule = [
            'token' => 'sometimes|string',
            'content' => 'required',
            'contact_way' => 'sometimes|string',
        ];
        $this->help->validateParameter($rule);

        $user = $this->userService->getUserByToken();

        $feedback = new Feedback;
        $feedback->uid = isset($user->uid) ? $user->uid : 0;
        $feedback->content = $request->content;
        $feedback->contact_way = isset($request->contact_way) ?  $request->contact_way  : '';
        $feedback->save();

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
}
