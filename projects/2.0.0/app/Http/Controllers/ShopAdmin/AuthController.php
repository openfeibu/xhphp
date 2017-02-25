<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Validator;
use App\Http\Requests;
use Auth;
use Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    public function __construct()
    {
        $this->middleware('guest:business', ['except' => 'logout']);
    }
     //登录
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            $validator = $this->validateLogin($request->input());
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
            if (Auth::guard('business')->attempt(['mobile'=>$request->mobile, 'password'=>$request->password])) {
                return Redirect::to('business')->with('success', '登录成功！'); 
            } else {
                return back()->with('error', '账号或密码错误')->withInput();
            }
        }
        return view('business.login');
    }
    //登录页面验证
    protected function validateLogin(array $data)
    {
        return Validator::make($data, [
            'mobile' => 'required|alpha_num',
            'password' => 'required',
        ], [
            'required' => ':attribute 为必填项',
            'min' => ':attribute 长度不符合要求'
        ], [
            'mobile' => '账号',
            'password' => '密码'
        ]);
    }

    //退出登录
    public function logout()
    {
        if(Auth::guard('business')->user()){
            Auth::guard('business')->logout();
        }
        return Redirect::to('business/login');
    }
}