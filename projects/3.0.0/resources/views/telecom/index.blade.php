<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <!-- 引入样式 -->
  <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
  <link rel="stylesheet" href="{{ asset('/telecom/css/reset.css') }}">
  <link rel="stylesheet" href="{{ asset('/telecom/css/style.css') }}">
</head>
<body >
  <div id="app" style="height:100%">
  	<header>
		<!-- 导航 S-->
		  <el-menu default-active="1" class="el-menu-demo" mode="horizontal" @select="" height="80px">
		  <!-- <el-menu-item index="1">首页</el-menu-item> -->
		  <el-submenu index="2">
		    <template slot="title">@{{userInfo.nickname}}</template>
		    <el-menu-item index="2-1"><a href="{{ url('telecomAdmin/logout') }}">退出</a></el-menu-item>
		  </el-submenu>
		  <el-menu-item index="4"><div class="timg"><img :src="userInfo.avatar_url" alt=""></div></el-menu-item>
		</el-menu>
	  	<!-- 导航 E-->
  	</header>
  	<el-col :span="4" class="cNav">
  		<a-logo></a-logo>

	    <el-menu  theme="dark" unique-opened :default-active="defaultActive" class="el-menu-vertical-demo" >
	   		 <el-menu-item index="1"><router-link to="home"><i class="el-icon-view"></i>首页</router-link></el-menu-item>
	      <el-menu-item index="2"><router-link to="order"><i class="el-icon-document"></i>预约订单</router-link></el-menu-item>
	      <!-- <el-submenu index="3">
	        <template slot="title"><i class="el-icon-picture"></i>钱包</template>
	        <el-menu-item-group>
		        <el-menu-item index="3-2" ><router-link to="classify">钱包记录</router-link></el-menu-item>
	        </el-menu-item-group>
	      </el-submenu> -->
	      <el-menu-item index="3"><router-link to="setting"><i class="el-icon-setting"></i>预约人数设置</router-link></el-menu-item>
	    </el-menu>

	 </el-col>
  	<div id="main">
  		<div class="snav">
  			<el-breadcrumb separator="/">
	  		  <el-breadcrumb-item v-for="snav in snavs" :key="snav.id" :to="{ path: snav.path }" >@{{snav.value}}</el-breadcrumb-item>

			</el-breadcrumb>
  		</div>
  		<router-view></router-view>
  	</div>
  	<footer>

  		<div class="copy">飞步信息科技有限公司 技术支持</div>
  	</footer>
  </div>
  	<!-- 商品列表 template-->
	<template id="table">
		<div class="productTable">
		 <div class="block">
		 	<div class="table_header">
			    <el-date-picker
			      v-model="dateVal"
			      type="date"
			      placeholder="选择日期"
			      format="yyyy-MM-dd"
			      clearable='true'
			      @change="ssHandleChange">
			    </el-date-picker>
			    <el-cascader
			    	placeholder="选择宿舍"
				    expand-trigger="hover"
				    :options="ssOptions"
				    v-model="ssSelectedOptions"
				    @change="ssHandleChange">
				  </el-cascader>
				 <el-input
				  placeholder="请输入姓名或者手机号码搜索"
				  icon="search"
				  v-model="input2"
				  :on-icon-click="handleIconClick">
				 </el-input>
				 <el-button type="primary" @click="resetForm">重置</el-button>
				 <el-button type="primary" @click="exportForm" style="float: right;">导出订单</el-button>
			 </div>
			 <div class="table_header2">
				<p>预约总人数：@{{productTablePage}}</p>
			 </div>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="tableData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="100">
		    </el-table-column>
		    <el-table-column
		      prop="name"
		      label="姓名"
		      width="180">
		    </el-table-column>
		    <el-table-column
		      prop="mobile_no"
		      label="手机号码"
		      width="180">
		    </el-table-column>
		    <el-table-column
		      prop="building_name"
		      label="宿舍楼"
		      width="180"
		      >
		    </el-table-column>
		     <el-table-column
		      prop="dormitory_number"
		      label="宿舍号"
		      width="180">
		    </el-table-column>
		    <el-table-column
		      prop="created_at"
		      label="预约时间"
		      width="">
		    </el-table-column>
		   <!--  <el-table-column
		      prop="is_on_sale_name"
		      label="状态"
		      width="100"
		      :filters="state"
		      :filter-method="filterState">
		       <template scope="scope">
		        <el-tag
		          :type="scope.row.is_on_sale_name === '上架' ? 'primary' : 'success'"
		          close-transition>@{{scope.row.is_on_sale_name}}</el-tag>
		      </template>
		    </el-table-column>
	     	<el-table-column label="操作"  width="">
		      <template scope="scope">
		        <el-button
		          size="small"
		          @click="PThandleEdit(scope.$index, scope.row)">修改</el-button>
		        <el-button
		          size="small"
		          type="danger"
		          modal="true"
		          @click="PThandleDelete(scope.$index, scope.row)">删除</el-button>
		      </template>
		    </el-table-column> -->
		  </el-table>

		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="productTablePage" current-page="1"  :current-page="1" :page-size="20">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 商品列表 template-->

	<!-- 店铺设置 -->
	<template id="setting">
		 <el-form :model="numberData"  ref="numberData" label-width="100px" class="ruleForm" v-loading.body="loading" element-loading-text="正在获取信息..." style="margin-top: 30px;">
		  <el-form-item v-for="item in numberData" :label="item.campus_name" >
		    <el-input  v-model="item.count" auto-complete="off" :name="item.setting_id" @focus="ssHandleFocus(item.count)"  @blur="ssHandleChange(item.setting_id,item.count)"></el-input>
		  </el-form-item>
		  <el-form-item>
		    <el-button type="primary" style="display: none;" @click="submitForm('numberData')">确定修改</el-button>
		  </el-form-item>
		</el-form>

	</template>
	<!-- 店铺设置 -->
	<!-- home -->
	<template id="home">
		 <div class="home">
			<div class="home-item" style="background: #1D8CE0">
				<span>@{{count}}</span>
				<p>总预约数</p>
			</div>
			<div class="home-item" style="background: #20A0FF">
				<span>@{{count_yk}}</span>
				<p>粤垦总预约数</p>
			</div>
			<div class="home-item" style="background: #58B7FF">
				<span>@{{count_zc}}</span>
				<p>增城总预约数</p>
			</div>
			<div class="home-item" style="background: #1D8CE0">
				<span>@{{today_count}}</span>
				<p>今天预约数</p>
			</div>
			<div class="home-item" style="background: #20A0FF">
				<span>@{{today_count_yk}}</span>
				<p>今日粤垦预约数</p>
			</div>
			<div class="home-item" style="background:#58B7FF">
				<span>@{{today_count_zc}}</span>
				<p>今日增城预约数</p>
			</div>
		 </div>

	</template>
	<!-- home -->
</body>
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
	    		//路由变化
		       fetchData:function () {
		            this.snavs = this.$route.meta.msg;
		            this.defaultActive =  this.$route.meta.index
		        },
		        //获取个人资料
		        getUser:function(){
		        	var that = this;
		        	$.getJSON(this.localhost+'telecom/getUser',function(data){
					          		that.userInfo = {
					          			"nickname":data.user.nickname,
					          			"avatar_url":data.user.avatar_url,
					          		}
				          }).error(function(){
				            that.$message.error('服务器开小差了');
				          })
		        },
		        exit:function(){
		        	var that = this;
		        	$.getJSON(this.localhost+'telecomAdmin/logout',function(data){
				          if(data.code == 200){
				          	window.location.href="login.html"
				          }
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
		        }

		  },
		  created:function(){
		  	//获取列表
		  	this.fetchData();
		  	this.getUser();
		  },
		    watch: {
		        '$route': 'fetchData'
		    },
		 data:function() {
	        return {
	          snavs:[{"value":"首页","path":""},{"value":"","path":""}],
		  	  defaultActive:"",
		  	  userInfo:{
		  	  	"nickname":"",
      			"avatar_url":"",
		  	  }
	        }
	      }
	  }
	var Ctor = Vue.extend(Main);
	new Ctor({router}).$mount('#app');

  </script>
</html>
