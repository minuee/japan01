var ui = {};
    ui.display = {};
    ui.broadcast = {};
    ui.message = {};
    ui.user = {};
    ui.system = {};
    ui.channel = {};
    ui.auto = {};
    ui.notice_loop = {};
    ui.templates = {};
    ui.sns = {};

ui.templates.icons = {
    'notice'    : '<span class="icon_ad">관리자</span>',
    'teacher'   : '<span class="icon_tch" name="icon_tch">선생님</span>',
};

/**
 * 메세지 전송
 * @param e
 */
ui.message.send = function(e)
{
    if (e.type == 'click' || e.which == 13) {
        if (e.shiftKey) {
            return;
        }

        if ( $("#isNotice").is(":checked") === true ) {
            if ( $("#NowNoticeIdx").val() > 0 ) {
                alert("현재 공지가 등록되어 있습니다. 제거후 등록해주세요");
                return false;
            }
            if( !confirm("공지로 올리시겠습니까?") ) {
                $("#isNotice").prop("checked",false);
                return false;
            }
        }

        if (window.container.channel_status == 'termination') {
            return;
        }
        var maxlength = $("#message").attr('maxlength');
        var message = utils.xss($("#message").val());
        if (message.length > maxlength) {
            message = message.substring(0, maxlength)
        }
        if (!utils.empty(message)) {
            $("#message").val('').focus();
            if ( $("#isNotice").is(":checked") === true ) {
                message = "[공지]" + message;
                if (broadcast.socket) {
                    broadcast.socket.emit('sendnotice', message);
                }
            }else{
                if (broadcast.socket) {
                    broadcast.socket.emit('send', message);
                }
            }


        }
        e.preventDefault();
    }
};



/**
 * 업무 메세지 전송
 * @param e
 */
ui.message.notifysend = function(data){

    let message = utils.xss(data);

    if (!utils.empty(message)) {
        $("#message").val('').focus();
        if (broadcast.socket) {
            broadcast.socket.emit('send', message);
        }
    }

};

/**
 * UI 알림
 * @param data
 */
ui.message.notify = function(data)
{
    ui.message.default(data);
};


/**
 * UI 달력업데이트
 * @param data
 */

ui.display.reCalendar = function (mode ,data) {
    if ( mode == 'updateTerm') {
        if (broadcast.socket) {
            broadcast.socket.emit('calsend', data);
        }
    }else if ( mode == 'insert') {
        if (broadcast.socket) {
            broadcast.socket.emit('calsend', data);
        }
    }

};


ui.display.calupdate = function(data) {
    if (data.cal_id) {
        let item = $("#calendar").fullCalendar('clientEvents', data.cal_id);

        if (data.cal_mode == 'updateTerm') {
            item[0].start = data.cal_start;
            item[0].end = data.cal_end;
            item[0].id = data.cal_id;
            item[0].title = data.cal_title;
            $('#calendar').fullCalendar('updateEvent', item[0]);
        } else if (data.cal_mode == 'insert') {
            if (  $("#UID").val() !== data.cal_UID ) {
                var addEvents = {
                    title:data.cal_title,
                    allDay: true,
                    start: data.cal_start,
                    end: data.cal_end,
                    id:data.cal_id,
                    backgroundColor:'#605ca8'
                };
                $('#calendar').fullCalendar('renderEvent', addEvents, true);
            }
        }
    }
};

/**
 * UI 공지추가제거
 * @param data
 */

ui.display.fn_updateNotice = function (data) {
    if (broadcast.socket) {
        broadcast.socket.emit('user:msgnotice', data);
    }
};


ui.display.actionnoticeupdate = function(data) {
    let wtarget_mode = data.target_mode;

    if (  wtarget_mode == "remove" ) {
        $(".alarm_noti").addClass("display_none");
    }else{
        $("#TargetNoticeTitle").removeClass("notice_already");
        $(".alarm_noti").removeClass("display_none");
    }
    ui.display.alam_box();
};


/**
 * UI 개인ToDO업무 업데이트
 * @param data
 */
ui.display.dotoinsert = function(data) {

    let thisuid = parseInt($("#UID").val());
    let senduserid = parseInt(data.cal_UID);

    if (  thisuid == senduserid ) {
    }else{
        $("#changeUserId").text(data.cal_todouser);
        readyAlert();
    }
};

ui.display.addIndivisualTodo = function (data) {
    if (broadcast.socket) {
        broadcast.socket.emit('user:todo', data);
    }
};

/**
 * UI 메세지  * @param data
 */
ui.message.default = function(data)
{
    let float_type = "f_l text-left";
    if( window.container.nickname == data.nickname && window.container.service_id == data.service_id  && window.container.channel_id == data.channel_id  ) {
        float_type = "f_r text-right";
    }
    let before_user = "";
    let before_user_li = "";
    if ( $('#lastMessanger').val() == data.nickname ){
        before_user = "display_none ";
        before_user_li = "margin_top_0";
    }


    let originwmessage = data.message;
    if (data.is_notice == 'regist') {
        data.message = data.message + "\n헤더공지 등록되었습니다.";
        $("#TargetNoticeTitle").addClass("notice_already");
    }

    let nowdate = new Date($.now());
    let nowtime = nowdate.getHours()+":"+nowdate.getMinutes();
    var template_message = '<li class="w_100 '+ before_user_li +'"><dd class="'+float_type+'"><strong class="'+before_user+'">{nickname?}</strong><p class="speech-bubble">{message}</p><p class="end_txt">'+nowtime+'</p></dd></li>';
    var $message = $(utils.replace(template_message, {
        'nickname': (data.is_teacher == 1) ? data.nickname_teacher : data.nickname,
        'message': utils.link(data.message.replace(/\n|\r/g, '<br/>')),
    }));

    $message.data('user', data);
    $message.attr('nickname', data.nickname);

    // 관리자 공지
    if (data.is_notice == 'remove') {
        $(".alarm_noti").addClass("display_none");
    }else if (data.is_notice == 'regist') {
        let add_permission = "";
        if( window.container.UID == data.userid   ) {
            add_permission = "<a class='noti_close'><img src='/assets/node/images/r_chat_close.gif' alt='삭제'></a>";
        }
        let strhtml = "<h4 id='TargetNoticeTitle'>"+originwmessage+"</h4>"+add_permission;
        $("#alarm_noti").html(strhtml);
        $(".alarm_noti").removeClass("display_none");
    }

    if ($('#chat_history').find('li').length >= window.container.max_message_count) {
        $('#chat_history').find('li').eq(0).remove();
    }

    $('#chat_history').append($message);
    $('#lastMessanger').val(data.nickname);
    $('.downScroll').data('message', true);
    $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {"scrollInertia": 0});

    /*console.log("111",window.container.UID);
    console.log("22",data.UID);
    console.log("33",window.container.service_id);
    console.log("44",data.service_id);
    console.log("555",window.container.channel_id);
    console.log("66",data.channel_id);*/
    let strNotice = 0;
    let newNoticeIdx = 0;
    if ( $("#isNotice").is(":checked") === true ) {
        $("#isNotice").prop("checked",false);
        strNotice = 1;
    }

    if ( $(".Chat_Wrapper").hasClass('display_none') ) {
        $("#changeUserId").text(data.cal_todouser);
        recieveMsgAlert();
    }

    if( window.container.UID == data.userid && window.container.service_id == data.service_id  && window.container.channel_id == data.channel_id  ) {
        let newmessage = originwmessage;
        if (data.is_notice == 'regist') {
            newmessage = originwmessage + "\n헤더공지 등록되었습니다.";
        }else if ( data.is_notice == 'remove' ){
            strNotice =  0;
        }

        //저장한다
        $.ajax({
            type: 'POST',
            async: false,
            url: "/nodeapi/API/msginsert",
            data: "regid=" + data.userid + "&service_id=" + data.service_id + "&chatroom=" + window.container.chat_room_idx + "&message=" + encodeURI(newmessage) + "&IsNotice=" + strNotice + "&noticemessage=" + encodeURI(originwmessage) ,
            dataType: 'JSON',
            success: function (res) {
                if (res.new_idx > 0 ) {
                    $("#NowNoticeIdx").val(res.new_idx);
                }
            }
        });
    }
    return false;

};


/**
 * UI 알림
 * @param data
 */
ui.message.notify = function(data)
{
    ui.message.default(data);
};

/**
 * 참여자 수
 * @param count
 */
ui.user.count = function(count)
{
    $('#users_count').html('현재 접속 : '+ utils.comma(count) +'명');
    $('#users_count_digit').html(utils.comma(count));
};



/**
 * 참여자 리스트
 * @param data
 */
ui.user.list = function(user_list) {

    $(document).find('#hidden_join_list').empty();
    let return_user_list = "";
    let jbSplit = user_list.toString().split(",");
    for (var i in jbSplit) {
        if ( i == 'exist') continue;
        return_user_list += "<li>" + jbSplit[i] + "</li>";
    }
    $(document).find('#hidden_join_list').html(return_user_list);
};


/**
 * 채널 정보 업데이트
 * @type {{notice: ui.channel.update.notice, control: ui.channel.update.control}}
 */
ui.channel.update = {
    "notice": function(data) {
        if (data.fix) {
            window.container.notice.fix = data.fix;
            // 고정 공지 업데이트
            ui.display.notice_fix();
        }
        if (data.loop) {
            window.container.notice.loop = data.loop;
            ui.notice_loop.time();
        }
    },
    "control": function(data) {
        window.container.is_use_count = data.IsVisiblePlayCnt;
        ui.display.count();
    }
};

/**
 * 시스템 메세지
 * @param data
 */
ui.system.message = function(data)
{
    alert(data.message)
};

/**
 * 시스템 종료
 * @param data
 */
ui.system.exit = function(data)
{
    alert(data.message);
    if (opener) {
        self.close();
    } else {
        // window.history.back();
    }
};

// **************************************************************
//
// **************************************************************

/**
 * $('#message') placeholder
 */
ui.display.placeholder = function()
{
    $('#message').prop('disabled', false).attr('placeholder', "메시지를 입력하세요");
};

/**
 * UI 초기화
 */
ui.display.reset = function()
{
    $('.tv_receive').hide();
    $('#player1').remove();
    $('#area_wait > .receive_timer').clearTimer();
    ui.display.placeholder();
};

/**
 * 참여자 수 노출 old
 */
ui.display.count = function()
{
    if (window.container.is_use_count == 1) {
        $('#users_count').show();
    } else {
        $('#users_count').hide();
    }
};

/**
 * 고정공지 노출
 */
ui.display.notice_fix = function()
{
    var notice = window.container.notice.fix;
    if (utils.empty(notice.Notice)) {
        $('.room_box').removeClass('on');
    } else {
        $('.room_box').removeClass('on').addClass('on');
        $('.alarm_noti > h4').html(notice.Notice);
        $('.alarm_noti > h4').get(0).style = notice.NoticeStyle;
    }
    ui.display.alam_box();
};

/**
 * 채팅 공지 높이 계산
 */
ui.display.alam_box = function(){
    var alamNoti = $("#alarm_noti").height();
    var viewDisplay = $('.alarm_noti').hasClass("display_none");
    if(viewDisplay === true){
        $('.room_box .chatScrollH').css({'height':'340'});
        $('.room_box').css({'padding-top':'0'});
    } else  {
        if ( alamNoti < 0 ) alamNoti = 30;
        $('.room_box .chatScrollH').css({'height':'340' - alamNoti});
        $('.room_box').css({'padding-top':alamNoti});
    }
};

/**
 * 약관동의 체크박스
 */
ui.display.checkAgree = function(){
    var checkAgreeTit = $(".check_agree dt a");
    var checkAgreeBox = $(".check_agree dd");
    checkAgreeTit.click(function(){
        var $this = $(this);
        var viewDisplay = $this.parent().next("dd").css("display");
        if(viewDisplay == "none"){
            checkAgreeBox.hide();
            checkAgreeTit.parent().removeClass("active");
            $this.parent().addClass("active").next().show();
        } else  {
            checkAgreeTit.parent().removeClass("active");
            checkAgreeBox.hide();
        }
    });

};

