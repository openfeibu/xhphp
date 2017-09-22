<?php
namespace App\Http\Controllers\TelecomAdmin;

use Input;
use Validator;
use Auth;
use View;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

	protected $username = 'mobile_no';
    protected $redirectTo = '/telecomAdmin';
    protected $guard = 'telecom';
    protected $loginView = 'telecom.login';
    protected $registerView = 'telecom.register';
    protected $redirectPath =  '/telecomAdmin';

    public function __construct()
    {
        $this->middleware('telecom:telecom', ['except' => ['getLogout','getLogin','postLogin']]);
    }

    protected function validator(array $data)
    {

        return Validator::make($data, [
            'mobile_no' => 'required|mobile_no|max:255|unique:user',
            'password' => 'required|confirmed|min:6',
        ]);

    }
	  // 登出
    public function getLogout()
    {
        if(Auth::guard('telecom')->user()){
            Auth::guard('telecom')->logout();
        }
        return Redirect::to('telecomAdmin/login');
    }
    public function getLogin()
    {
        return view('telecom.login');
    }
    protected function create(array $data)
    {


    }

}
