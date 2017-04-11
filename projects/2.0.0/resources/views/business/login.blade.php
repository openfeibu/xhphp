@extends('layouts.common')

@section('content')
<body class="loginBg">
  <div id="app" style="height:100%">
	<el-form :model="login" :rules="rules" ref="login" label-width="90px" class="login" action="{{ url('business/login') }}" method="post">
	  <div class="logo"></div>
	  <el-form-item label="账号" prop="mobile_no">
	    <el-input  v-model="login.mobile_no" auto-complete="off" value="{{ old('mobile_no') }}" name="mobile_no"></el-input>
	    @if ($errors->has('mobile_no'))
	        <div class="el-form-item__error">
	            <strong>{{ $errors->first('mobile_no') }}</strong>
	        </div>
	  	@endif
	  </el-form-item>

	  <el-form-item label="密码" prop="password">
	    <el-input type="password" v-model="login.password" auto-complete="off" name="password"></el-input>
	    @if ($errors->has('password'))
	        <div class="el-form-item__error">
	            <strong>{{ $errors->first('password') }}</strong>
	        </div>
      	@endif
	  </el-form-item>

	  <el-form-item>
	    <el-button type="primary" @click="submit('login')">登录</el-button>
	  </el-form-item>
	</el-form>
  </div>
</body >

  <script>

  	var Main = {
	    methods: {
		     submit:function(formName){
		     	this.$refs[formName].validate((valid) => {
		          if (valid) {
			  		$(".login").submit();
		          } else {
				    this.$message.error('账号密码不可为空');
		            return false;
		          }
		        });
		     }

		  },
		  created:function(){
		  	//获取列表

		  },
		 data:function() {
		 	 var validateA = (rule, value, callback) => {
		        if (value === '') {
		          callback(new Error('请输入校汇账号'));
		        }else {
		          callback();
		        }
		      };
		      var validateP = (rule, value, callback) => {
		        if (value === '') {
		          callback(new Error('请输入校汇密码'));
		        }else {
		          callback();
		        }
		      };
	        return {
	          login:{
	          	"mobile_no":"",
	          	"password":""
	          },
	           rules: {
			          mobile_no: [
			            { validator: validateA, trigger: 'blur' }
			          ],
			          password: [
			            { validator: validateP, trigger: 'blur' }
			          ],
			        }
	        }
	      }
	  }
	var Ctor = Vue.extend(Main);
	new Ctor({router:router}).$mount('#app');

  </script>
@stop
