//html fontSize 重置
(function (doc, win) {
  var docEl = doc.documentElement,
  resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
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
$(function(){
  $(".web-active").on("touchstart",function(){
    $(this).addClass("active");
  })
  $(".web-active").on("touchend",function(){
    $(this).removeClass("active");
  })
})
//兼容APP链接跳转
function winGo(str){
  try {
      if (typeof(eval(JavaScriptInterface))) {
          JavaScriptInterface.openWindow(str)
      }
  } catch(e) {
      window.location.href=str;
  }
}


function winClose(){

  try {
      if (typeof(eval(JavaScriptInterface))) {
          JavaScriptInterface.closeWindow()
      }
  } catch(e) {
      window.history.go(-1)
  }

}
function returnUp(){


      window.history.go(-1)


}