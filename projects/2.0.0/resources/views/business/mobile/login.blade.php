@extends('layouts.mobile_business.common')

@section('content')
<body style="height:100%;" >
	<div id="app" v-cloak>
		<section class='main' style="height:100%;" >
			<!--登陆 S-->
			<div class="login" style="height:100%;">
				<h1 class="logo" ><img src="http://web.feibu.info/images/loginReg/icon_login_headpor.png" alt="校汇"></h1>
				<div class="outer-main">
					<form method="POST" class="login_form" @submit="return login()"  action="{{ url('mbusiness/login') }}">
						<input type="text" id="account" placeholder="手机号" v-model="loginData.account" value="{{ old('mobile_no') }}" name="mobile_no"><br>
						<input type="password" id="password" placeholder="密码" v-model="loginData.password" name="password"><br>
						<input type="submit" value="登录"  class="opa_active">
					</form>
				</div>
				@if ($errors->has('mobile_no'))
				   <div class="el-form-item__error">
					   <strong>{{ $errors->first('mobile_no') }}</strong>
				   </div>
			   @endif
			   @if ($errors->has('password'))
	   	        <div class="el-form-item__error">
	   	            <strong>{{ $errors->first('password') }}</strong>
	   	        </div>
	           @endif
			</div>
			<!--登陆 E-->
		</section>
	</div>
</body>
 <script>
    var Main = {
      methods: {
      	login:function(){
      		var that = this;
      		that.$indicator.open();     	
      	}
      },
      created:function(){

      },
      data:function(){
      	return{
      		loginData:{
      			account:window.localStorage.account || "",
      			password:"",
      		}
      	}
      }
    }
  var Ctor = Vue.extend(Main);
  new Ctor({router:router}).$mount('#app');
  </script>
@stop
