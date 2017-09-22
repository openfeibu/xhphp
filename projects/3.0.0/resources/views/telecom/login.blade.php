<!DOCTYPE html>
<html style="height: 100%;">
<head>
  <meta charset="UTF-8">
  <!-- 引入样式 -->
  <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
  <link rel="stylesheet" href="{{ asset('/telecom/css/reset.css') }}">
  <link rel="stylesheet" href="{{ asset('/telecom/css/style.css') }}">
</head>
<body class="loginBg">
  <div id="app" style="height:100%">
	<el-form :model="login" :rules="rules" ref="login" label-width="90px" class="login" action="{{ url('telecomAdmin/login') }}" method="post">
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
    <!-- 先引入 Vue -->
  <script src="{{ asset('/telecom/js/jquery2.1.1.min.js') }}"></script>
  <script src="{{ asset('/telecom/js/vue.min.js') }}"></script>
  <script src="{{ asset('/telecom/js/vue-router.js') }}"></script>
  <!-- 引入组件库 -->
  <script src="{{ asset('/telecom/js/element.js') }}"></script>
  <script src="{{ asset('/telecom/js/vue-template.js') }}"></script>
  <script src="{{ asset('/telecom/js/routes.js') }}"></script>
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
		          callback(new Error('请输入账号'));
		        }else {
		          callback();
		        }
		      };
		      var validateP = (rule, value, callback) => {
		        if (value === '') {
		          callback(new Error('请输入密码'));
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
	new Ctor({router}).$mount('#app');

  </script>
</html>
