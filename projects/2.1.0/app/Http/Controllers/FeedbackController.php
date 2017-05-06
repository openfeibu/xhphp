<?php

namespace App\Http\Controllers;

use App\Feedback;
use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use App\Services\HelpService;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
	protected $help;
    protected $userRepository;

	function __construct(HelpService $help,UserRepository $userRepository)
    {
	    parent::__construct();
	    $this->middleware('auth', ['except' => 'feedback']);
        $rule = [
            'token' => 'sometimes|required',
            'content' => 'required',
            'contact_way' => 'required',
        ];
        $help->validateParameter($rule);
        $this->help = $help;
        $this->userRepository = $userRepository;
    }

    public function feedback(Request $request){
        $user = $this->userRepository->getUser();

        $feedback = new Feedback;
        $feedback->uid = isset($user->uid) ? $user->uid : 0;
        $feedback->content = $request->content;
        $feedback->contact_way = $request->contact_way;
        $feedback->save();

        throw new \App\Exceptions\Custom\RequestSuccessException();
    }
}
