(function (doc, win,fs) {
  var docEl = doc.documentElement,
  resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize';
  recalc = function () {
    var clientWidth = docEl.clientWidth;
    if (!clientWidth)
    {
      return;
    } 
    else if(clientWidth>750){
      docEl.style.fontSize = 100 + 'px';
    }
    else if(clientWidth<=750)
    {
      docEl.style.fontSize = (clientWidth / 7.5) + 'px';
    }
  };

  if (!doc.addEventListener) return;
  win.addEventListener(resizeEvt, recalc, false);
  doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);

  var locahost ="http://xhplus.feibu.info";
 //var locahost ="http://xh.feibu.info";
 // var locahost ="http://192.168.0.9:8084";
 // var locahost ="http://192.168.0.99:8080/xh/server.php";
var tab =window.location.hash.replace(/#\//,"");
var locaTime = 600000; //十分钟  全局缓存
$.ajaxSetup({
          xhrFields: {
                       withCredentials: true
               },
        });
var photoarray = '' ;//头像数组缓存；
var photoarray_thumb = '' ;//头像数组缓存；
//html fontSize 重置



function htmlAddClass(className){
    $('html').addClass(className);
  }

function htmlRemoveClass(className){
    $('html').removeClass(className);
  }


//储存缓存
function setItem(a,b){
   if(window.localStorage){
     var storage = window.localStorage; 
     storage.setItem(a,b);//设置a储存b;
     var time=new Date().getTime(); //获取时间戳
     storage.setItem(a+"time",time);//设置a储存b;
    }
}
//获取缓存
function getItem(a){
   if(window.localStorage){
     var storage = window.localStorage; 
     if(storage.getItem(a) != undefined){
       var b = storage.getItem(a);//获取a的内容;
       var c = storage.getItem(a+"time");//获取a的时间戳;
       return [b,c];  
     }else{
        return false;
     }
    }else{
      return false;
    }
}
//获取缓存并判断是否过期 true:过期 false:未过期
function getItemATime(a){
   if(window.localStorage){
     var storage = window.localStorage; 
     if(storage.getItem(a) != undefined){
       var b = storage.getItem(a);//获取a的内容;
       var c = storage.getItem(a+"time");//获取a的时间戳;
       var now = new Date().getTime();
       var thislocaTime =locaTime;
       if(now-c >= thislocaTime){
          //过期
          return true;
       }else{
        return false;
       }  
     }else{
        return true;
     }
    }else{
      return true;
    }
}
//获取参数
function GetString(name)
{
     var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
     var r = window.location.search.substr(1).match(reg); 
     if(r!=null)return  unescape(r[2]); return null;
}
//时间转时间戳
function toUnix(date){
  if(date == undefined){
    return false;
  }

   date = date.substring(0,19);    
  date = date.replace(/-/g,'/'); 
  return timestamp = new Date(date).getTime();
}
//话题点赞

function thumbUp(obj,topic_id){
  var token = window.localStorage["token"];
  $.post(locahost+'/topic/thumbUp/?token='+token+'&topic_id='+topic_id, function(data){
    if(data.code == 2001){
      fb_alert(fb_error["2001"]);
      window.location.href="login.html";
    }else if(data.code == 110){
      fb_alert(data.detail);
    }
    if(data.code == 200){
      
      if(data.isthumb == 1){
         $(obj).parents(".topic_bottom").find(".zan").text("取消");
      }else if(data.isthumb == '-1'){
         $(obj).parents(".topic_bottom").find(".zan").text("赞");
      }
      $(obj).parents(".topic_bottom").find(".zanN span").text(data.data);
      setItem("topic",$(".topic_list ").html());
    }
  })
}

//删除话题
function deletopic(obj,topic_id){
  alert_flag("确定删除该话题？")
  $(".flag_true").on("click",function(){
    deletopicFun(obj,topic_id);
    $(".flag_A").remove();
  })
}
function deletopicFun(obj,topic_id){
  $(obj).css({"position":"relative","top":$(window).height()/2,"opacity":0})
  var token = window.localStorage["token"];
  $.post(locahost+'/topic/deleteTopic/?token='+token+'&topic_id='+topic_id, function(data){
    if(data.code == 2001){
      fb_alert(fb_error["2001"])
      window.location.href="../login.html";
    }
    if(data.code == 200){
      fb_alert(fb_error["5"]);
      $(obj).parents(".topic_box").animate({"opacity":0},500,function(){
        $(this).remove();
      window.localStorage.removeItem("topicHtml");
      });
    }else{
      fb_alert(code.detail);

    }

  })
}
//删除评论
function delecomment(obj,comment_id){
  alert_flag("确定删除该评论？")
  $(".flag_true").on("click",function(){
    delecommentFun(obj,comment_id);
    $(".flag_A").remove();
  })
}
function delecommentFun(obj,comment_id){
  $(obj).css({"position":"relative","left":$(window).width()/2,"opacity":0})
  var token = window.localStorage["token"];
  $.post(locahost+'/topic/deleteComment/?token='+token+'&comment_id='+comment_id, function(data){
    if(data.code == 2001){
      fb_alert(fb_error["2001"]);
      window.location.href="../login.html";
    }
    if(data.code == 200){
      fb_alert(fb_error["5"]);
      $(obj).remove();
      setItem("topic",$(".topic_list").html()); //重新缓存
    }else{
      fb_alert(data.detail);

    }

  })
}
//返回上一页
function returnUp(){  
  
  window.history.go(-1);
}


//判断是否登陆
function online(){
  var locaTime = 60000000; //1000分钟
  var token =getItem("token");
  var now = new Date().getTime();
  if(now-token[1] >= locaTime){
    window.localStorage.clear("token");
    return false;
  }
  if(token){
     if(now-token[1]< locaTime){
        return true
      }
    return false;
  }
    return false;
}

//分割个人信息数组{
function splitInfo(){
      var info = window.localStorage.info;
      info = info.split("\\");
      var arg = 'address,association_id,avatar_url,birth_day,birth_month,birth_year,college,college_id,enrollment_year,favourites_count,gender,integral,introduction,is_cheif,is_merchant,mobile_no,nickname,openid,realname'.split(",") ;
      var data = [];
     for (var i = info.length - 1; i >= 0; i--) {
       data[arg[i]]= info[i];
     }
     return data;
}

//手机号码正则
function checkMobile(val){
    return /^1[3|4|5|8|7][0-9]{9}$/.test(val);
  }
//全屏加载动画
function loading(flag){
  if(flag){
    //增加动画
    $(".bigloading").remove();
    $("body").append('<div class="bigloading"></div>');
  }else{
    //删除动画
    $(".bigloading").remove();
  }
}
//清除滚动事件
function clearWindowScroll(){
  $("#loading,#loaded").remove();
  $(window).off("scroll")
  $(window).scrollTop(0);
}
//路由判断显示dom
function routDom(obj){
  $(".routes").hide();
  if(obj){
    obj.show();
  }
  
}
//提示框
var fb_time;
function fb_alert(msg){
  clearTimeout(fb_time)
  $(".bottom_aside").remove();
  if(msg == undefined){
    msg = "正在装修中..."
  }
  var html = '<div class="bottom_aside">'+msg+'</div>';
  $("body").append(html);
  fb_time=setTimeout(function(){
    $(".bottom_aside").remove();
  },2000)
}

//是否弹窗
function alert_flag(str){
  var html = '<div class="flag_A"><div class="flag_B">\
                <div class="flag_box_con">'+str+'</div>\
                <div class="flag_close">取消</div>\
                <div class="flag_true">确定</div>\
              </div></div>'
  $("body").append(html);
  $(".flag_close").on("click",function(){
    $(".flag_A").remove();
  })
}
//正在支付
function is_alipay(a){
  if(a){
    var html = '<div class="alipay_alert"></div>';
    $(".alipay_alert").remove();
    $("body").append(html);
  }else{
    $(".alipay_alert").remove();

  }
  
}
//获取时间是什么时候之前  例如1天前  传入格式 2016-08-24 23:23
function afterTime(a){
      var unix = toUnix(a);
      var now = new Date().getTime();
      var unixed = parseInt((now - unix)/1000);
      if(unixed <= 0 ){
        var time = "刚刚";
      }else if(unixed <= 60 ){
        var time = unixed+"秒前";
      }else if(unixed > 60 && unixed < 3600 ){
        var time = parseInt(unixed/60)+"分前";
      }else if(unixed > 3600 && unixed < 86400 ){
        var time = parseInt(unixed/3600)+"小时前";
      }else{
        var time = parseInt(unixed/3600/24)+"天前";
      }
      return time
}
// 是否实名
function is_auth(){
  if(window.localStorage.info != "undefined"){
        var info = JSON.parse(window.localStorage.info);
         if(info.is_auth == 0){
          return false;
        }else if(info.is_auth==1){
          return true;
        }else if(info.is_auth==2){
          fb_alert("正在实名中...")
          return false;
        }
    }else{
        return false;
    }
}
//滚动到下方刷新
function addScroll(){

}
//话题换行
function ClearBr(key) { 
  key = key.replace(/<\/?.+?>/g,"</p><p>"); 
  key = key.replace(/[\r\n]/g, "</p><p>"); 
  return key; 
} 
//获取字符串长度（汉字算1个字符，字母数字算半个）
function getByteLen(val) {
  var len = 0;
  for (var i = 0; i < val.length; i++) {
    var a = val.charAt(i);
    if (a.match(/[^\x00-\xff]/ig) != null) {
      len += 1;
    }
    else {
      len += 0.5;
    }
  }

  return Math.round(len);
}
//错误提示
var fb_error ={
                "0":"校汇等待你的到来",
                "1":"修改成功",
                "2":"接单成功",
                "3":"发布成功",
                "4":"谢谢你的赞，么么哒",
                "5":"删除成功",
                "6":"清除缓存完成",
                "7":"下次登录请使用新密码",
                "8":"请完善支付宝资料",
                "9":"绑定支付宝账号成功",
                "10":"提现成功,1-3个工作日到账",
                "11":"提现金额不可少于10元",
                "12":"请先到我的资料里实名认证",
                "13":"该话题已被删除",
                "14":"请先绑定支付宝",
                "100":"留言不可为空",
                "110":"任务已被接",
                "200":"时间已成过去",
                "300":"地址不可为空",
                "400":"你的手机号码不是地球的",
                "500":"赏金不可低于2元",
                "1000":"两次密码不一样",
                "1001":"支付密码设置成功",
                "1002":"已设置过支付密码",
                "1003":"原支付密码错误",
                "1004":"新密码不可与旧密码相同",
                "1005":"支付密码只能为六位纯数字",
                "1006":"验证码不可为空",
                "1007":"重置支付密码成功",
                "1008":"短信验证码错误，请重新输入或重新获取",
                "1009":"密码不可小于6位",
                "1010":"请先到[我的钱包]设置支付密码",
                "1100":"请完善内容",
                "1101":"反馈成功",
                "2001":"登录失效",
                "2011":"不能接自己的任务",
                "3001":"你已完成任务",
                "3002":"结算完成",
                "3003":"取消任务成功",
                "ass_01":"请完善你的资料",
                "ass_02":"你的手机号码不是地球的",
                "ass_03":"设置成功",
                "ass_04":"操作成功",
                "ass_05":"已提交申请,等待会长审核",
                "ass_06":"不可为空",
                "404":"网络异常",
                "f_001":"请填写学号 密码",
                "f_002":"故障描述不可为空",
                "f_003":"报修成功,请耐心等待网管上门",
              }


      // $.post(locahost+'/user/sendResetPayPasswordSMS/?token='+token,  function(data){
      //      if(data.code == 2001){
      //           fb_alert(fb_error["2001"])
      //           window.location.href = "../login.html";
      //           return;
      //        }
      //        if(data.code == "200"){    
               
      //        }else{
      //            fb_alert(data.detail);    
      //          }
      // }).error(function(xhr,errorText,errorType){
      //         alert('网络超时，请稍后再试')
      //     });
