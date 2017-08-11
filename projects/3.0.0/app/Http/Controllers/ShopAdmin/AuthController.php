<?php
namespace App\Http\Controllers\ShopAdmin;

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
    protected $redirectTo = '/business';
    protected $guard = 'business';
    protected $loginView = 'business.login';
    protected $registerView = 'business.register';
    protected $redirectPath =  '/business';

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
          return Redirect::to('business/login');
     }
    protected function create(array $data)
    {


    }

}
