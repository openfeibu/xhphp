@extends('layouts.common')

@section('content')
<body >
  <div id="app" style="height:100%">
  	<header>
		<!-- 导航 S-->
		  <el-menu default-active="1" class="el-menu-demo" mode="horizontal" @select="" height="80px">
		  <!-- <el-menu-item index="1">首页</el-menu-item> -->
		  <el-submenu index="2">
		    <template slot="title">@{{userInfo.nickname}}</template>
		    <el-menu-item index="2-1"><a href="{{ url('business/logout') }}">退出</a></el-menu-item>
		  </el-submenu>
		  <el-menu-item index="4"><div class="timg"><img :src="userInfo.avatar_url" alt=""></div></el-menu-item>
		</el-menu>
	  	<!-- 导航 E-->
  	</header>
  	<el-col :span="4" class="cNav">
  		<a-logo></a-logo>
	    <el-menu  theme="dark" unique-opened :default-active="defaultActive" class="el-menu-vertical-demo" >
	     <el-submenu index="1">
	        <template slot="title"><i class="el-icon-document"></i>订单管理</template>
	        <el-menu-item-group>
		        <el-menu-item index="1-1" ><router-link to="notShipped">未发货</router-link></el-menu-item>
		        <el-menu-item index="1-2" ><router-link to="shipped">已发货</router-link></el-menu-item>
		        <el-menu-item index="1-3" ><router-link to="succ">已完成</router-link></el-menu-item>
		        <el-menu-item index="1-4" ><router-link to="cancell">退货与售后</router-link></el-menu-item>
	        </el-menu-item-group>
	      </el-submenu>
	      <el-submenu index="2">
	        <template slot="title"><i class="el-icon-picture"></i>商品管理</template>
	        <el-menu-item-group>
		        <el-menu-item index="2-1" ><router-link to="table">商品列表</router-link></el-menu-item>
		        <el-menu-item index="2-2" ><router-link to="classify">分类管理</router-link></el-menu-item>
		        
	        </el-menu-item-group>
	      </el-submenu>
	      <!-- <el-submenu index="3">
	        <template slot="title"><i class="el-icon-picture"></i>钱包</template>
	        <el-menu-item-group>
		        <el-menu-item index="3-2" ><router-link to="classify">钱包记录</router-link></el-menu-item>
	        </el-menu-item-group>
	      </el-submenu> -->
	      <el-menu-item index="4"><router-link to="setting"><i class="el-icon-setting"></i>店铺设置</router-link></el-menu-item>
	    </el-menu>
	    <div class="c-userInfo">
			<p>钱包：<span>@{{userInfo.wallet}}</span>元</p>
			<p>店铺销售量：<span>@{{userInfo.sale_count}}</span>单</p>
			<p>店铺总收入：<span>@{{userInfo.income}}</span>元</p>
	    	
	    </div>
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
  			
  		<div class="copy">飞步信息科技有限公司 版权所有</div>
  	</footer>
  </div>
  	<!-- 商品列表 template-->
	<template id="table">
		<div class="productTable">
		 <el-input
		  placeholder="请输入搜索商品名称"
		  icon="search"
		  v-model="input2"
		  :on-icon-click="handleIconClick">
		 </el-input>
		 <el-button type="primary" style="float:right;margin:8px 0 0 0" @click="showAdd">上传商品<i class="el-icon-upload el-icon--right"></i></el-button>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="tableData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="50">
		    </el-table-column>
		    <el-table-column
		      prop="goods_name"
		      label="商品名称"
		      width="120">
		    </el-table-column>
		    <el-table-column
		      prop="cat_name"
		      label="商品分类"
		      width="120"
		      :filters="filters"
		      :filter-method="filterTag"
		      >
		      <template scope="scope">
		        <el-tag
				close-transition>@{{scope.row.cat_name}}</el-tag>
		      </template>
		    </el-table-column>
		    <el-table-column
		      prop="goods_thumb"
		      label="商品图片"
		      width="120">
		      <template scope="scope">
		        <img  :src="scope.row.goods_thumb" alt="" style="display:block;max-width:80%;max-height:80%;margin:0 auto;"/>
		      </template>
		    </el-table-column>
		    <el-table-column
		      prop="goods_price"
		      label="单价"
		      width="100">
		    </el-table-column>
		    <el-table-column
		      prop="goods_number"
		      label="库存"
		      width="80">
		    </el-table-column>
		    <el-table-column
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
		    <el-table-column
		      prop="goods_desc"
		      label="商品简介"
		      width="120"
		      show-overflow-tooltip>
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
		    </el-table-column>
		  </el-table>
		  <!-- 编辑框 -->
		  <el-dialog title="修改商品" v-model="dialogFormVisible"  v-loading.body="updataloading" element-loading-text="正在修改中，请稍后">
			  <el-form :model="form">
			    <el-form-item label="商品名称" :label-width="formLabelWidth">
			     <el-input v-model="form.goods_name" auto-complete="off"></el-input>
			    </el-form-item>
			    <el-form-item label="商品图片" :label-width="formLabelWidth">
				    <el-upload
				     name = "uploadfile"
					  class="avatar-uploader"
					  :action=this.localhost+"/business/goods/uploadGoodsImage"
					  :show-file-list="false"
					  :on-success="handleAvatarScucess"
					  :before-upload="beforeAvatarUpload">
					  <img v-if="form.goods_thumb" :src="form.goods_thumb" class="avatar">
					  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
					</el-upload>
			    </el-form-item>
			    <el-form-item label="商品单价(元)" :label-width="formLabelWidth">
			     <el-input v-model="form.goods_price" auto-complete="off"></el-input>
			    </el-form-item>
			     <el-form-item label="商品库存(件)" :label-width="formLabelWidth">
			      <el-input-number v-model="form.goods_number" @change="" :min="0" :max="999"></el-input-number>
			    </el-form-item>
			    <el-form-item label="商品简介" :label-width="formLabelWidth">
			     <el-input
					  type="textarea"
					  :autosize="{ minRows: 2, maxRows: 4}"
					  placeholder="请输入内容"
					  v-model="form.goods_desc">
					</el-input>
			    </el-form-item>
			    <el-form-item label="商品分类" :label-width="formLabelWidth">
			      <el-select v-model="form.cat_id" placeholder="请选择商品分类">
			      	<template v-for="categories in filters">
			        	<el-option  :label="categories.value" :value="categories.id"></el-option>
			      	</template>
			      </el-select>
			    </el-form-item>
			    <el-form-item label="商品状态" :label-width="formLabelWidth">
			      <el-select v-model="form.is_on_sale_name" placeholder="请选择商品状态">
			        <el-option label="上架" value="上架"></el-option>
			        <el-option label="下架" value="下架"></el-option>
			      </el-select>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogFormVisible = false">取 消</el-button>
			    <el-button type="primary" @click="updateGoods">确 定</el-button>
			  </div>
		  </el-dialog>

		  <!-- 编辑框 End-->
		  <!-- 上传商品弹出层 -->
		  <el-dialog title="上传商品" v-model="dialogStore"   v-loading.body="storeloading" element-loading-text="正在上传中，请稍后">
			  <el-form :model="storeForm">
			    <el-form-item label="商品名称" :label-width="formLabelWidth">
			     <el-input v-model="storeForm.goods_name" auto-complete="off"></el-input>
			    </el-form-item>
			    <el-form-item label="商品图片" :label-width="formLabelWidth">
				    <el-upload
				     name = "uploadfile"
					  class="avatar-uploader"
					  :action=this.localhost+"/business/goods/uploadGoodsImage"
					  :show-file-list="false"
					  :on-success="handleAvatarScucess"
					  :before-upload="beforeAvatarUpload">
					  <img v-if="storeForm.goods_thumb" :src="storeForm.goods_thumb" class="avatar">
					  <i v-else class="el-icon-plus avatar-uploader-icon"></i>
					</el-upload>
			    </el-form-item>
			    <el-form-item label="商品单价(元)" :label-width="formLabelWidth">
			     <el-input v-model="storeForm.goods_price" auto-complete="off"></el-input>
			    </el-form-item>
			     <el-form-item label="商品库存(件)" :label-width="formLabelWidth">
			      <el-input-number v-model="storeForm.goods_number" @change="" :min="0" :max="999"></el-input-number>
			    </el-form-item>
			    <el-form-item label="商品简介" :label-width="formLabelWidth">
			     <el-input
					  type="textarea"
					  :autosize="{ minRows: 2, maxRows: 4}"
					  placeholder="请输入内容"
					  v-model="storeForm.goods_desc">
					</el-input>
			    </el-form-item>
			    <el-form-item label="商品分类" :label-width="formLabelWidth">
			      <el-select v-model="storeForm.cat_id" placeholder="请选择商品分类">
			      	<template v-for="categories in filters">
			        	<el-option  :label="categories.value" :value="categories.id"></el-option>
			      	</template>
			      </el-select>
			    </el-form-item>
			    <el-form-item label="商品状态" :label-width="formLabelWidth">
			      <el-select v-model="storeForm.is_on_sale_name" placeholder="请选择商品状态">
			        <el-option label="上架" value="上架"></el-option>
			        <el-option label="下架" value="下架"></el-option>
			      </el-select>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogStore = false">取 消</el-button>
			    <el-button type="primary" @click="storeGoods">确 定</el-button>
			  </div>
		  </el-dialog>

		  <!-- 上传商品弹出层 End-->
		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="productTablePage" current-page="1"  :current-page="1" :page-size="15">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 商品列表 template-->
	<!-- 分类管理 template-->
	<template id="classify">
		<div class="productTable">
		 <el-input
		  placeholder="请输入搜索分类名称"
		  icon="search"
		  :disabled="true">
		 </el-input>
		 <el-button type="primary" style="float:right;margin:8px 0 0 0" @click="showAdd">增加分类<i class="el-icon-upload el-icon--right"></i></el-button>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="classifyData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="50">
		    </el-table-column>
		    <el-table-column
		      prop="cat_name"
		      label="分类名称"
		      width="800">
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
		    </el-table-column>
		  </el-table>
		  <!-- 编辑框 -->
		  <el-dialog title="修改分类" v-model="dialogFormVisible"  v-loading.body="updataloading" element-loading-text="正在修改中，请稍后">
			  <el-form :model="form">
			    <el-form-item label="分类名称" :label-width="formLabelWidth">
			     <el-input v-model="form.cat_name" auto-complete="off"></el-input>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogFormVisible = false">取 消</el-button>
			    <el-button type="primary" @click="updateClassify" >确 定</el-button>
			  </div>
		  </el-dialog>

		  <!-- 编辑框 End-->
		  <!-- 上传商品弹出层 -->
		  <el-dialog title="增加分类" v-model="dialogStore"   v-loading.body="storeloading" element-loading-text="正在上传中，请稍后">
			<el-form :model="storeForm">
			    <el-form-item label="分类名称" :label-width="formLabelWidth">
			     <el-input v-model="storeForm.cat_name" auto-complete="off"></el-input>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogStore = false">取 消</el-button>
			    <el-button type="primary" @click="storeClassify">确 定</el-button>
			  </div>
		  </el-dialog>

		  <!-- 上传商品弹出层 End-->
		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="classifyPage" current-page="1"  :current-page="1" :page-size="15">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 分类管理 template-->
	<!-- 未发货 template-->
	<template id="notShipped">
		<div class="productTable">
		 <el-input
		  placeholder="请输入搜索订单号"
		  icon="search"
		  :disabled="true">
		 </el-input>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="orderData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="50">
		    </el-table-column>
		    <el-table-column
		      prop="order_sn"
		      label="订单号"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="created_at"
		      label="下单时间"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="consignee"
		      label="收货人"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="mobile"
		      label="用户联系方式"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="address"
		      label="用户地址"
		      width="140">
		    </el-table-column>
		     <el-table-column
		      prop="total_fee"
		      label="总价"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="status_desc"
		      label="订单状态"
		      width="100">
		    </el-table-column>
	     	<el-table-column label="操作"  width="">
		      <template scope="scope">
		        <el-button
		          size="small"
		          @click="PThandleEdit(scope.$index, scope.row)">查看</el-button>
		        <el-button
		          size="small"
		          type="danger"
		          modal="true"
		          @click="delivery(scope.$index, scope.row)">发货</el-button>
		      </template>
		    </el-table-column>
		  </el-table>
		  <!-- 编辑框 -->
		  <el-dialog title="订单详情" v-model="dialogFormVisible" >
			  <el-form :model="form">
			  	<el-steps :space="200" :active="1" :align-center="true" finish-status="success" style="margin:0 0 20px 100px">
				  <el-step title="付款成功" :description="form.pay_time" ></el-step>
				  <el-step title="发货中" description="" icon="circle-check"></el-step>
				  <el-step title="已完成" description="" icon="circle-check"></el-step>
				</el-steps>
			    <el-form-item label="商品列表:" :label-width="formLabelWidth">
			     <template>
				    <el-table
				      :data="form.order_goodses"
				      style="width: 100%">
				      <el-table-column
				        prop="goods_name"
				        label="商品名称"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_number"
				        label="商品数量"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_price"
				        label="商品单价">
				      </el-table-column>
				    </el-table>
				  </template>
			    </el-form-item>
			    <el-form-item label="" :label-width="formLabelWidth">
			      <span class="c">商品总价(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.goods_amount}}</label></span>
			      <span class="c">运费(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.shipping_fee}}</label></span>
			      <span class="c">总计(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.total_fee}}</label></span>
			    </el-form-item>
			    <el-form-item label="收货人:" :label-width="formLabelWidth">
			      <span class="c">@{{form.consignee}}</span>
			    </el-form-item>
			    <el-form-item label="用户联系方式:" :label-width="formLabelWidth">
			      <span class="c">@{{form.mobile}}</span>
			    </el-form-item>
			    <el-form-item label="用户地址:" :label-width="formLabelWidth">
			      <span class="c">@{{form.address}}</span>
			    </el-form-item>
			    <el-form-item label="留言:" :label-width="formLabelWidth">
			      <span class="c">@{{form.postscript}}</span>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogFormVisible = false">关 闭</el-button>
			  </div>
		  </el-dialog>

		  <!-- 编辑框 End-->
		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="notShippedPage" current-page="1"  :current-page="1" :page-size="15">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 未发货 template-->
	<!-- 已发货 template-->
	<template id="shipped">
		<div class="productTable">
		 <el-input
		  placeholder="请输入搜索订单号"
		  icon="search"
		  :disabled="true">
		 </el-input>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="orderData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="50">
		    </el-table-column>
		    <el-table-column
		      prop="order_sn"
		      label="订单号"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="created_at"
		      label="下单时间"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="consignee"
		      label="收货人"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="mobile"
		      label="用户联系方式"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="address"
		      label="用户地址"
		      width="140">
		    </el-table-column>
		     <el-table-column
		      prop="total_fee"
		      label="总价"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="status_desc"
		      label="订单状态"
		      width="100">
		    </el-table-column>
	     	<el-table-column label="操作"  width="">
		      <template scope="scope">
		        <el-button
		          size="small"
		          @click="PThandleEdit(scope.$index, scope.row)">查看</el-button>
		      </template>
		    </el-table-column>
		  </el-table>
		  <!-- 编辑框 -->
		  <el-dialog title="订单详情" v-model="dialogFormVisible" >
			  <el-form :model="form">
			  	<el-steps :space="200" :active="2" :align-center="true" finish-status="success" style="margin:0 0 20px 100px">
				  <el-step title="付款成功" :description="form.pay_time" ></el-step>
				  <el-step title="发货中" :description="form.shipping_time"  ></el-step>
				  <el-step title="已完成" description=""  icon="circle-check"></el-step>
				</el-steps>
			    <el-form-item label="商品列表:" :label-width="formLabelWidth">
			     <template>
				    <el-table
				      :data="form.order_goodses"
				      style="width: 100%">
				      <el-table-column
				        prop="goods_name"
				        label="商品名称"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_number"
				        label="商品数量"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_price"
				        label="商品单价">
				      </el-table-column>
				    </el-table>
				  </template>
			    </el-form-item>
			    <el-form-item label="" :label-width="formLabelWidth">
			      <span class="c">商品总价(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.goods_amount}}</label></span>
			      <span class="c">运费(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.shipping_fee}}</label></span>
			      <span class="c">总计(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.total_fee}}</label></span>
			    </el-form-item>
			    <el-form-item label="收货人:" :label-width="formLabelWidth">
			      <span class="c">@{{form.consignee}}</span>
			    </el-form-item>
			    <el-form-item label="用户联系方式:" :label-width="formLabelWidth">
			      <span class="c">@{{form.mobile}}</span>
			    </el-form-item>
			    <el-form-item label="用户地址:" :label-width="formLabelWidth">
			      <span class="c">@{{form.address}}</span>
			    </el-form-item>
			    <el-form-item label="留言:" :label-width="formLabelWidth">
			      <span class="c">@{{form.postscript}}</span>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogFormVisible = false">关 闭</el-button>
			  </div>
		  </el-dialog>

		  <!-- 编辑框 End-->
		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="shippedPage" current-page="1"  :current-page="1" :page-size="15">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 已发货 template-->
	<!-- 已完成 template-->
	<template id="succ">
		<div class="productTable">
		 <el-input
		  placeholder="请输入搜索订单号"
		  icon="search"
		  :disabled="true">
		 </el-input>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="orderData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="50">
		    </el-table-column>
		    <el-table-column
		      prop="order_sn"
		      label="订单号"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="created_at"
		      label="下单时间"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="consignee"
		      label="收货人"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="mobile"
		      label="用户联系方式"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="address"
		      label="用户地址"
		      width="140">
		    </el-table-column>
		     <el-table-column
		      prop="total_fee"
		      label="总价"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="status_desc"
		      label="订单状态"
		      width="100">
		    </el-table-column>
	     	<el-table-column label="操作"  width="">
		      <template scope="scope">
		        <el-button
		          size="small"
		          @click="PThandleEdit(scope.$index, scope.row)">查看</el-button>
		      </template>
		    </el-table-column>
		  </el-table>
		  <!-- 编辑框 -->
		  <el-dialog title="订单详情" v-model="dialogFormVisible" >
			  <el-form :model="form">
			  	<el-steps :space="200" :active="3" :align-center="true" finish-status="success" style="margin:0 0 20px 100px">
				 <el-step title="付款成功" :description="form.pay_time" ></el-step>
				  <el-step title="发货中" :description="form.shipping_time" ></el-step>
				  <el-step title="已完成" :description="form.succ_time"  ></el-step>
				</el-steps>
			    <el-form-item label="商品列表:" :label-width="formLabelWidth">
			     <template>
				    <el-table
				      :data="form.order_goodses"
				      style="width: 100%">
				      <el-table-column
				        prop="goods_name"
				        label="商品名称"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_number"
				        label="商品数量"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_price"
				        label="商品单价">
				      </el-table-column>
				    </el-table>
				  </template>
			    </el-form-item>
			    <el-form-item label="" :label-width="formLabelWidth">
			      <span class="c">商品总价(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.goods_amount}}</label></span>
			      <span class="c">运费(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.shipping_fee}}</label></span>
			      <span class="c">总计(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.total_fee}}</label></span>
			    </el-form-item>
			    <el-form-item label="收货人:" :label-width="formLabelWidth">
			      <span class="c">@{{form.consignee}}</span>
			    </el-form-item>
			    <el-form-item label="用户联系方式:" :label-width="formLabelWidth">
			      <span class="c">@{{form.mobile}}</span>
			    </el-form-item>
			    <el-form-item label="用户地址:" :label-width="formLabelWidth">
			      <span class="c">@{{form.address}}</span>
			    </el-form-item>
			    <el-form-item label="留言:" :label-width="formLabelWidth">
			      <span class="c">@{{form.postscript}}</span>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogFormVisible = false">关 闭</el-button>
			  </div>
		  </el-dialog>

		  <!-- 编辑框 End-->
		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="shippedPage" current-page="1"  :current-page="1" :page-size="15">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 已完成 template-->
	<!-- 退款与售后 template-->
	<template id="cancell">
		<div class="productTable">
		 <el-input
		  placeholder="请输入搜索订单号"
		  icon="search"
		  :disabled="true">
		 </el-input>
		<el-table
		  	v-loading.body="loading"
		  	element-loading-text="玩命加载中"
		    :data="orderData"
		    border
		    style="width:100%,text-align:center;"
		    @selection-change="handleSelectionChange">
		    <el-table-column
		      type="selection"
		      width="50">
		    </el-table-column>
		    <el-table-column
		      prop="order_sn"
		      label="订单号"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="created_at"
		      label="下单时间"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="consignee"
		      label="收货人"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="mobile"
		      label="用户联系方式"
		      width="140">
		    </el-table-column>
		    <el-table-column
		      prop="address"
		      label="用户地址"
		      width="140">
		    </el-table-column>
		     <el-table-column
		      prop="total_fee"
		      label="总价"
		      width="80">
		    </el-table-column>
		    <el-table-column
		      prop="status_desc"
		      label="订单状态"
		      width="100">
		    </el-table-column>
	     	<el-table-column label="操作"  width="">
		      <template scope="scope">
		        <el-button
		          size="small"
		          @click="PThandleEdit(scope.$index, scope.row)">查看</el-button>
		          <el-button
		          size="small"
		          type="danger"
		          v-show="scope.row.shipping_status == 0"
		          @click="refund(scope.$index, scope.row)">同意退款</el-button>
		      </template>
		    </el-table-column>
		  </el-table>
		  <!-- 编辑框 -->
		  <el-dialog title="订单详情" v-model="dialogFormVisible" >
			  <el-form :model="form">
				<el-steps :space="200" :active="2" :align-center="true" finish-status="success" style="margin:0 0 20px 100px">
				 <el-step title="付款成功" :description="form.pay_time" ></el-step>
				  <template  v-if="form.shipping_status == 0">
				  		<el-step title="申请退款" :description="form.cancelling_time"  ></el-step>
				  		<el-step title="退款完成" icon="circle-check" icon="circle-check"></el-step>
				  </template>
				  <template  v-else>
				  		<el-step title="退款完成" :description="form.cancelled_time"  ></el-step>
				  </template>
				</el-steps>
			    <el-form-item label="商品列表:" :label-width="formLabelWidth">
			     <template>
				    <el-table
				      :data="form.order_goodses"
				      style="width: 100%">
				      <el-table-column
				        prop="goods_name"
				        label="商品名称"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_number"
				        label="商品数量"
				        width="180">
				      </el-table-column>
				      <el-table-column
				        prop="goods_price"
				        label="商品单价">
				      </el-table-column>
				    </el-table>
				  </template>
			    </el-form-item>
			    <el-form-item label="" :label-width="formLabelWidth">
			      <span class="c">商品总价(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.goods_amount}}</label></span>
			      <span class="c">运费(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.shipping_fee}}</label></span>
			      <span class="c">总计(元)：<label style="color:#ff4949;padding:0 20px 0 0">@{{form.total_fee}}</label></span>
			    </el-form-item>
			    <el-form-item label="收货人:" :label-width="formLabelWidth">
			      <span class="c">@{{form.consignee}}</span>
			    </el-form-item>
			    <el-form-item label="用户联系方式:" :label-width="formLabelWidth">
			      <span class="c">@{{form.mobile}}</span>
			    </el-form-item>
			    <el-form-item label="用户地址:" :label-width="formLabelWidth">
			      <span class="c">@{{form.address}}</span>
			    </el-form-item>
			    <el-form-item label="留言:" :label-width="formLabelWidth">
			      <span class="c">@{{form.postscript}}</span>
			    </el-form-item>
			  </el-form>
			  <div slot="footer" class="dialog-footer">
			    <el-button @click="dialogFormVisible = false">关 闭</el-button>
			  </div>
		  </el-dialog>

		  <!-- 编辑框 End-->
		  <!-- 分页 S -->
  		  <div class="pageBlock">
		    <el-pagination
		      layout="prev, pager, next"
     		  @current-change="handleCurrentChange"
		      :total="shippedPage" current-page="1"  :current-page="1" :page-size="15">
		    </el-pagination>
		  </div>
	  		<!-- 分页 E -->
		 </div>
	</template>
	<!-- 退款与售后 template-->
	<!-- 店铺设置 -->
	<template id="setting">
		 <el-form :model="shopInfo" :rules="rules" ref="shopInfo" label-width="100px" class="ruleForm" v-loading.body="loading" element-loading-text="正在获取店铺信息...">
		  <el-form-item label="店铺名称" >
		    <el-input  v-model="shopInfo.shop_name" auto-complete="off" :disabled="true"></el-input>
		  </el-form-item>
		  <el-form-item label="店铺LOGO" >
		    <template>
		    	 <img width="100px" v-if="shopInfo.shop_img" :src="shopInfo.shop_img" class="avatar">
		    </template>
		  </el-form-item>   
		  <el-form-item label="店铺简介" prop="text">
		    <el-input type="textarea" v-model.number="shopInfo.description"></el-input>
		  </el-form-item>
		  <el-form-item label="最低购买价格" prop="min_goods_amount">
		    <el-input type="number" v-model.number="shopInfo.min_goods_amount" ></el-input>
		  </el-form-item>
		  <el-form-item label="配送费" prop="shipping_fee">
		    <el-input type="number" v-model.number="shopInfo.shipping_fee"></el-input>
		  </el-form-item>
		  <el-form-item label="开店时间" >
		    <template>
			  <el-time-select
			    placeholder="营业时间"
			    v-model="shopInfo.open_time"
			    :picker-options="{
			      start: '00:00',
			      step: '00:05',
			      end: '24:00'
			    }">
			  </el-time-select>
			  <el-time-select
			    placeholder="打烊时间"
			    v-model="shopInfo.close_time"
			    :picker-options="{
			      start: '00:00',
			      step: '00:05',
			      end: '24:00',
			      minTime: shopInfo.open_time
			    }">
			  </el-time-select>
			</template>
		  </el-form-item>
		  <el-form-item label="是否开店" >
		    <el-switch on-text="开店" off-text="关店" v-model="shopInfo.shop_status_flag" on-color="#13ce66" off-color="#999" ></el-switch>
		  </el-form-item>

		  <el-form-item>
		    <el-button type="primary" @click="submitForm('shopInfo')">确定修改</el-button>
		  </el-form-item>
		</el-form>

	</template>
	<!-- 店铺设置 -->

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
		        	$.getJSON(this.localhost+'/business/user/getUser',function(data){
					          		that.userInfo = {
					          			"nickname":data.user.nickname,
					          			"avatar_url":data.user.avatar_url,
					          			"wallet":data.user.wallet,
					          			"sale_count":data.shop.sale_count,
      									"income":data.shop.income,
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
	          snavs:[{"value":"首页","path":""},{"value":"商品管理","path":""}],
		  	  defaultActive:"1-1",
		  	  userInfo:{
		  	  	"nickname":"",
      			"avata	r_url":"",
      			"wallet":"",
      			"sale_count":"",
      			"income":""
		  	  }
	        }
	      }
	  }
	var Ctor = Vue.extend(Main);
	new Ctor({router}).$mount('#app');
	
  </script>
@stop