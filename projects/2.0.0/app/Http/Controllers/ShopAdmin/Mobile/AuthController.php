<?php
namespace App\Http\Controllers\ShopAdmin\Mobile;

use App\ShopAdmin;
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
    protected $redirectTo = '/mbusiness';
    protected $guard = 'business';
    protected $loginView = 'business.mobile.login';
    protected $registerView = 'business.mobile.register';

    public function __construct()
    {
        $this->middleware('business:business', ['except' => ['getLogout','getLogin','postLogin']]);
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
        if(Auth::guard('business')->user()){
            Auth::guard('business')->logout();
        }
          return Redirect::to('mbusiness/login');
     }
    protected function create(array $data)
    {


    }

}
