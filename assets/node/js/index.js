var broadcast = new Broadcast();
$(function() {
    $.idleTimer(60 * 60 * 1000 * 4); // 4시간
    $(document).bind("idle.idleTimer", function(){
        if (window.container.is_auth == 1) {
            var params = {
                "Channel": window.container.channel_id,
                "Nickname": window.container.nickname,
            };
            actions.user_leave(params, function() {
                $.idleTimer('destroy');
                broadcast.socket.emit('user:expired');
                window.container.nickname = '';
                window.container.is_auth = 0;
                ui.display.placeholder();
            });
        }
    });

    /**
     * 채널 정보
     */
    var params = {'channel_id': window.container.channel_id};
    actions.information(params, function(response)
    {
        window.container.broadcast      = response.data.ChannelInfo || null;
        

        // 흐름공지 (시간)
        //ui.notice_loop.time();

        // 방송 준비중 남은 시간
        //$('#area_preparing > .receive_timer').timer(window.container.current_datetime, window.container.begin_datetime).hide();

        // 채팅 아이콘 사용 여부
        if (window.container.broadcast.IsUseChat == 0) {
            $('.chat_head .icon_chat').data('use', 1).trigger('click').data('use', 0);
        }
    });

    $('input, textarea').placeholder();

    $("[onlynumber]").onlynumber();

    $("#lastMessanger").val('');


    // 레이어 닫기
    $('[name=btn_layer_close]').on('click', function(e) {
        $(this).layer('close');
        e.preventDefault();
    });

    // 닉네임 설정
    $('[name=btn_layer_nickname]').on('click', function(e) {
        var is_use = $(this).data('use');
        if (is_use != 1) {
            return false;
        }
        $('#layer_nickname').layer('open');
        $('#layer_nickname').find('.error').removeClass('hide').addClass('hide');
        e.preventDefault()
    });


    // 접속자리스트 컨트롤 By Noh.S.N
    $("#btn_show_userlist").click(function(e) {

        if ( $(this).hasClass('none')) {
            $('#hidden_con').animate({
                "margin-left":"-230"
            },500,function(){
                $('#btn_show_userlist').removeClass('none');
            });
        }else{
            $('#hidden_con').animate({
                "margin-left":"230"
            },500,function(){
                $('#btn_show_userlist').addClass('none');
            });
        }
        e.preventDefault();
    });

    // 채팅창 컨트롤
    $(".chat_head .icon_chat, .room_top .room_close").click(function(e) {
        var is_use = $(this).data('use');
        if (is_use == 0) {
            return false;
        }
        var image_path = "/front/v2/images/";
        var $el = $(".chat_wrap");
        $el.toggleClass("active");
        if($el.hasClass("active")){
            $(".chat_head h2").find("img").attr("src", image_path + "chat_logo_on.png");
            $(".chat_head .icon_chat").find("img").attr("src", image_path + "chat_menu03_on.png");
            //20190124 - 추가및 수정- 채팅창 높이
            $(".chat_con").addClass('active');
        } else {
            $(".chat_head h2").find("img").attr("src", image_path + "chat_logo.png");
            $(".chat_head .icon_chat").find("img").attr("src", image_path + "chat_menu03.png");
            //20190124 - 추가및 수정- 채팅창 높이
            $(".chat_con").removeClass('active');
        }

        //채팅창 높이
        chatAutoH();

        $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {"scrollInertia": 0});

        e.preventDefault();
    });

    $('#btn_video_play').on('click', function() {
        $('#video_main').get(0).play();
    });

    // 채팅 지우기
    $('[name=btn_clear]').on('click', function(e) {
        $('#chat_history').empty();
        $('#lastMessanger').val('');
        e.preventDefault();
    });

    // 채팅창 스크롤
    $(".chatScrollH").mCustomScrollbar({
        "axis": "y",
        "theme": "dark-3",
        "mouseWheelPixels": 100,
        "scrollInertia": 100,
        "callbacks": {
            onInit: function() {

            },
            whileScrolling:function() {
                // 채팅창 Down 버튼
                if (this.mcs.topPct != "100") {
                    if (!$('.downScroll').data('message')) {
                        $(".room_box .downScroll").show();
                    }
                    $('.downScroll').data('message', false);
                } else if (this.mcs.topPct == "100") {
                    $(".room_box .downScroll").hide();
                }
            }
        }
    });

    // 채팅창 Down 버튼 이벤트
    $(".room_box .downScroll").click(function(){
        $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {
            "scrollInertia": 300
        });
    });

    // 채팅창 로드시 맨 아래로 이동
    $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {
        "scrollInertia": 0
    });

    // 방송 정보
    $(".chat_txt_info .btn_info").click(function() {
        var notice_height = $('.room_box .alarm_noti').is(':hidden') ? 0 : $(".room_box .alarm_noti").height();
        $(this).toggleClass("active");
        if ($(this).hasClass("active")) {
            $(".chat_txt_view").show();
            //20190124 - 추가및 수정- 채팅창 높이
            chatAutoH();
            //퀵메뉴 위치
            $(".room_box .downScroll").css("top", "700px");
        } else {
            $(".chat_txt_view").hide();
            //20190124 - 추가및 수정- 채팅창 높이
            chatAutoH();
            $(".room_box .downScroll").css("top", "577px");

        }
    });

    // 방송정보 스크롤
    $(".chatScrollView").mCustomScrollbar({
        "axis": "y",
        "theme": "my-theme-gray",
        "mouseWheelPixels": 100,
        "scrollInertia": 100,
    });


    // 입력
    $('#btn_enter').on('click', ui.message.send);
    $("#message").on('keypress', ui.message.send);

    $("#message").on('keyup, keydown', function() {
        var maxlength = $("#message").attr('maxlength');
        if ($(this).val().length > maxlength) {
            $(this).val($(this).val().substring(0, maxlength));
        }
    });


    //ui.display.checkAgree();
    ui.display.alam_box();  //채팅 높이 계산

    //20190124 - 추가 채팅창 높이
    chatAutoH();

    //리사이징
    $(window).bind('resize', function() {
        //20190124 - 추가 채팅창 높이
        chatAutoH();
    });

});


var cal_update = function(mode, data ){

    if(data[0].cal_id) {
        ui.display.reCalendar(mode,data);
    }

};

var chatAutoH = function(){
    var h_window = $(window).height();
    var w_window = $(window).width();
    var h_chat_head = $(".chat_wrap .chat_head").height();
    var h_room_top = $(".r_con .room_top").height();
    var h_alarm_noti = $(".r_con .alarm_noti").is(':visible') ? $(".r_con .alarm_noti").height() : 0;
    var h_room_write = $(".r_con .room_write").height();
    var h_history = h_window - h_chat_head - h_room_top - h_alarm_noti - h_room_write;
    // var h_history = h_window - h_chat_head - h_room_top - h_alarm_noti - h_room_write - 5;

    /*
    console.log('h_window', h_window)
    console.log('w_window', w_window)
    console.log('h_chat_head', h_chat_head)
    console.log('h_room_top', h_room_top)
    console.log('h_alarm_noti', h_alarm_noti)
    console.log('h_room_write', h_room_write)
    */

    if(w_window < 1280) {
        var p_r_con =  h_window - (h_chat_head + h_room_top + h_alarm_noti + h_room_write + (h_history / 2));
        // var p_r_con =  h_window - (h_chat_head + h_room_top + h_alarm_noti + h_room_write + (h_history / 2) + 5);
        $('.r_con').css({"top": p_r_con, "opacity": 0.8});
        $(".chat_wrap").addClass("size_type");
        $(".room_box .chatScrollH").css('height', h_history / 2);
    } else {
        $('.r_con').css({"top": 0, "opacity": 1});
        $(".chat_wrap").removeClass("size_type");
        $(".room_box .chatScrollH").css('height', h_history);
    }
};



//20190124 - 추가 chat 높이
// var chatAutoH = function(){
// 	var wchatH = $(window).height();
// 	var wchatW = $(window).width();
// 	var chatHead = $(".chat_wrap .chat_head").height();
// 	var chatCon = $(".chat_con .l_con").height();
// 	var chatTop = $(".chat_wrap .room_top").height();
// 	var chatWrite = $(".chat_wrap .room_write").height();
// 	var totalCH = chatCon - chatWrite;
//
// 	if(wchatW < "1280"){
// 		$(".chat_wrap").addClass("size_type");
// 		$(".room_box .chatScrollH").css('height',(wchatH/3)+"px");
// 	} else {
// 		$(".chat_wrap").removeClass("size_type");
// 		$(".room_box .chatScrollH").css('height',totalCH+"px");
// 	}
// };



