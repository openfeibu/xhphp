Vue.prototype.localhost = "";
Vue.prototype.pageNum = 20;

var home =  Vue.extend({
  template: '#home',
  created:function(){
    //获取个人资料
    var that = this;
    that.getUser()
    // if(!window.localStorage.userInfo || !window.localStorage.shopInfo ){
    //   that.getUser()
    // }else{
    //  that.userInfo =JSON.parse(window.localStorage.userInfo);
    //  that.shopInfo =JSON.parse(window.localStorage.shopInfo);
    // }
  },
   data:function(){
    return {
          userInfo:{},
          shopInfo:{}
    }
  },
  methods: {
    getUser:function(){
      var that = this;
          that.$indicator.open();
          $.getJSON(this.localhost+'/business/user/getUser',function(data){
               that.$indicator.close();
              if(data.code == 200){
                    that.userInfo = {
                      "nickname":data.user.nickname,
                      "avatar_url":data.user.avatar_url,
                      "wallet":data.user.wallet,
                      "sale_count":data.shop.sale_count,
                      "income":data.shop.income,
                      "todayIncome":data.shop.todayIncome,
                    }
                    window.localStorage.userInfo =JSON.stringify(that.userInfo);
                    window.localStorage.shopInfo =JSON.stringify(data.shop);
              }else{
                that.wloading = true;
                  that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
              }
            }).error(function(){
               that.$indicator.close();
               that.wloading = true;
                  that.$toast({
                      message: '服务器出小差',
                      position: 'bottom',
                      duration: 3000
                    });
            })
    
    }
  },

});

var center =  Vue.extend({
   template: '#center',
   created:function(){
    //获取个人资料
      var that = this;
      if(!window.localStorage.userInfo || !window.localStorage.shopInfo ){
        that.getUser()
      }else{
        that.userInfo =JSON.parse(window.localStorage.userInfo);
        that.shopInfo =JSON.parse(window.localStorage.shopInfo);
        if(that.shopInfo.shop_status == 1){
          that.shopStatus = true;
        }else{
          that.shopStatus = false;
        }
      }
    },
   data:function(){
    return {
          userInfo:{},
          shopInfo:{},
          shopStatus:false,
          openValue:"",
          closeValue:"",
          open_time:"",
          close_time:"",
          hour:"12"
    }
    },
  methods: {
    getUser:function(){
      var that = this;
          that.$indicator.open();
          $.getJSON(this.localhost+'/business/user/getUser',function(data){
               that.$indicator.close();
               if(data.code == 200){
                    that.shopInfo = data.shop;
                    window.localStorage.userInfo =JSON.stringify(that.userInfo);
                    window.localStorage.shopInfo =JSON.stringify(data.shop);
                    if(that.shopInfo.shop_status == 1){
                      that.shopStatus = true;
                    }else{
                      that.shopStatus = false;
                    }
               }else{
                that.wloading = true;
                  that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                }
                    
            }).error(function(){
                 that.$indicator.close();
                that.wloading = true;
                  that.$toast({
                      message: '服务器出小差',
                      position: 'bottom',
                      duration: 3000
                    });
            })
    
      },
     openPicker:function() {
        this.$refs.open.open();
      },
      handleopenValue:function(){
        this.$refs.close.open();
      },
      handlecloseValue:function(){
          var that = this;
          that.$indicator.open("正在修改中");
            that.open_time += ":00";
            that.close_time += ":00";
            $.post(this.localhost+'/business/shop/updateShop',{"open_time":that.open_time,"close_time":that.close_time},function(data){
                that.$indicator.close();
                if(data.code == 200){
                  that.shopInfo.open_time = that.open_time;
                  that.shopInfo.close_time = that.close_time;
                  window.localStorage.removeItem("shopInfo");
                }else{
                  that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                }
               
                }).error(function(){
                   that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })

      },
      changeDes:function(){
          var that = this;
          that.$messagebox.prompt('请输入店铺简介?','校汇').then(function(value) {
              var Data = {
                "description" : value.value
              };
              that.$indicator.open("正在修改中"); 
              $.post(that.localhost+'/business/shop/updateShop',Data,function(data){
                  that.$indicator.close(); 
                  if(data.code == 200){
                    that.shopInfo.description = value.value;
                    window.localStorage.removeItem("shopInfo");
                  }else{
                    that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                  }
                }).error(function(){
                    that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
            });
          setTimeout(function(){
            $(".mint-msgbox-input input").val(that.shopInfo.description)
          },500)
          
        },
      changeStatus:function(){
          var that = this;
          that.$indicator.open("正在修改中");
          if(that.shopStatus){
            var shop_status = 1;
          }else{
            var shop_status = 3;
          }

            $.post(this.localhost+'/business/shop/updateShop',{"shop_status":shop_status},function(data){
                that.$indicator.close();
                if(data.code == 200){
                  window.localStorage.removeItem("shopInfo");
                }else{
                  that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                }
               
                }).error(function(){
                   that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
      },
  },
});

var wfh = Vue.extend({
  template: '#wfh',
   created:function(){
        this.wloadMore();
      },
        data:function(){
        return {
          tabID:"1",
          wpage:0,
          wload:0,  //1有加载  2加载完毕了
          wlist:[],
          wloading:false,         
          wtopStatus:"",
        }
      },
      methods: {
        whandleTopChange:function(status){
          this.wtopStatus = status;
        },
       
        wloadTop : function(){
          this.wloadMore(true);
        },
       
        wloadMore:function(flag) {
              var that = this;
              that.wloading = true;
              that.wpage++;
              if(flag){
                that.wpage = 1;
              }else{
                //有緩存 就跳出
                if(window.localStorage.wlist && JSON.parse(window.localStorage.wlist).length > 0 && that.wpage == 1){
                  that.wlist = JSON.parse(window.localStorage.wlist);
                  return;
                };
                
              }
              that.$indicator.open();
              
              //获取列表
              $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+ that.wpage+'&type=beship',function(data){
                that.$indicator.close();
                if(data.code == 200){
                  if(flag){
                      that.wlist = [];
                      $(".mint-loadmore-content").css({"transform":"matrix(1, 0, 0, 1, 0, 0)","-webkit-transform":"matrix(1, 0, 0, 1, 0, 0)","-o-transform":"matrix(1, 0, 0, 1, 0, 0)","-moz-transform":"matrix(1, 0, 0, 1, 0, 0)","-ms-transform":"matrix(1, 0, 0, 1, 0, 0)"})
                  };
                  that.wloading = false;
                  that.wload = 1;
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
                      that.wlist.push(orderArray);
                  })
                  if(that.wpage == 1){
                    window.localStorage.wlist = JSON.stringify(data.order_infos);
                  }
                  if(data.order_infos.length < that.pageNum ){
                      that.wloading = true;
                      that.wload = 2;
                  }
                }else{
                  that.wloading = true;
                  that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                }
               
                }).error(function(){
                   that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        },

      },

});
// 已发货
var yfh = Vue.extend({
  template: '#yfh',
      created:function(){
        this.yloadMore();
      },
        data:function(){
        return {
          tabID:"1",
          ypage:0,
          yload:0,  //1有加载  2加载完毕了
          ylist:[],
          yloading:false,         
          ytopStatus:"",
        }
      },
      methods: {
        yhandleTopChange:function(status){
          this.ytopStatus = status;
        },
       
        yloadTop : function(){
          this.yloadMore(true);
        },
       
        yloadMore:function(flag) {
              var that = this;
              console.log(1)
              that.ypage++;
              if(flag){
                that.ypage = 1;
              }else{
                //有緩存 就跳出
                if(window.localStorage.ylist && JSON.parse(window.localStorage.ylist).length > 0 && that.ypage == 1){
                  that.ylist = JSON.parse(window.localStorage.ylist);
                  return;
                };
                
              }
              that.yloading = true;
              that.$indicator.open();
              
              //获取列表
              $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+ that.ypage+'&type=shipping',function(data){
                that.$indicator.close();
                if(data.code == 200){
                  if(flag){
                      that.ylist = [];
                      $(".mint-loadmore-content").css({"transform":"matrix(1, 0, 0, 1, 0, 0)","-webkit-transform":"matrix(1, 0, 0, 1, 0, 0)","-o-transform":"matrix(1, 0, 0, 1, 0, 0)","-moz-transform":"matrix(1, 0, 0, 1, 0, 0)","-ms-transform":"matrix(1, 0, 0, 1, 0, 0)"})
                  };
                  that.yloading = false;
                  that.yload = 1;
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
                      that.ylist.push(orderArray);
                  })
                  if(that.ypage == 1){
                    window.localStorage.ylist = JSON.stringify(data.order_infos);
                  }
                  if(data.order_infos.length < that.pageNum  ){
                      that.yloading = true;
                      that.yload = 2;
                  }
                }else{
                  that.wloading = true;
                  that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                   
                }
                }).error(function(){
                             that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        },

      },

});
// 已发货
// 已完成
var ywc = Vue.extend({
  template: '#ywc',
  created:function(){
      var that = this;
           that.cloadMore();
      },
        data:function(){
        return {
          tabID:"1",
          cpage:0,
          cload:0,  //1有加载  2加载完毕了
          clist:[],
          cloading:false,         
          ctopStatus:"",
        }
      },
      methods: {
        chandleTopChange:function(status){
          this.ctopStatus = status;
        },
       
        cloadTop : function(){
          this.cloadMore(true);
        },
       
        cloadMore:function(flag) {
              var that = this;
              that.cloading = true;
              that.cpage++;
              if(flag){
                that.cpage = 1;
              }else{
                //有緩存 就跳出
                if(window.localStorage.clist && JSON.parse(window.localStorage.clist).length > 0 && that.cpage == 1){
                  that.clist = JSON.parse(window.localStorage.clist);
                  return;
                };
                
              }
              
              that.$indicator.open();
              //获取列表
              $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+ that.cpage+'&type=succ',function(data){
                  that.$indicator.close();
                if(data.code == 200){
                  if(flag){
                      that.clist = [];
                      $(".mint-loadmore-content").css({"transform":"matrix(1, 0, 0, 1, 0, 0)","-webkit-transform":"matrix(1, 0, 0, 1, 0, 0)","-o-transform":"matrix(1, 0, 0, 1, 0, 0)","-moz-transform":"matrix(1, 0, 0, 1, 0, 0)","-ms-transform":"matrix(1, 0, 0, 1, 0, 0)"})
                  };
                  that.cloading = false;
                  that.cload = 1;
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
                      that.clist.push(orderArray);
                  })
                  if(that.cpage == 1){
                    window.localStorage.clist = JSON.stringify(data.order_infos);
                  }
                  if(data.order_infos.length < that.pageNum  ){
                      that.cloading = true;
                      that.cload = 2;
                  }
                }else{
                  that.wloading = true;
                    that.$toast({
                        message: data.detail,
                        position: 'bottom',
                        duration: 3000
                      });
                }
                }).error(function(){
                             that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        },

      },

});
// 已完成
// 退货与售后
var thsh = Vue.extend({
  template: '#thsh',
  created:function(){
        this.tloadMore();
      },
        data:function(){
        return {
          tpage:0,
          tload:0,  //1有加载  2加载完毕了
          tlist:[],
          tloading:false,         
          ttopStatus:"",
        }
      },
      methods: {
        thandleTopChange:function(status){
          this.ttopStatus = status;
        },
       
        tloadTop : function(){
          this.tloadMore(true);
        },
       
        tloadMore:function(flag) {
              var that = this;
              that.tloading = true;
              that.tpage++;
              if(flag){
                that.tpage = 1;
              }else{
                //有緩存 就跳出
                if(window.localStorage.tlist && JSON.parse(window.localStorage.tlist).length > 0 && that.tpage == 1){
                  that.tlist = JSON.parse(window.localStorage.tlist);
                  return;
                };
                
              }
              that.$indicator.open();
              that.tpage++;
              //获取列表
              $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+ that.tpage+'&type=cancell',function(data){
                  that.$indicator.close();
                if(data.code == 200){
                  if(flag){
                      that.tlist = [];
                      $(".mint-loadmore-content").css({"transform":"matrix(1, 0, 0, 1, 0, 0)","-webkit-transform":"matrix(1, 0, 0, 1, 0, 0)","-o-transform":"matrix(1, 0, 0, 1, 0, 0)","-moz-transform":"matrix(1, 0, 0, 1, 0, 0)","-ms-transform":"matrix(1, 0, 0, 1, 0, 0)"})
                  };
                  that.tloading = false;
                  that.tload = 1;
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
                      that.tlist.push(orderArray);
                  })
                  if(that.tpage == 1){
                    window.localStorage.tlist = JSON.stringify(data.order_infos);
                  }
                  if(data.order_infos.length < that.pageNum  ){
                      that.tloading = true;
                      that.tload = 2;
                  }
                }else{
                  that.wloading = true;
                      that.$toast({
                          message: data.detail,
                          position: 'bottom',
                          duration: 3000
                        });
                }
                }).error(function(){
                             that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        },

      },

});
// 退货与售后
// 未发货详情
var worderDe = Vue.extend({
      template: '#worderDe',
      created:function(){
        var that = this;
        var index = that.$route.params.id;
        $(window).scrollTop(0)
        if(window.localStorage.wlist && JSON.parse(window.localStorage.wlist).length >= index                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ){
          that.nowwlist = JSON.parse(window.localStorage.wlist)[index];
        }else{
          //没有缓存，先获取列表
        
          var page = index%that.pageNum  == 0 && index != 0 ? index/that.pageNum  : parseInt(index/that.pageNum )+1;

           that.$indicator.open();
            $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+page+'&type=beship',function(data){
                  that.$indicator.close();
                  if(page == 1){
                    window.localStorage.wlist = JSON.stringify(data.order_infos);
                  }
                  index = index-(page-1)*that.pageNum ;
                  that.nowwlist = data.order_infos[index];
                 
                }).error(function(){
                    that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        }

      },
      data:function(){
        return {
          nowwlist:[],
          popupVisible:false
        }
      },
      methods: {
        delivery:function(order_id){

          var that = this;
          that.$messagebox.confirm('货物已打包,确定发货？').then(function(action){
              that.deliveryGoods(order_id)
          });
        },
         //发货
        deliveryGoods:function(order_id){
          var that = this;
           that.$indicator.open();
          $.post(this.localhost+'/business/orderInfo/shipping?order_id='+order_id,function(data){
                if(data.code == 200 ){
                   that.$indicator.close();
                    that.$toast({
                      message: '发货成功',
                      iconClass: 'icon icon-success'
                    });
                    window.localStorage.removeItem("wlist"); 
                    window.history.go(-1);
                } else{
                  that.$indicator.close();
                   that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                } 
                }).error(function(){
                 that.$indicator.close();
                   that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
          
        },
      },
       watch:{

        }

});
// 未发货详情
// 已发货详情
var yorderDe = Vue.extend({
      template: '#yorderDe',
      created:function(){
        var that = this;
        var index = that.$route.params.id;
        $(window).scrollTop(0)
        if(window.localStorage.ylist && JSON.parse(window.localStorage.ylist).length >= index){
          that.nowylist = JSON.parse(window.localStorage.ylist)[index];
        }else{
          //没有缓存，先获取列表
          var page = index%that.pageNum  == 0 ? index/that.pageNum  : parseInt(index/that.pageNum )+1;
           that.$indicator.open();
            $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+page+'&type=shipped',function(data){
                  that.$indicator.close();
                  if(page == 1){
                    window.localStorage.ylist = JSON.stringify(data.order_infos);
                  }
                  index = index-(page-1)*that.pageNum ;
                  that.nowylist = data.order_infos[index];
                }).error(function(){
                             that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        }

      },
      data:function(){
        return {
          nowylist:[]
        }
      },
      methods: {
        // delivery:function(){
        //   var that = this;
        //   that.$messagebox.confirm('货物已打包,确定发货？').then(function(action){
              
        //   });
        // }
      },


});
// 已发货详情
// 已完成详情
var corderDe = Vue.extend({
      template: '#corderDe',
      created:function(){
        var that = this;
        var index = that.$route.params.id;
        $(window).scrollTop(0)
        if(window.localStorage.clist && JSON.parse(window.localStorage.clist).length >= index                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ){
          that.nowclist = JSON.parse(window.localStorage.clist)[index];
        }else{
          //没有缓存，先获取列表
          var page = index%that.pageNum  == 0 ? index/that.pageNum  : parseInt(index/that.pageNum )+1;
           that.$indicator.open();
            $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+page+'&type=succ',function(data){
                  that.$indicator.close();
                  if(page == 1){
                    window.localStorage.clist = JSON.stringify(data.order_infos);
                  }
                  index = index-(page-1)*that.pageNum ;
                  that.nowclist = data.order_infos[index];
                }).error(function(){
                             that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        }

      },
      data:function(){
        return {
          nowclist:[]
        }
      },
      methods: {
        // delivery:function(){
        //   var that = this;
        //   that.$messagebox.confirm('货物已打包,确定发货？').then(function(action){
              
        //   });
        // }
      },


});
// 已完成详情
// 退货与售后
var torderDe = Vue.extend({
      template: '#torderDe',
      created:function(){
        var that = this;
        var index = that.$route.params.id;
        $(window).scrollTop(0)
        if(window.localStorage.tlist && JSON.parse(window.localStorage.tlist).length >= index                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 ){
          that.nowtlist = JSON.parse(window.localStorage.tlist)[index];
        }else{
          //没有缓存，先获取列表
          var page = index%that.pageNum  == 0 ? index/that.pageNum  : parseInt(index/that.pageNum )+1;
           that.$indicator.open();
            $.getJSON(this.localhost+'/business/orderInfo/orderInfos?page='+page+'&type=cancell',function(data){
                  that.$indicator.close();
                  if(page == 1){
                    window.localStorage.tlist = JSON.stringify(data.order_infos);
                  }
                  index = index-(page-1)*that.pageNum ;
                  that.nowtlist = data.order_infos[index];
                }).error(function(){
                      that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        }

      },
      data:function(){
        return {
          nowtlist:[],
          popupVisible:false
        }
      },
      methods: {
        returnMoney:function(order_id){
          var that = this;
          that.$messagebox.confirm('已协商好，确定退款？').then(function(action){
              that.refundGoods(order_id)
          });
        },
        //退款
        refundGoods:function(order_id){
          var that = this;
          that.$indicator.open();
          $.post(this.localhost+'/business/orderInfo/agreeCancel?order_id='+order_id,function(data){
                if(data.code == 200 ){
                  that.$indicator.close();
                  that.popupVisible = true;
                  setTimeout(function(){
                     that.popupVisible = false;
                  },2000)
                  window.localStorage.removeItem("tlist"); 
                } else{
                  that.$indicator.close();
                   that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                } 
                }).error(function(){
                   that.$indicator.close();
                   that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })  
        },
      },


});
// 退货与售后

//  商品列表
var product = Vue.extend({
   template: '#product',
   created:function(){
          var that = this;
          that.tloadMore(true);
          //获取分类
          that.$indicator.open();
          $.getJSON(that.localhost+'/business/goods/getCats',function(data){
            that.$indicator.close(); 
            if(data.code == 200){
              that.productClassAll = data.cats;
            }else{
              that.$toast({
                message: data.detail,
                position: 'bottom',
                duration: 3000
              });
            }
            
          }).error(function(){
              that.$toast({
                message: '服务器开小差',
                position: 'bottom',
                duration: 3000
              });
          })
   },
    data:function(){
          return {
            tpage:0,
            tload:0,  //1有加载  2加载完毕了
            list:[],
            loading:false,         
            ttopStatus:"",
            productClassAll:[],
            catId: window.localStorage.catId || "all"
          }
      },
      methods: {
        thandleTopChange:function(status){
          this.ttopStatus = status;
        },
       
        tloadTop : function(){
          this.tloadMore(true);
        },
       
        tloadMore:function(flag) {
              var that = this;
              that.loading = true;
              that.tpage++;
              if(flag){
                that.tpage = 1;
              }else{
                //有緩存 就跳出
                if(window.localStorage.list && JSON.parse(window.localStorage.list).length > 0 && that.tpage == 1){
                  that.list = JSON.parse(window.localStorage.list);
                  return;
                };
                
              }
              that.$indicator.open();    
              //获取列表
              if(that.catId != 'all'){
                var prodata = {
                  'page':that.tpage,
                  'cat_id':that.catId
                }
              }else{
                var prodata = {
                  'page':that.tpage,
                }
              }
              $.getJSON(this.localhost+'/business/goods/getGoodses',prodata,function(data){
                that.$indicator.close();
                that.$refs.loadmore.onBottomLoaded()
                // console.log(this.$refs)
                // console.log(this.$refs.$loadmore)
                // this.$refs.loadmore.onBottomLoaded();
                if(flag){
                    that.list = [];
                    // $(".mint-loadmore-content").css({"transform":"matrix(1, 0, 0, 1, 0, 0)","-webkit-transform":"matrix(1, 0, 0, 1, 0, 0)","-o-transform":"matrix(1, 0, 0, 1, 0, 0)","-moz-transform":"matrix(1, 0, 0, 1, 0, 0)","-ms-transform":"matrix(1, 0, 0, 1, 0, 0)"})
                };
                
                that.loading = false;
                that.tload = 1;
                $.each(data.goods,function(a,b){
                  that.list.push(b);
                })
                
                if(that.tpage == 1){
                  window.localStorage.list = JSON.stringify(data.goods);
                }

                if(data.goods.length < that.pageNum  ){
                    that.loading = true;
                    that.tload = 2;
                }

                }).error(function(){
                    that.$indicator.close();
                    that.wloading = true;
                    that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
        },
        changeClass :function(){
          window.localStorage.catId = this.catId;
          this.tloadMore(true);
        },
        change_is_on_sale: function(goods_id,is_on_sale,index){
            var that = this;
            if(is_on_sale){
              var con = "是否确定上架？"
            }else{
              var con = "是否确定下架？"
            }
            that.$messagebox.confirm(con).then(function(value,action) {
                that.$indicator.open("正在修改中");
                $.post(that.localhost+'/business/goods/update',{"goods_id":goods_id,"is_on_sale":is_on_sale},function(data){
                  that.$indicator.close();
                  if(data.code == 200){
                    that.list[index].is_on_sale = is_on_sale;
                    that.$toast({
                      message: '修改成功',
                      position: 'bottom',
                      duration: 3000
                    });
                  }else{
                    that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                  }
                  
                }).error(function(){
                    that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
            })
        },
        deletePro:function(goods_id,index){
           var that = this;
           that.$messagebox.confirm("确定删除该商品？").then(function(value,action) {
                that.$indicator.open("正在删除中");
                $.post(that.localhost+'/business/goods/delete',{"goods_id":goods_id},function(data){
                  that.$indicator.close();
                  if(data.code == 200){
                    that.list.splice(index,1);
                    that.$toast({
                      message: '修改成功',
                      position: 'bottom',
                      duration: 3000
                    });
                  }else{
                    that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                  }
                  
                }).error(function(){
                    that.$toast({
                      message: '服务器开小差',
                      position: 'bottom',
                      duration: 3000
                    });
                })
            })
        }
      },

});
// 商品列表
// 商品详情
var productDe = Vue.extend({
      template: '#productDe',
      created:function(){
        var that = this;
        var goods_id = that.$route.params.id;0

        $(window).scrollTop(0)
        //没有缓存，先获取列表
         that.$indicator.open();
         function getproInfo(){
          $.getJSON(that.localhost+'/goods/getGoods?goods_id='+goods_id,function(data){
                that.$indicator.close();
                if(data.code == 200){
                  that.productDeList = data.data;
                  if(that.productDeList.is_on_sale == 1){
                    that.isOnSale = true;
                  }else{
                    that.isOnSale = false;

                  }
                }else{
                  that.$toast({
                    message: data.detail,
                    position: 'bottom',
                    duration: 3000
                  });
                }
                
              }).error(function(){
                  that.$toast({
                    message: '服务器开小差',
                    position: 'bottom',
                    duration: 3000
                  });
              })
         }
              //获取分类
          $.getJSON(that.localhost+'/business/goods/getCats',function(data){
            if(data.code == 200){
              that.productClassAll = data.cats;
              getproInfo();
            }else{
              that.$toast({
                message: data.detail,
                position: 'bottom',
                duration: 3000
              });
            }
            
          }).error(function(){
              that.$toast({
                message: '服务器开小差',
                position: 'bottom',
                duration: 3000
              });
          })

      },
      data:function(){
        return {
          productDeList:{},
          popupVisible:false,
          productClassAll:[],
          isOnSale:false,
          proImg_thumb:'',
          proImg:''
        }
      },
      methods: {
       updataPro:function(){
        var that = this;
        if(that.productDeList.goods_name == ''){
          that.$toast({
                message: '请填写商品名称',
                duration: 3000
            });
          return false;
        }else if(parseInt(that.productDeList.goods_price) <= 0 || isNaN(that.productDeList.goods_price)){
          that.$toast({
                message: '商品价格不可为空并且必须为数字',
                duration: 3000
            });
          return false;
        }else if(parseInt(that.productDeList.goods_number) < 0 || isNaN(that.productDeList.goods_number)){
          that.$toast({
                message: '商品库存必须为数字',
                duration: 3000
            });
          return false;
        }
        if(that.isOnSale){
          that.productDeList.is_on_sale = 1;
        }else{
          that.productDeList.is_on_sale = 0;
        }
        if(that.proImg != ''){
          that.productDeList.goods_img = that.proImg
        }
        if(that.proImg_thumb != ''){
          that.productDeList.goods_thumb = that.proImg_thumb
        }
          that.$indicator.open("正在修改中");
          $.post(that.localhost+'/business/goods/update',that.productDeList,function(data){
            that.$indicator.close();
            if(data.code == 200){
              window.localStorage.removeItem("list"); 
              that.$toast({
                message: '修改成功',
                position: 'bottom',
                duration: 3000
              });
            }else{
              that.$toast({
                message: data.detail,
                position: 'bottom',
                duration: 3000
              });
            }
            
          }).error(function(){
              that.$toast({
                message: '服务器开小差',
                position: 'bottom',
                duration: 3000
              });
          })
       },
       change : function(){
          var that = this;
          var field=new upField($('#bookpic'));
          var maxSize=10240; //kb
          var name=$('#bookpic').attr('name');
          var pic = $('#bookpic').prop('files');
          var fordata=new FormData();
                    console.log(pic)
          fordata.append('uploadfile',pic[0]); //添加字段

          if(pic.length == 0) return;
            that.$indicator.open("开始上传");
       
          $.ajax({
              url:that.localhost+'/business/goods/uploadGoodsImage',
              type:'POST',
              dataType:'json',
              data:fordata,
              processData: false,
              contentType: false,
              error: function(){
                that.$toast({
                  message: '未知错误',
                  position: 'bottom',
                  duration: 3000
                });
              },  
              success: function(data){
                if(data.code == 200){
                   $('[data-name="pro-detail-img"]').attr("src",data.thumb_url);
                   that.proImg_thumb = data.thumb_url;
                   that.proImg = data.url;
                   that.productDeList.goods_thumb = that.proImg_thumb;
                   that.$indicator.close();
                }else{  
                   that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                }
              }
          })

      },
      },


});
// 商品详情
// 商品详情
var addProduct = Vue.extend({
      template: '#addProduct',
      created:function(){
        var that = this;
        var goods_id = that.$route.params.id;0

        $(window).scrollTop(0)
        //没有缓存，先获取列表
         that.$indicator.open();
          //获取分类
          $.getJSON(that.localhost+'/business/goods/getCats',function(data){
            that.$indicator.close();
            if(data.code == 200){
              that.productClassAll = data.cats;
              that.productDeList.cat_id = data.cats[0]["cat_id"];
            }else{
              that.$toast({
                message: data.detail,
                position: 'bottom',
                duration: 3000
              });
            }
            
          }).error(function(){
              that.$toast({
                message: '服务器开小差',
                position: 'bottom',
                duration: 3000
              });
          })

      },
      data:function(){
        return {
          productDeList:{
            "goods_thumb":"http://web.feibu.info/images/ass_add.png",
            "goods_name":"",
            "goods_price":"",
            "goods_number":"",
            "cat_id":"",
            "goods_desc":""
          },
          popupVisible:false,
          productClassAll:[],
          isOnSale:false,
          proImg_thumb:'',
          proImg:''
        }
      },
      methods: {
       updataPro:function(){
        var that = this;
        if(that.productDeList.goods_name == ''){
          that.$toast({
                message: '请填写商品名称',
                duration: 3000
            });
          return false;
        }else if(parseInt(that.productDeList.goods_price) <= 0 || isNaN(that.productDeList.goods_price)){
          that.$toast({
                message: '商品价格不可为空并且必须为数字',
                duration: 3000
            });
          return false;
        }else if(parseInt(that.productDeList.goods_number) < 0 || isNaN(that.productDeList.goods_number)){
          that.$toast({
                message: '商品库存必须为数字',
                duration: 3000
            });
          return false;
        }
        if(that.isOnSale){
          that.productDeList.is_on_sale = 1;
        }else{
          that.productDeList.is_on_sale = 0;
        }
        if(that.proImg != ''){
          that.productDeList.goods_img = that.proImg
        }
        if(that.proImg_thumb != ''){
          that.productDeList.goods_thumb = that.proImg_thumb
        }
          that.$indicator.open("正在上传中");
          $.post(that.localhost+'/business/goods/store',that.productDeList,function(data){
            that.$indicator.close();
            if(data.code == 200){
              window.localStorage.removeItem("list"); 
              that.$toast({
                message: '上传成功',
                position: 'bottom',
                duration: 3000
              });
              // window.history.go(-1);
            }else{
              that.$toast({
                message: data.detail,
                position: 'bottom',
                duration: 3000
              });
            }
            
          }).error(function(){
              that.$toast({
                message: '服务器开小差',
                position: 'bottom',
                duration: 3000
              });
          })
       },
       change : function(){
          var that = this;
          var field=new upField($('#bookpic'));
          var maxSize=10240; //kb
          var name=$('#bookpic').attr('name');
          var pic = $('#bookpic').prop('files');
          var fordata=new FormData();
                    console.log(pic)
          fordata.append('uploadfile',pic[0]); //添加字段

          if(pic.length == 0) return;
            that.$indicator.open("开始上传");
       
          $.ajax({
              url:that.localhost+'/business/goods/uploadGoodsImage',
              type:'POST',
              dataType:'json',
              data:fordata,
              processData: false,
              contentType: false,
              error: function(){
                that.$toast({
                  message: '未知错误',
                  position: 'bottom',
                  duration: 3000
                });
              },  
              success: function(data){
                if(data.code == 200){
                   $('[data-name="pro-detail-img"]').attr("src",data.thumb_url);
                   that.proImg_thumb = data.thumb_url;
                   that.proImg = data.url;
                   that.productDeList.goods_thumb = that.proImg_thumb;
                   that.$indicator.close();
                }else{  
                   that.$toast({
                      message: data.detail,
                      position: 'bottom',
                      duration: 3000
                    });
                }
              }
          })

      },
      },


});
// 商品详情

// 分类管理
var classify = Vue.extend({
   template: '#classify',
   created:function(){
          var that = this;
          //获取分类
          that.$indicator.open();
          $.getJSON(that.localhost+'/business/goods/getCats',function(data){
            that.$indicator.close(); 
            if(data.code == 200){
              that.productClassAll = data.cats;
            }else{
              that.$toast({
                message: data.detail,
                position: 'bottom',
                duration: 3000
              });
            }
            
          }).error(function(){
              that.$toast({
                message: '服务器开小差',
                position: 'bottom',
                duration: 3000
              });
          })
   },
    data:function(){
          return {
            productClassAll:[],
          }
      },
      methods: {
        updataClassify : function(catId,catName,index){
          var that = this;
          that.$messagebox.prompt('请输入类名',catName).then(function(value) {
            var classData = {
              "cat_name" : value.value,
              "cat_id" : catId
            };
            that.$indicator.open("正在修改中"); 
            $.post(that.localhost+'/business/goods/updateCat/',classData,function(data){
                that.$indicator.close(); 
                if(data.code == 200){
                  that.productClassAll[index].cat_name = value.value;
                }else{
                  that.$toast({
                    message: data.detail,
                    position: 'bottom',
                    duration: 3000
                  });
                }
              }).error(function(){
                  that.$toast({
                    message: '服务器开小差',
                    position: 'bottom',
                    duration: 3000
                  });
              })
          });
        },
        deleteClassify: function(catId,catName,index){
          var that = this;
          that.$messagebox.confirm('是否删除该类名',catName).then(function(){
            var classData = {
              "cat_id" : catId
            };
            that.$indicator.open("正在删除中"); 
            $.post(that.localhost+'/business/goods/deleteCat',classData,function(data){
                that.$indicator.close(); 
                if(data.code == 200){
                  that.productClassAll.splice(index, 1)
                }else{
                  that.$toast({
                    message: data.detail,
                    position: 'bottom',
                    duration: 3000
                  });
                }
              }).error(function(){
                  that.$toast({
                    message: '服务器开小差',
                    position: 'bottom',
                    duration: 3000
                  });
              })
          });
        },

        addClassify :function(){
           var that = this;
          that.$messagebox.prompt('输入新类名','校汇').then(function(value) {
            var classData = {
              "cat_name" : value.value
            };
            that.$indicator.open("正在添加中"); 
            $.post(that.localhost+'/business/goods/addCat',classData,function(data){
                that.$indicator.close(); 
                if(data.code == 200){
                  that.productClassAll.push(data.cats)
                }else{
                  that.$toast({
                    message: data.detail,
                    position: 'bottom',
                    duration: 3000
                  });
                }
              }).error(function(){
                  that.$toast({
                    message: '服务器开小差',
                    position: 'bottom',
                    duration: 3000
                  });
              })
          });
        }
      },

});
// 分类管理