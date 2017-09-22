// 注册
Vue.component('a-logo', {
  template: '<div class="logo"><a href="index.html"></a></div>'
})
// Vue.prototype.localhost = "http://xhplus.feibu.info/";
Vue.prototype.localhost = "http://xhapi.com/";
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

var home = Vue.extend({
	template: '#home',
	created:function(){
	  	//获取列表
	  	var that = this;
	  	 $.getJSON(this.localhost+'telecom/statistics',function(data){
	          that.loading = false;
	         	that.count=data.count;
				that.count_yk=data.count_yk;
				that.count_zc=data.count_zc;
				that.today_count=data.today_count;
				that.today_count_yk=data.today_count_yk;
				that.today_count_zc=data.today_count_zc;
	          }).error(function(){
	            that.$message.error('服务器开小差了');
	          })
			},
	data: function() {
		return {
			count:0,
			count_yk:0,
			count_zc:0,
			today_count:0,
			today_count_yk:0,
			today_count_zc:0,
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
