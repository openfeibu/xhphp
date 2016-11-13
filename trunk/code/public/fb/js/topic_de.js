$(function() {
    
    var mySwiper;
    loading(true);
    var topic_id = GetString("tid");
    var token = window.localStorage.token;
    var pageNum = 10;
    var n;
    if (token) {
        var token_f = '&token=' + token;

    } else {
        var token_f = '';


    };
    $.getJSON(locahost + '/topic/getTopic/?topic_id=' + topic_id + token_f,
    function(data, status, xhr) {
        loading(false);
        if (data.code == 2001) {
            fb_alert(fb_error["2001"])
            window.localStorage.clear();
            window.location.href = "login.html";
            return;

        } else if (data.data == null) {
            fb_alert(fb_error["13"]);
            return;

        }
        var b = data.data;
        if (b["img"] != 'null' && b["img"] != null && b["img"] != '') {
            var img_url = '';
            var big_img_url = '';
            $.each(b["img"].split(","), 
            function(a, b) {
                big_img_url += '<div class="swiper-slide"><div class="cell"><img src="' + b + '" / ></div></div>';
            });
            $.each(b["thumb"].split(","), 
            function(a, b) {
                img_url += '<span style="background:url(' + b + ') no-repeat center;background-size:cover"></span>';


            });

        } else {
            var img_url = '';
            var big_img_url = '';


        }
        var time = afterTime(b["created_at"]);
        
        list_dom = '<div class="topic_header">\
                        <div class="img fl">\
                          <img src="' + b["avatar_url"] + '" alt="">\
                        </div>\
                        <div class="name fl">\
                          <p>' + b["nickname"] + '</p>\
                        </div>\
                        <div class="data fr">\
                          <p>' + time + '</p>\
                        </div>\
                      </div>\
                      <div class="topic_con">\
                        <div class="test">\
                          <p>' + b["content"] + '</p>\
                        </div>\
                        <div class="img">' + img_url + '</div>\
                      </div>\
                      <div class="topic_bottom">\
                        <div class="_xin fl" ></div>\
                        <div class="see fl" >浏览 <span>' + b["view_num"] + '</span></div>\
                        <div class="commen fl" >评论 <span>' + b["comment_num"] + '</span></div>\
                        <div class="zan fl" >赞 <span>' + b["favourites_count"] + '</span></div>\
                        <div class="liking fr" onclick=thumbUp(this,' + b["tid"] + ')></div>\
                      </div>';
        
        if (b["favorited"]) {
            list_dom = list_dom.replace(/liking/, "liked");

        }
        switch (b["type"]) {
            case "帮帮忙":
            
            break;
            case "吐吐槽":
            list_dom = list_dom.replace(/_bang/, "_tu");
            break;
            case "一起约":
            list_dom = list_dom.replace(/_bang/, "_yue");
            break;
            case "随心写":
            list_dom = list_dom.replace(/_bang/, "_sui");
            break;
            case "新鲜事":
            list_dom = list_dom.replace(/_bang/, "_xin");
            break;
            case "问一下":
            list_dom = list_dom.replace(/_bang/, "_wen");
            break;

        }
        $(".topic_ajax").html(list_dom);
        $(".big_img .swiper-wrapper").html(big_img_url);
        mySwiper = new Swiper('.swiper-container2', {
            loop: false,
            pagination: '.swiper-pagination2',

        });

    })
    getTopicCommentsList(1);
    
    function getTopicCommentsList(page) {
        $(window).off("scroll");
        
        if ($("#loading").length == 0) {
            $(".topic_de").append("<div id='loading'>正在玩命的加载中...</div>");

        }
        $.getJSON(locahost + '/topic/getTopicCommentsList/?page=1&topic_id=' + topic_id + '&page=' + page, 
        function(data, status, xhr) {
            $("#loading").remove();
            var dom = '',
            htmldom = '';
            $.each(data.data, 
            function(a, b) {
                if (b["be_review_id"] != 0) {
                    var name = "<span>" + b["nickname"] + "</span>回复<b>" + b["be_review_username"] + "</b>";

                } else {
                    var name = "<span>" + b["nickname"] + "</span>";

                }
                var time = afterTime(b["created_at"]);
                dom += '<div class="commen_box">\
                             <div class="img fl">\
                                 <a href="#"><img src="' + b["avatar_url"] + '" alt="头像"></a>\
                             </div>\
                             <div class="commen_test" tid="' + b["tcid"] + '">\
                               <div class="topic_header">\
                                  <div class="name fl">\
                                    <p>' + name + '</p>\
                                  </div>\
                                  <div class="data fr">\
                                    <p>' + time + '</p>\
                                  </div>\
                                </div>\
                                <div class="commen_con" >\
                                  <p>' + b["content"] + '</p>\
                                </div>\
                              </div>\
                           </div>';


            })
            if (data.data == '') {
                
                $("#loading").remove();
                if (page == 1) {
                    $(".topic_de").append("<div id='loaded'>快来抢沙发...</div>");

                } else {
                    $(".topic_de").append("<div id='loaded'>我是有底线的</div>");

                }

            } else {
                
                if (page == 1) {
                    $(".commen_list").html(dom);
                    n = 1;
                    $("#loading").remove();

                } else {
                    $(".commen_list").append(dom);

                }
                
                
                if (data.data.length < pageNum) {
                    
                    $("#loading").remove();
                    $(".topic_de").append("<div id='loaded'>我是有底线的</div>");

                } else {
                    $(window).on("scroll", 
                    function() {
                        if ($(window).scrollTop() + 50 >= $(document).height() - $(window).height()) {
                            
                            n++;
                            getTopicCommentsList(n);

                        }


                    });

                }
                


            }


        })

    }
    $(".commen_input_text").on("click", 
    function() {
        var val = $(this).val();
        if (val.length == 0) {
            $(this).attr("comment_id", "0").attr("placeholder", "评论一下。。。");
            var text = $(this).attr("text") == undefined ? '': $(this).attr("text");
            $(this).val(text);

        }

    })
    $(".commen_input_text").on("blur", 
    function() {
        var val = $(this).val();
        
        var tid = $(this).attr("comment_id");
        if (tid == 0) {
            $(this).attr("text", val);

        } else {
            $(".commen_test[tid='" + tid + "']").attr("text", val);

        }

    })
    $(".commen_list").on("click", ".commen_test", 
    function() {
        var comment_id = $(this).attr("tid");
        var name = $(this).parents(".commen_box").find(".name span").text();
        $(".commen_input_text").attr("comment_id", comment_id).attr("name", name).attr("placeholder", "@" + name);
        var text1 = $(".commen_test[tid='" + comment_id + "']").attr("text") == undefined ? '': $(".commen_test[tid='" + comment_id + "']").attr("text");
        $(".commen_input_text").val(text1);
        $(".commen_input_text").focus();

    })
    $(".commen_input_submit").on("click", commen_submit);
    function commen_submit() {
        if (!$(this).hasClass("active")) {
            $(".commen_input_submit").one("click", commen_submit);
            return;
        }
        if (window.localStorage.getItem("token") == undefined) {
            fb_alert(fb_error["2001"]);
            window.location.href = "login.html";
            return;
        }
        var val = $(".commen_input_text").val();
        var comment_id = $(".commen_input_text").attr("comment_id");
        var name = $(".commen_input_text").attr("name");
        var token = window.localStorage.getItem("token");
        if (val.length == 0) {
            alert("不可为空");
            $(".commen_input_submit").one("click", commen_submit);

        } else {
            if (comment_id == 0) {
                comment_id = '';

            } else {
                comment_id = '&comment_id=' + comment_id

            }
            
            var info = JSON.parse(window.localStorage.info);
            if (comment_id == 0) {
                var html = '<div class="commen_box">\
                       <div class="img fl">\
                           <a href="#"><img src="' + info["avatar_url"] + '" alt="头像"></a>\
                       </div>\
                       <div class="commen_test">\
                         <div class="topic_header">\
                            <div class="name fl">\
                              <p><span>' + info["nickname"] + '</span></p>\
                            </div>\
                            <div class="data fr">\
                              <p>刚刚</p>\
                            </div>\
                          </div>\
                          <div class="commen_con" >\
                            <p>' + val + '</p>\
                          </div>\
                        </div>\
                     </div>';

            } else {
                var html = '<div class="commen_box">\
                 <div class="img fl">\
                     <a href="#"><img src="' + info["avatar_url"] + '" alt="头像"></a>\
                 </div>\
                 <div class="commen_test">\
                   <div class="topic_header">\
                      <div class="name fl">\
                         <p><span>' + info["nickname"] + '</span>回复<b>' + name + '</b></p>\
                      </div>\
                      <div class="data fr">\
                        <p>刚刚</p>\
                      </div>\
                    </div>\
                    <div class="commen_con">\
                      <p>' + val + '</p>\
                    </div>\
                  </div>\
               </div>';

            }
            $(".commen_list").prepend(html);
            $(".commen_input_text").val('').attr("text", "");
            
            
            $.post(locahost + '/topic/comment/?token=' + token + '&topic_comment=' + val + '&topic_id=' + topic_id + comment_id, 
            function(data) {
                if (data.code == 2001) {
                    fb_alert(fb_error["2001"])
                    window.location.href = "login.html";
                    return;

                } else if (data.code == 110) {
                    fb_alert(fb_error["13"])
                    return;

                }
                if (data.code == "200") {
                    $(".commen_input_submit").one("click", commen_submit);
                    

                }


            })

        }
        return false;

    }


    $(".topic_de").on("click", ".img span", 
    function() {
        var i = $(this).index(".img span");
        $(".big_img").css({
            "z-index": 1001,
            "opacity": "1"
        });
        mySwiper.slideTo(i, 0, false);
        

    });
    $(".big_img").on("click", 
    function() {
        $(this).css({
            "z-index": "-1",
            "opacity": "0"
        });

    });
    $(".commen_input_text").on("input propertychange", 
    function() {
        if ($(this).val().length > 0)
        $(".commen_input_submit").addClass("active");
        else
        $(".commen_input_submit").removeClass("active");


    });

});