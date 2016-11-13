
var refresh= new Array("home","topic","tape","work","mytopic","mycomment","assList","asActives","jieOrder","faOrder","myAss","Member","SS");
  $(function(){
      //下拉刷新
      htmlUpdata();
      function htmlUpdata(obj){
      $("body").append("<div id='load' class='lIng' style='display:none'></div>")
      var sY=0;var nowY=0;var h=0;
      document.addEventListener('touchstart', touchSatrtFunc, false);
      document.addEventListener('touchmove', touchMoveFunc, false);
      document.addEventListener('touchend', touchEndFunc, false);
      } 
      function touchSatrtFunc(e){

         var touch = e.touches[0]; //获取第一个触点
         var x = Number(touch.pageX); //页面触点X坐标
         var y = Number(touch.pageY); //页面触点Y坐标
         sY = y;
         nowY = y;
         h = 0;
         $("#load").removeClass("led")
      }
      function touchMoveFunc(e,obj){

        var touch = e.touches[0]; //获取第一个触点
        var x = Number(touch.pageX); //页面触点X坐标
        var y = Number(touch.pageY); //页面触点Y坐标
        if(isOne(refresh)){
          if(y-sY <=0){
            nowY = y;
            return;
          }else{
            var sT = $(window).scrollTop();
            if(sT <= 0){
              //到达顶部
              h = y-nowY;
              $("#load").show().css({"transition":"all 0s","-webkit-transition":"all 0s"})
              if(h>=200){
                $("#load").addClass("led")
                $("#load").css({"transform":"translate3d(0,66.66px,0)","-ms-transform":"translate3d(0,66.66px,0)","-moz-transform":"translate3d(0,66.66px,0)","-webkit-transform":"translate3d(0,66.66px,0)","-o-transform":"translate3d(0,66.66px,0)",});
              }else{
                $("#load").removeClass("led")
                $("#load").css({"transform":"translate3d(0,"+h/3+"px,0)","-ms-transform":"translate3d(0,"+h/3+"px,0)","-moz-transform":"translate3d(0,"+h/3+"px,0)","-webkit-transform":"translate3d(0,"+h/3+"px,0)","-o-transform":"translate3d(0,"+h/3+"px,0)",});
              }
             e.preventDefault();
            }
          }
    }
      }
      function touchEndFunc(e){
          $("#load").css({"transition":"all 0.2s","-webkit-transition":"all 0.2s"})
        if(h>=200){
          //ajax
          if(isOne(refresh)){
              $("#loaded").remove();
              refresh[tab](1);
          }else{ 
              $("#load").css({"transform":"translate3d(0,-2rem,0)","-ms-transform":"translate3d(0,-2rem,0)","-moz-transform":"translate3d(0,-2rem,0)","-webkit-transform":"translate3d(0,-2rem,0)","-o-transform":"translate3d(0,-2rem,0)"});
          }
         
        }else{
         $("#load").css({"transform":"translate3d(0,-2rem,0)","-ms-transform":"translate3d(0,-2rem,0)","-moz-transform":"translate3d(0,-2rem,0)","-webkit-transform":"translate3d(0,-2rem,0)","-o-transform":"translate3d(0,-2rem,0)"});
        }
      }
      //判断是否在当前页面
      function isOne(obj){
        var flag=false;
         $.each(obj,function(a,b){
          if(b == tab){
            flag = true;
          }else{
            return;
          }
         })
         return flag;
      }

})
  function clearLoading(){
    if($("#load").length != 0)
      $("#load").css({"transform":"translate3d(0,-2rem,0)","-ms-transform":"translate3d(0,-2rem,0)","-moz-transform":"translate3d(0,-2rem,0)","-webkit-transform":"translate3d(0,-2rem,0)","-o-transform":"translate3d(0,-2rem,0)"});
  }