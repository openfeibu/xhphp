// 注册
Vue.component('a-logo', {
  template: '<div class="logo"><a href="index.html"></a></div>'
})
Vue.prototype.localhost = "http://xhplus.feibu.info/";
var table = Vue.extend({
	template: '#table',
	 methods: {
		      handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },

			  //分页获取数据
			  getData(page,input){
				  	var that = this;
				  	var postData={
				  		page:page,
				  	};
				  	if(input){
				  		postData.keyword = that.input2;

				  	}
				  	//判断是否有宿舍
				  	if(that.ssSelectedOptions){
				  		postData.campus_id = that.ssSelectedOptions[0];
				  		postData.building_id = that.ssSelectedOptions[1];
				  	}
				  	//判断是否有宿舍
				  	//判断是否有时间
				  	if(that.dateVal){
				  		postData.date = that.dateVal.getTime()/1000;
				  	}
				  	//判断是否有时间
				   	that.loading = true;
				  	that.tableData = [];
				  	 $.getJSON(this.localhost+'telecom/getEnrolls',postData,function(data){
				          that.loading = false;
				          that.productTablePage = parseInt(data.count);
				        	var orderArray ;
				          $.each(data.data,function(a,b){
			          		var orderArray = {
			          			"building_id":b.building_id,
			          			"building_name":b.building_name,
			          			"campus_id":b.campus_id,
			          			"created_at":b.created_at,
			          			"date":b.date,
			          			"dormitory_number":b.dormitory_number,
			          			"enroll_id":b.enroll_id,
			          			"name":b.name,
			          			"mobile_no":b.mobile_no,
			          		}
			          		that.tableData.push(orderArray);

			          })
			          }).error(function(){
			            that.loading = false;
				        that.$message.error('服务器开小差了');
			          })
				},
				getSchoolCampusBuildings(page){
				  	var that = this;
				   	that.loading = true;
				  	 $.getJSON(this.localhost+'telecom/getSchoolCampusBuildings',function(data){
				        that.loading = false;
				        var BuildArray;
				          $.each(data.data,function(a,b){
				          	var childrens = [];
				          	 $.each(b.buildings,function(k,v){
				          	 	var children = {
				          	 		value:v.building_id,
				          	 		label:v.building_no
				          	 	}

				          	 	childrens.push(children);
				          	 })
				          	 console.log(childrens)
			          		var BuildArray = {
			          			"value":b.campus_id,
			          			"label":b.campus_name,
			          			"children":childrens,
			          		}
			          		that.ssOptions.push(BuildArray);

			          })
				          console.log(that.ssOptions)
			          }).error(function(){
			            that.loading = false;
				        that.$message.error('服务器开小差了');
			          })
				},
		      //更改页数
		      handleCurrentChange(val) {
		        this.currentPage = val;
		        this.getData(val)
		      },
		       handleIconClick(ev) {
			      var that = this;
		        	that.getData(1,that.input2);
			   },
		      handleAvatarScucess(res, file) {
		        this.form.goods_thumb = res.thumb_url;
		        this.form.goods_img = res.url;
		        this.storeForm.goods_thumb = res.thumb_url;
		        this.storeForm.goods_img = res.url
		      },
		      ssHandleChange(val){
		      	var that = this;
		      	that.getData(1);
		      },
		      resetForm(){
		      	var that = this;
		      	that.dateVal	='';
		      	that.input2='';
		      },
		      exportForm(){
		      		var that = this;
				  	var url=this.localhost+'telecom/explodeEnrolls?';
				  	var ssSelectedOptionUrl ='';
				  	var dateUrl ='';
				  	//判断是否有宿舍
				  	if(that.ssSelectedOptions.length !=0){
				  		ssSelectedOptionUrl = 'campus_id='+that.ssSelectedOptions[0]+'&building_id='+that.ssSelectedOptions[1]+'&';
				  	}
				  	//判断是否有宿舍
				  	//判断是否有时间
				  	if(that.dateVal){
				  		dateUrl = 'date='+that.dateVal.getTime()/1000
				  	}
				  	url = url+ssSelectedOptionUrl+dateUrl;
				  	console.log(url)
				  	window.open(url,'_blank');
				  	//判断是否有时间
		      }
		},
		created:function(){
		  	//获取列表
		  	var that = this;
		    that.getData(1);
		    that.getSchoolCampusBuildings();

		},
		data: function() {
			return {
		        dateVal:'',
				tableData: [],
			    loading:true,
			    input2:"",
			    productTablePage:0,
			    dialogFormVisible:false,
		        formLabelWidth: '120px',
		        updataloading:false,
		        ssSelectedOptions:[],
		        ssOptions:[],
			}
		}
});

var classify = Vue.extend({
	template: '#classify',
	 methods: {
	 		  handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
			//弹出修改分类层
			PThandleEdit(index, row) {
			  	this.dialogFormVisible = true;
			  	this.form = {
			  		"index":index,
			  		"cat_name":row.cat_name,
		  			"cat_id":row.cat_id,
			      }
		    },
		      //弹出增加层
			showAdd() {
			  	this.dialogStore = true;
			  	this.storeForm ={
				        "index":"",
				  		"cat_name":"",
			  			"cat_id":"",

			        }
		    },
		      //确认修改分类
			updateClassify(){
			      	var that = this;
			      	that.updataloading = true;
				  	 $.post(this.localhost+'/business/goods/updateCat/',that.form,function(data){;
				          if(data.code == 200){
				          	//更新成功
					  		that.dialogFormVisible = false;
					  		that.updataloading = false;
				          	that.$message({
					          message: '修改成功',
					          type: 'success'
					        });
					        //更新数据
					        that.$set(that.classifyData,that.form.index,{
				          			"cat_name":data.cats.cat_name,
				          			"cat_id":data.cats.cat_id
					        })
				          }else{
				          	that.updataloading = false;
				          	that.$message.error(data.detail);
				          }
				          }).error(function(){
				          	 that.updataloading = false;
				             that.$message.error('服务器开小差了');
				          })
			},
			    //确认增加分类
			    storeClassify(){
			      	var that = this;
			      	that.storeloading = true;
				  	 $.post(this.localhost+'/business/goods/addCat',that.storeForm,function(data){
				          if(data.code == 200){
				          	//更新成功
					  		that.dialogStore = false;
					  		that.storeloading = false;
				          	that.$message({
					          message: '增加成功',
					          type: 'success'
					        });
					        //更新数据
					        that.classifyData.push({
				          			"cat_name":data.cats.cat_name,
				          			"cat_id":data.cats.cat_id
					        })
				          }else{
				          	that.storeloading = false;
				          	that.$message.error(data.detail);
				          }
				          }).error(function(){
				             that.$message.error('服务器开小差了');
				          })

			    },
			  //分页获取数据
				getData(page){
				  	var that = this;
				   	that.loading = true;
				  	 $.getJSON(this.localhost+'/business/goods/getCats?page='+page,function(data){
				         that.loading = false;
				          that.classifyPage = parseInt(data.count);
				          that.classifyData=[];
				          $.each(data.cats,function(a,b){
				          		var classifyArray = {
				          			"cat_id":b.cat_id,
				          			"cat_name":b.cat_name
				          		}
				          		that.classifyData.push(classifyArray);
				          })
			          }).error(function(){
			          	that.loading = false;
			            that.$message.error('服务器开小差了');
			          })
				},
				//删除分类
				deleclassify(index,cat_id){
					var that = this;
					that.loading = true;
					$.post(this.localhost+'/business/goods/deleteCat?cat_id='+cat_id,function(data){
				         if(data.code == 200 ){
					        	that.loading = false;
				           that.$message({
					            type: 'success',
					            message: '删除成功!'
					         });
				         that.classifyData.splice(index,1);
				        } else{
				        	that.loading = false;
				           	that.$message({
					            type: 'error',
					            message: data.detail
					         });
				        }

			          }).error(function(){
			          	that.loading = false;
			            that.$message.error('服务器开小差了');
			          })

				},
			    PThandleDelete(index, row) {
			        var that = this;
			         this.$confirm('永久删除该分类?', '校汇', {
			          confirmButtonText: '确定',
			          cancelButtonText: '取消',
			          type: 'warning'
			        }).then(() => {
						that.deleclassify(index,row.cat_id)

			        }).catch(() => {
			          this.$message({
			            type: 'info',
			            message: '已取消删除'
			          });
			        });
			    },
		      //更改页数
			    handleCurrentChange(val) {
			        this.currentPage = val;
			        this.getData(val)
			    },
			 },
				created:function(){
			  	//获取列表
			  	var that = this;
			  	 $.getJSON(this.localhost+'/business/goods/getCats?page=1',function(data){
			          that.loading = false;
			          that.classifyPage = parseInt(data.count);
			          $.each(data.cats,function(a,b){
			          		var classifyArray = {
			          			"cat_id":b.cat_id,
			          			"cat_name":b.cat_name
			          		}
			          		that.classifyData.push(classifyArray);
			          })
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
					},
				data: function() {
				return {
					classifyData: [],
				    multipleSelection: [],
				    filters:[],
				    state:[{"text":"上架","value":"上架"},{"text":"下架","value":"下架"}],
				    loading:true,
				    input2:"",
				    classifyPage:0,
				    dialogFormVisible:false,
				     form: {
				        "index":"",
				  		"cat_name":"",
			  			"cat_id":"",

			        },
			        storeloading:false,
				    dialogStore:false,
			        storeForm: {
				       "index":"",
				  		"cat_name":"",
			  			"cat_id":"",

			        },
			        formLabelWidth: '120px',
			        updataloading:false,
				}
				}
});

// 未发货订单
var notShipped = Vue.extend({
	 template: '#notShipped',
	 methods: {
	 		  handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
				//弹出详情
				PThandleEdit(index, b) {
				  	this.dialogFormVisible = true;
				  	this.form = {
				  		"address":b.address,
	          			"consignee":b.consignee,
	          			"created_at":b.created_at,
	          			"goods_amount":b.goods_amount,
	          			"mobile":b.mobile,
	          			"order_id":b.order_id,
	          			"order_sn":b.order_sn,
	          			"order_status":b.order_status,
	          			"pay_time":b.pay_time,
	          			"postscript":b.postscript,
	          			"shipping_fee":b.shipping_fee,
	          			"shipping_status":b.shipping_status,
	          			"total_fee":b.total_fee,
	          			"order_goodses":b.order_goodses
				      }
				      console.log(this.form.order_goodses)
			    },
				  //分页获取数据
				getData(page){
					  	var that = this;
					   	that.loading = true;
					  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?type=beship&page='+page,function(data){
					          that.loading = false;
					          that.notShippedPage = parseInt(data.count);
					          that.orderData = [];
					          $.each(data.order_infos,function(a,b){
					          		var orderArray = {
					          			"address":b.address,
					          			"consignee":b.consignee,
					          			"created_at":b.created_at,
					          			"goods_amount":b.goods_amount,
					          			"mobile":b.mobile,
					          			"order_id":b.order_id,
					          			"order_sn":b.order_sn,
					          			"order_status":b.order_status,
					          			"pay_time":b.pay_time,
					          			"postscript":b.postscript,
					          			"shipping_fee":b.shipping_fee,
					          			"shipping_status":b.shipping_status,
					          			"total_fee":b.total_fee,
					          			"order_goodses":b.order_goodses,
					          			"status_desc":b.status_desc
					          		}
					          		that.orderData.push(orderArray);
					          })
				          }).error(function(){

				            that.$message.error('服务器开小差了');
				          })
				},
				//发货
				deliveryGoods(index,order_id){
					var that = this;
					that.loading = true;
					$.post(this.localhost+'/business/orderInfo/shipping?order_id='+order_id,function(data){
				        if(data.code == 200 ){
				        	that.loading = false;
				           	that.$message({
					            type: 'success',
					            message: '发货成功!'
					         });
				        	that.orderData.splice(index,1);
				        } else{
				        	that.loading = false;
				           	that.$message({
					            type: 'error',
					            message: data.detail
					         });
				        }
			          }).error(function(){
			          	that.loading = false;
			            that.$message.error('服务器开小差了');
			          })

				},
			    delivery(index, row) {
			        var that = this;
			         this.$confirm('东西已打包，我要发货?', '校汇', {
			          confirmButtonText: '确定',
			          cancelButtonText: '取消',
			          type: 'success'
			        }).then(() => {
						that.deliveryGoods(index,row.order_id)

			        }).catch(() => {
			          this.$message({
			            type: 'info',
			            message: '已取消发货'
			          });
			        });
			    },
		      //更改页数
			    handleCurrentChange(val) {
			        this.currentPage = val;
			        this.getData(val)
			    },
			 },
				created:function(){
			  	//获取列表
			  	var that = this;
			  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page=1&type=beship',function(data){
			          that.loading = false;
			          that.notShippedPage = parseInt(data.count);
			          $.each(data.order_infos,function(a,b){
			          		var orderArray = {
			          			"address":b.address,
			          			"consignee":b.consignee,
			          			"created_at":b.created_at,
			          			"goods_amount":b.goods_amount,
			          			"mobile":b.mobile,
			          			"order_id":b.order_id,
			          			"order_sn":b.order_sn,
			          			"order_status":b.order_status,
			          			"pay_time":b.pay_time,
			          			"postscript":b.postscript,
			          			"shipping_fee":b.shipping_fee,
			          			"shipping_status":b.shipping_status,
			          			"total_fee":b.total_fee,
			          			"order_goodses":b.order_goodses,
			          			"status_desc":b.status_desc
			          		}
			          		that.orderData.push(orderArray);

			          })
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
					},
				data: function() {
					return {
						orderData: [],
					    loading:true,
					    notShippedPage:0,
					    dialogFormVisible:false,
					    form: {},
				        formLabelWidth: '120px',
				        updataloading:false,
					}
				}
});

// 已发货订单
var shipped = Vue.extend({
	template: '#shipped',
	 methods: {
	 		  handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
				//弹出详情
				PThandleEdit(index, b) {
				  	this.dialogFormVisible = true;
				  	this.form = {
				  		"address":b.address,
	          			"consignee":b.consignee,
	          			"created_at":b.created_at,
	          			"goods_amount":b.goods_amount,
	          			"mobile":b.mobile,
	          			"order_id":b.order_id,
	          			"order_sn":b.order_sn,
	          			"order_status":b.order_status,
	          			"pay_time":b.pay_time,
	          			"postscript":b.postscript,
	          			"shipping_fee":b.shipping_fee,
	          			"shipping_status":b.shipping_status,
	          			"total_fee":b.total_fee,
	          			"order_goodses":b.order_goodses,
	          			"shipping_time":b.shipping_time
				      }
				      console.log(this.form.order_goodses)
			    },
				  //分页获取数据
				getData(page){
					  	var that = this;
					   	that.loading = true;
					  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?type=shipping&page='+page,function(data){
					          that.loading = false;
					          that.shippedPage = parseInt(data.count);
					          that.orderData = [];
					          $.each(data.order_infos,function(a,b){
					          		var orderArray = {
					          			"address":b.address,
					          			"consignee":b.consignee,
					          			"created_at":b.created_at,
					          			"goods_amount":b.goods_amount,
					          			"mobile":b.mobile,
					          			"order_id":b.order_id,
					          			"order_sn":b.order_sn,
					          			"order_status":b.order_status,
					          			"pay_time":b.pay_time,
					          			"postscript":b.postscript,
					          			"shipping_fee":b.shipping_fee,
					          			"shipping_status":b.shipping_status,
					          			"total_fee":b.total_fee,
					          			"order_goodses":b.order_goodses,
					          			"status_desc":b.status_desc,
					          			"shipping_time":b.shipping_time
					          		}
					          		that.orderData.push(orderArray);
					          })
				          }).error(function(){

				            that.$message.error('服务器开小差了');
				          })
				},
				//发货
				deliveryGoods(index,order_id){
					var that = this;
					that.loading = true;
					$.post(this.localhost+'/business/orderInfo/shipping?order_id='+order_id,function(data){
				        if(data.code == 200 ){
				        	that.loading = false;
				           	that.$message({
					            type: 'success',
					            message: '发货成功!'
					         });
				        	that.orderData.splice(index,1);
				        } else{
				        	that.loading = false;
				           	that.$message({
					            type: 'error',
					            message: data.detail
					         });
				        }
			          }).error(function(){
			          	that.loading = false;
			            that.$message.error('服务器开小差了');
			          })

				},
			    delivery(index, row) {
			        var that = this;
			         this.$confirm('东西已打包，我要发货?', '校汇', {
			          confirmButtonText: '确定',
			          cancelButtonText: '取消',
			          type: 'success'
			        }).then(() => {
						that.deliveryGoods(index,row.order_id)

			        }).catch(() => {
			          this.$message({
			            type: 'info',
			            message: '已取消发货'
			          });
			        });
			    },
		      //更改页数
			    handleCurrentChange(val) {
			        this.currentPage = val;
			        this.getData(val)
			    },
			 },
				created:function(){
			  	//获取列表
			  	var that = this;
			  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page=1&type=shipping',function(data){
			          that.loading = false;
			          that.shippedPage = parseInt(data.count);
			          $.each(data.order_infos,function(a,b){
			          		var orderArray = {
			          			"address":b.address,
			          			"consignee":b.consignee,
			          			"created_at":b.created_at,
			          			"goods_amount":b.goods_amount,
			          			"mobile":b.mobile,
			          			"order_id":b.order_id,
			          			"order_sn":b.order_sn,
			          			"order_status":b.order_status,
			          			"pay_time":b.pay_time,
			          			"postscript":b.postscript,
			          			"shipping_fee":b.shipping_fee,
			          			"shipping_status":b.shipping_status,
			          			"total_fee":b.total_fee,
			          			"order_goodses":b.order_goodses,
			          			"status_desc":b.status_desc,
			          			"shipping_time":b.shipping_time
			          		}
			          		that.orderData.push(orderArray);

			          })
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
					},
				data: function() {
					return {
						orderData: [],
					    loading:true,
					    shippedPage:0,
					    dialogFormVisible:false,
					    form: {},
				        formLabelWidth: '120px',
				        updataloading:false,
					}
				}
});

// 已完成订单
var succ = Vue.extend({
	template: '#succ',
	 methods: {
	 		  handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
				//弹出详情
				PThandleEdit(index, b) {
				  	this.dialogFormVisible = true;
				  	this.form = {
				  		"address":b.address,
	          			"consignee":b.consignee,
	          			"created_at":b.created_at,
	          			"goods_amount":b.goods_amount,
	          			"mobile":b.mobile,
	          			"order_id":b.order_id,
	          			"order_sn":b.order_sn,
	          			"order_status":b.order_status,
	          			"pay_time":b.pay_time,
	          			"postscript":b.postscript,
	          			"shipping_fee":b.shipping_fee,
	          			"shipping_status":b.shipping_status,
	          			"total_fee":b.total_fee,
	          			"order_goodses":b.order_goodses,
	          			"shipping_time":b.shipping_time,
	          			"succ_time":b.succ_time
				      }
				      console.log(this.form.order_goodses)
			    },
				  //分页获取数据
				getData(page){
					  	var that = this;
					   	that.loading = true;
					  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?type=succ&page='+page,function(data){
					          that.loading = false;
					          that.shippedPage = parseInt(data.count);
					          that.orderData = [];
					          $.each(data.order_infos,function(a,b){
					          		var orderArray = {
					          			"address":b.address,
					          			"consignee":b.consignee,
					          			"created_at":b.created_at,
					          			"goods_amount":b.goods_amount,
					          			"mobile":b.mobile,
					          			"order_id":b.order_id,
					          			"order_sn":b.order_sn,
					          			"order_status":b.order_status,
					          			"pay_time":b.pay_time,
					          			"postscript":b.postscript,
					          			"shipping_fee":b.shipping_fee,
					          			"shipping_status":b.shipping_status,
					          			"total_fee":b.total_fee,
					          			"order_goodses":b.order_goodses,
					          			"status_desc":b.status_desc,
					          			"shipping_time":b.shipping_time,
	          							"succ_time":b.succ_time
					          		}
					          		that.orderData.push(orderArray);
					          })
				          }).error(function(){

				            that.$message.error('服务器开小差了');
				          })
				},

		      //更改页数
			    handleCurrentChange(val) {
			        this.currentPage = val;
			        this.getData(val)
			    },
			 },
				created:function(){
			  	//获取列表
			  	var that = this;
			  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page=1&type=succ',function(data){
			          that.loading = false;
			          that.shippedPage = parseInt(data.count);
			          $.each(data.order_infos,function(a,b){
			          		var orderArray = {
			          			"address":b.address,
			          			"consignee":b.consignee,
			          			"created_at":b.created_at,
			          			"goods_amount":b.goods_amount,
			          			"mobile":b.mobile,
			          			"order_id":b.order_id,
			          			"order_sn":b.order_sn,
			          			"order_status":b.order_status,
			          			"pay_time":b.pay_time,
			          			"postscript":b.postscript,
			          			"shipping_fee":b.shipping_fee,
			          			"shipping_status":b.shipping_status,
			          			"total_fee":b.total_fee,
			          			"order_goodses":b.order_goodses,
			          			"status_desc":b.status_desc,
			          			"shipping_time":b.shipping_time,
	          					"succ_time":b.succ_time
			          		}
			          		that.orderData.push(orderArray);

			          })
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
					},
				data: function() {
					return {
						orderData: [],
					    loading:true,
					    shippedPage:0,
					    dialogFormVisible:false,
					    form: {},
				        formLabelWidth: '120px',
				        updataloading:false,
					}
				}
});
// 退款与售后
var cancell = Vue.extend({
	template: '#cancell',
	 methods: {
	 		  handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
				//弹出详情
				PThandleEdit(index, b) {
				  	this.dialogFormVisible = true;
				  	this.form = {
				  		"address":b.address,
	          			"consignee":b.consignee,
	          			"created_at":b.created_at,
	          			"goods_amount":b.goods_amount,
	          			"mobile":b.mobile,
	          			"order_id":b.order_id,
	          			"order_sn":b.order_sn,
	          			"order_status":b.order_status,
	          			"pay_time":b.pay_time,
	          			"postscript":b.postscript,
	          			"shipping_fee":b.shipping_fee,
	          			"shipping_status":b.shipping_status,
	          			"total_fee":b.total_fee,
	          			"order_goodses":b.order_goodses,
	          			"shipping_time":b.shipping_time,
	          			"succ_time":b.succ_time,
	          			"cancelling_time":b.cancelling_time,
	          			"cancelled_time":b.cancelled_time,
				      }
			    },
				  //分页获取数据
				getData(page){
					  	var that = this;
					   	that.loading = true;
					  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?type=cancell&page='+page,function(data){
					          that.loading = false;
					          that.shippedPage = parseInt(data.count);
					          that.orderData = [];
					          $.each(data.order_infos,function(a,b){
					          		var orderArray = {
					          			"address":b.address,
					          			"consignee":b.consignee,
					          			"created_at":b.created_at,
					          			"goods_amount":b.goods_amount,
					          			"mobile":b.mobile,
					          			"order_id":b.order_id,
					          			"order_sn":b.order_sn,
					          			"order_status":b.order_status,
					          			"pay_time":b.pay_time,
					          			"postscript":b.postscript,
					          			"shipping_fee":b.shipping_fee,
					          			"shipping_status":b.shipping_status,
					          			"total_fee":b.total_fee,
					          			"order_goodses":b.order_goodses,
					          			"status_desc":b.status_desc,
					          			"shipping_time":b.shipping_time,
	          							"succ_time":b.succ_time,
	          							"cancelling_time":b.cancelling_time,
	          							"cancelled_time":b.cancelled_time,
					          		}
					          		that.orderData.push(orderArray);
					          })
				          }).error(function(){

				            that.$message.error('服务器开小差了');
				          })
				},
				//退款
				refundGoods(index,order_id){
					var that = this;
					that.loading = true;
					$.post(this.localhost+'/business/orderInfo/agreeCancel?order_id='+order_id,function(data){
				        if(data.code == 200 ){
				        	that.loading = false;
				           	that.$message({
					            type: 'success',
					            message: '退款成功!'
					         });
				        	that.$set(that.orderData,index,{
					        		"shipping_status":3,
					        })
				        } else{
				        	that.loading = false;
				           	that.$message({
					            type: 'error',
					            message: data.detail
					         });
				        }
			          }).error(function(){
			          	that.loading = false;
			            that.$message.error('服务器开小差了');
			          })
				},
			    refund(index, row) {
			        var that = this;
			         this.$confirm('是否同意用户退款?', '校汇', {
			          confirmButtonText: '确定',
			          cancelButtonText: '取消',
			          type: 'success'
			        }).then(() => {
						that.refundGoods(index,row.order_id)

			        }).catch(() => {
			          this.$message({
			            type: 'info',
			            message: '已取消退款'
			          });
			        });
			    },
		      //更改页数
			    handleCurrentChange(val) {
			        this.currentPage = val;
			        this.getData(val)
			    },
			 },
				created:function(){
			  	//获取列表
			  	var that = this;
			  	 $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page=1&type=cancell',function(data){
			          that.loading = false;
			          that.shippedPage = parseInt(data.count);
			          $.each(data.order_infos,function(a,b){
			          		var orderArray = {
			          			"address":b.address,
			          			"consignee":b.consignee,
			          			"created_at":b.created_at,
			          			"goods_amount":b.goods_amount,
			          			"mobile":b.mobile,
			          			"order_id":b.order_id,
			          			"order_sn":b.order_sn,
			          			"order_status":b.order_status,
			          			"pay_time":b.pay_time,
			          			"postscript":b.postscript,
			          			"shipping_fee":b.shipping_fee,
			          			"shipping_status":b.shipping_status,
			          			"total_fee":b.total_fee,
			          			"order_goodses":b.order_goodses,
			          			"status_desc":b.status_desc,
			          			"shipping_time":b.shipping_time,
	          					"succ_time":b.succ_time,
	          					"cancelling_time":b.cancelling_time,
	          					"cancelled_time":b.cancelled_time,
			          		}
			          		that.orderData.push(orderArray);

			          })
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
					},
				data: function() {
					return {
						orderData: [],
					    loading:true,
					    shippedPage:0,
					    dialogFormVisible:false,
					    form: {},
				        formLabelWidth: '120px',
				        updataloading:false,
					}
				}
});
// 人数设置
var setting = Vue.extend({
	template: '#setting',
	 methods: {
	 		  handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
			  submitForm(formName){
			  	var that = this;
			  	console.log(that.numberData)
			  	$.post(this.localhost+'telecom/updateEnrollSetting',that.shopInfo,function(data){
	  				if(data.code == 200){
	  					that.loading = false;
		            	that.$message.success('修改成功');
	  				}else{
	  					that.$message.error(data.detail);
	  				}
		          }).error(function(){
		            that.$message.error('服务器开小差了');
		          })
			 	},
			 	ssHandleChange(key,val){
			 		var that = this;
			 		var postData = {
			 			'setting_id':key,
			 			'count':val
			 		}
			 		if(val == that.FocusVal){
			 			return false;
			 		}
			 		that.loading = true;
			 		$.post(this.localhost+'telecom/updateEnrollSetting',postData,function(data){
		  				if(data.code == 200){
		  					that.loading = false;
			            	that.$message.success('修改成功');
		  				}else{
		  					that.$message.error(data.detail);
		  				}
			          }).error(function(){
			            that.$message.error('服务器开小差了');
			          })
			 	},
			 	ssHandleFocus(val){
			 		this.FocusVal = val;
			 	}
			},
			created:function(){
			  	//获取列表
			  	var that = this;
			  		$.getJSON(this.localhost+'telecom/getEnrollSettings',function(data){
			  				that.loading = false;
			  				if(data.code == 200){

			  					$.each(data.data,function(a,b){
			  						var lnumberData = b;
			  						that.numberData.push(lnumberData)
			  					})

			  				}else{
			  					that.$message.error(data.detail);
			  				}
				          }).error(function(){
				            that.$message.error('服务器开小差了');
				          })
					},
				data: function() {
					return {
						numberData: [],
					    loading:true,
					    FocusVal:''
					}
				}
});
