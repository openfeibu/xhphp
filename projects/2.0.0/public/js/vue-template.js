// 注册
Vue.component('a-logo', {
  template: '<div class="logo"><a href="index.html"></a></div>'
})
Vue.prototype.localhost = "http://localhost:8085";
var table = Vue.extend({
	template: '#table',
	 methods: {
		      handleSelect(key, keyPath) {
			        console.log(key, keyPath);
			      },
			  handleSelectionChange(val) {
			        this.multipleSelection = val;
			     },
			    //弹出修改商品层
			  PThandleEdit(index, row) {
			  	this.dialogFormVisible = true;
			  	this.form = { 
			  		"goods_index":index,
				  	"goods_name":row.goods_name,
		  			"goods_price":row.goods_price,
		  			"goods_desc":row.goods_desc,
		  			"goods_number":row.goods_number,
		  			"goods_thumb":row.goods_thumb,
		  			"is_on_sale_name":row.is_on_sale_name,
		  			"cat_name":row.cat_name,
		  			"cat_id":row.cat_id,
		  			"goods_id":row.goods_id,
			      }
		      },
		      //弹出增加层
			  showAdd() {
			  	this.dialogStore = true;
			  	this.storeForm ={
				       "goods_name":'',
			  			"goods_price":'',
			  			"goods_desc":'',
			  			"goods_number":'',
			  			"goods_thumb":'',
			  			"is_on_sale_name":'',
			  			"cat_name":'',
			  			"goods_img":'',
			  			"cat_id":'',
			        }
		      },
		      //确认修改商品
			    updateGoods(){
			      	var that = this;
			      	that.updataloading = true;
				     that.form.is_on_sale = that.form.is_on_sale_name == "上架" ? 1 : 0;
				  	 $.post(this.localhost+'/business/goods/update',that.form,function(data){;
				          if(data.code == 200){
				          	//更新成功
					  		that.dialogFormVisible = false;
					  		that.updataloading = false;
				          	that.$message({
					          message: '修改成功',
					          type: 'success'
					        });
					        //更新数据
				          	data.goods.is_on_sale_name =data.goods.is_on_sale == 1 ? "上架" : "下架";
					        that.$set(that.tableData,that.form.goods_index,{
					        		"goods_id":data.goods.goods_id,
				          			"goods_name":data.goods.goods_name,
				          			"goods_price":data.goods.goods_price,
				          			"goods_desc":data.goods.goods_desc,
				          			"goods_number":data.goods.goods_number,
				          			"goods_thumb":data.goods.goods_thumb,
				          			"is_on_sale":data.goods.is_on_sale,
				          			"is_on_sale_name":data.goods.is_on_sale_name,
				          			"cat_name":data.goods.cat_name,
				          			"cat_id":data.goods.cat_id
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
			    //确认上传商品
			    storeGoods(){
			      	var that = this;
			      	that.storeloading = true;
				     that.storeForm.is_on_sale = that.storeForm.is_on_sale_name == "上架" ? 1 : 0;
				  	 $.post(this.localhost+'/business/goods/store',that.storeForm,function(data){
				          if(data.code == 200){
				          	//更新成功
					  		that.dialogStore = false;
					  		that.storeloading = false;
				          	that.$message({
					          message: '上传成功',
					          type: 'success'
					        });
					        //更新数据
				          	data.goods.is_on_sale_name =data.goods.is_on_sale == 1 ? "上架" : "下架";
					        that.tableData.push({
					        		"goods_id":data.goods.goods_id,
				          			"goods_name":data.goods.goods_name,
				          			"goods_price":data.goods.goods_price,
				          			"goods_desc":data.goods.goods_desc,
				          			"goods_number":data.goods.goods_number,
				          			"goods_thumb":data.goods.goods_thumb,
				          			"is_on_sale":data.goods.is_on_sale,
				          			"is_on_sale_name":data.goods.is_on_sale_name,
				          			"cat_name":data.goods.cat_name,
				          			"cat_id":data.goods.cat_id
					        })
				          }else{
				          	that.storeloading = false;
				          	that.$message.error(data.detail);
				          }
				          
				        	
				          }).error(function(){
				          	that.storeloading = false;
				             that.$message.error('服务器开小差了');
				          })

			    },
			  //分页获取数据
				getData(page){
				  	var that = this;
				   	that.loading = true;
				  	 $.getJSON(this.localhost+'/business/goods/getGoodses?page='+page,function(data){
				          that.loading = false;
				          that.productTablePage = parseInt(data.count);
				          that.tableData = [];
				          $.each(data.goods ,function(a,b){
				          		b.is_on_sale_name = b.is_on_sale == 1 ? "上架" : "下架";
				          		var tableArray = {
				          			"goods_id":b.goods_id,
				          			"goods_name":b.goods_name,
				          			"goods_price":b.goods_price,
				          			"goods_desc":b.goods_desc,
				          			"goods_number":b.goods_number,
				          			"goods_thumb":b.goods_thumb,
				          			"is_on_sale_name":b.is_on_sale_name,
				          			"cat_name":b.cat_name,
				          			"cat_id":b.cat_id
				          		}
				          		that.tableData.push(tableArray);
				          })
			          }).error(function(){
			            that.loading = false;
				        that.$message.error('服务器开小差了');
			          })
				},
				//删除商品
				deleGoods(index,goods_id){
					var that = this;
					that.loading = true;
					$.post(this.localhost+'/business/goods/delete?goods_id='+goods_id,function(data){
			           if(data.code == 200){
			           	 that.loading = false;
				           that.$message({
					            type: 'success',
					            message: '删除成功!'
					         });
				         that.tableData.splice(index,1);
				     }else{
				     	 that.loading = false;
				           that.$message({
					            type: 'error',
					            message: data.detail
					         });
				     }
			          

			        console.log(that.tableData)
			          }).error(function(){
			             that.loading = false;
				        that.$message.error('服务器开小差了');
			          })
					
				},
			    PThandleDelete(index, row) {
			        var that = this;
			         this.$confirm('永久删除该商品?', '校汇', {
			          confirmButtonText: '确定',
			          cancelButtonText: '取消',
			          type: 'warning'
			        }).then(() => {
						that.deleGoods(index,row.goods_id)
			          
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
		       handleIconClick(ev) {
			      console.log(ev);
			    },
			  filterTag(value, row) {
		        return row.cat_name === value;
		      },
		      filterState(value, row) {
		        return row.is_on_sale_name === value;
		      },
		      handleAvatarScucess(res, file) {
		        this.form.goods_thumb = res.thumb_url;
		        this.form.goods_img = res.url;
		        this.storeForm.goods_thumb = res.thumb_url;
		        this.storeForm.goods_img = res.url
		      },
		      beforeAvatarUpload(file) {
		        const isJPG = file.type === 'image/jpeg' || 'image/png';
		        const isLt2M = file.size / 1024 / 1024 < 2;

		        if (!isJPG) {
		          this.$message.error('上传头像图片只能是 JPG 格式!');
		        }
		        if (!isLt2M) {
		          this.$message.error('上传头像图片大小不能超过 2MB!');
		        }
		        return isJPG && isLt2M;
		      }
		  	},
		created:function(){
		  	//获取列表
		  	var that = this;
		  	 $.getJSON(this.localhost+'/business/goods/getGoodses?page=1',function(data){
		          that.loading = false;
		          that.productTablePage = parseInt(data.count);
		          $.each(data.goods,function(a,b){
		          		b.is_on_sale_name = b.is_on_sale == 1 ? "上架" : "下架";
		          		var tableArray = {
		          			"goods_id":b.goods_id,
		          			"goods_name":b.goods_name,
		          			"goods_price":b.goods_price,
		          			"goods_desc":b.goods_desc,
		          			"goods_number":b.goods_number,
		          			"goods_thumb":b.goods_thumb,
		          			"is_on_sale_name":b.is_on_sale_name,
		          			"cat_name":b.cat_name,
		          			"cat_id":b.cat_id
		          		}
		          		that.tableData.push(tableArray);
		          })
		           $.each(data.categories,function(a,b){
		          		var categories = {
		          			"id":b.cat_id,
		          			"text":b.cat_name,
		          			"value":b.cat_name,
		          		}
		          		that.filters.push(categories);
		          })
		          // that.$data.tableData3=
		        	
		          }).error(function(){
		            
		          })
		},
	data: function() {
		return {
			tableData: [],
		    multipleSelection: [],
		    filters:[],
		    state:[{"text":"上架","value":"上架"},{"text":"下架","value":"下架"}],
		    loading:true,
		    input2:"",
		    productTablePage:0,
		    dialogFormVisible:false,
		     form: {
		        "goods_name":'',
	  			"goods_price":'',
	  			"goods_desc":'',
	  			"goods_number":'',
	  			"goods_thumb":'',
	  			"is_on_sale_name":'',
	  			"cat_name":'',
	  			"goods_id":'',
	  			"goods_img":'',
	  			"cat_id":'',

	        },
	        storeloading:false,
		    dialogStore:false,
	        storeForm: {
		        "goods_name":'',
	  			"goods_price":'',
	  			"goods_desc":'',
	  			"goods_number":'',
	  			"goods_thumb":'',
	  			"is_on_sale_name":'',
	  			"cat_name":'',
	  			"goods_img":'',
	  			"cat_id":'',

	        },
	        formLabelWidth: '120px',
	        updataloading:false,
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
// 店铺设置
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
			  	this.$refs[formName].validate((valid) => {
		          if (valid) {
			  		that.loading = true;
		            $.post(this.localhost+'/business/shop/updateShop',that.shopInfo,function(data){
			  				if(data.code == 200){	
			  					that.loading = false;
				            	that.$message.success('修改成功');
			  				}else{
			  					that.$message.error(data.detail);
			  				}
				          }).error(function(){
				            that.$message.error('服务器开小差了');
				          })
		          } else {
				    that.$message.error('你的店铺资料有误，请检查');
		            
		            return false;
		          }
		        });
			  }
			 },
				created:function(){
			  	//获取列表
			  	var that = this;
			  		$.getJSON(this.localhost+'/business/user/getShop',function(data){
			  				that.loading = false;
			  				if(data.code == 200){
			  					that.shopInfo = {
				          			"shop_id":data.shop.shop_id,
				          			"shop_name":data.shop.shop_name,
				          			"shop_img":data.shop.shop_img,
				          			"description":data.shop.description,
				          			"min_goods_amount":data.shop.min_goods_amount,
				          			"shipping_fee":data.shop.shipping_fee,
				          			"open_time":data.shop.open_time,
				          			"close_time":data.shop.close_time,
				          			"shop_status":data.shop.shop_status,
				          			"shop_status_flag":''
				          		}
				          		 that.shopInfo["shop_status_flag"] = that.shopInfo.shop_status == 1 ? true : false;
			  				}else{
			  					that.$message.error(data.detail);
			  				}
				          }).error(function(){
				            that.$message.error('服务器开小差了');
				          })
					},
				data: function() {
					      var validateMGA = (rule, value, callback) => {
					        if (value === '') {
					          callback(new Error('请输入最低购买价格'));
					        }else {
					          callback();
					        }
					      };
					      var validateSF = (rule, value, callback) => {
					        if (value === '') {
					          callback(new Error('请输入最低购买价格'));
					        }else {
					          callback();
					        }
					      };
					return {
						orderData: [],
					    loading:true,
					    shopInfo: {
				         	"shop_id":"",
		          			"shop_name":"",
		          			"shop_img":"",
		          			"description":"",
		          			"min_goods_amount":"",
		          			"shipping_fee":"",
		          			"open_time":"",
		          			"close_time":"",
		          			"shop_status":"",
		          			"shop_status_flag":false
				        },

				        rules: {
				          min_goods_amount: [
				            { validator: validateMGA, trigger: 'blur' }
				          ],
				          shipping_fee: [
				            { validator: validateSF, trigger: 'blur' }
				          ],
				        }
					}
				}
});


