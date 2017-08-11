 // JavaScript Document
/**
 * Created by Administrator on 2016/8/26.
 */

function upField(doc){
    var self=this;
    this.waitTime=3000;  //错误信息 显示时长
    this.doc=doc;

    this.addErr = function (message) {
        var error=this.doc.parent(".img").find('.error');
        error.html(message).show();
        setTimeout(function () {
            error.html('').hide();
        },3000)
    };
	
    this.add=function (img,name) {
		  var template='<div class="img_close" name="'+name+'"></div>';
		  template+='<img src="'+img+'" alt="">';
		  this.doc.parent(".img").html(template);
		  //添加input
		  var input='<input name='+name+' type="hidden" value='+img+' class="up-item">';
		  $("#myform").append(input);
		  

    };
    this.del = function () {
			var img=this.doc.parent(".img");
			var name=img.find(".img_close").attr('name');
			var template='<input name="'+name+'" type="file" id="'+name+'" class="upload">';
			template+='<span>+</span>';
		    template+='<div class="error"></div> ';		  
		 	img.html(template);
			$(".up-item[name="+name+"]")[0]&&$(".up-item[name="+name+"]").remove();
    };


}

