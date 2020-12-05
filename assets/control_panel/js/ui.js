var ui = {};
    ui.message = {};
    ui.user = {};
    ui.system = {};
    ui.templates = {};

ui.templates.message = '<li><a class="clickLayerMenu" onclick="ui.menu_open(this)">{nickname}</a><p>{message}</p></li>';
ui.templates.message_admin = '<li><a class="clickLayerMenu">{nickname}</a>{icon?}<p>{message}</p></li>';
ui.templates.icons = {
    'admin'         : '<span class="icon_ad" style="margin-right:3px;">관리자</span>',
    'notice'        : '<span class="icon_ad" style="margin-right:3px;">관리자 공지</span>',
    'teacher'       : '<span class="icon_tch" name="ico_teacher" style="margin-right:3px;">선생님</span>',
    'block'         : '<span class="icon_spam" name="ico_block" style="margin-right:3px;">차단</span>',
    'block_history' : '<span class="icon_spam" name="ico_block" style="margin-right:3px;">차단이력</span>',
    'hidden'        : '<span class="icon_spam" name="ico_hidden" style="margin-right:3px;">숨김</span>',
    'spam'          : '<span class="icon_spam" style="margin-right:3px;">스팸어</span>',
    'forbidden'     : '<span class="icon_spam" style="margin-right:3px;">금지어</span>',
};

ui.message.default = function(data)
{
    console.log('message', data)
    var is_down_scroll = true;
    var template_message = (data.is_admin == 1) ? ui.templates.message_admin : ui.templates.message;
    var $message = $(utils.replace(template_message, {
        "nickname": (data.is_teacher == 1) ? data.nickname_teacher : data.nickname,
        "message": utils.link(data.message.replace(/\n|\r/g, '<br/>'))
    }));

    $message.data('user', data);
    $message.attr('nickname', data.nickname);
    $message.appendIcons(data.is_admin, data.is_teacher, data.is_notice, data.is_spam, data.is_forbidden, data.is_block, data.is_block_history, data.is_hidden);

    // 선생님 메세지
    if (data.is_teacher == 1) {
        $message.addClass('info_notice');
    }
    // is_notice 1: 관리자공지, 2: 흐름공지, 3: 자동문구
    if (data.is_notice > 0) {
        $message.find('p').get(0).style = data.NoticeStyle;
    }
    if ($('#chat_history').find('li').length >= window.container.max_messages_count) {
        $('#chat_history').find('li').eq(0).remove();
    }
    if ($('#chat_history').prop('scrollHeight') - $('#chat_history').scrollTop() - 30 > $("#chat_history").height()) {
        is_down_scroll = false;
    }
    $('#chat_history').append($message);
    if (is_down_scroll) {
        $('#chat_history').scrollTop($('#chat_history').prop('scrollHeight'));
    }
};

ui.message.notify = function(data)
{
    console.log('message:notify', data);
    ui.message.default(data);
};

ui.message.notice = function(data)
{
    console.log('message:notice', data);
};

ui.user.count = function(data)
{
    console.log('admin:channel:users:count', data)
    if (data.users_count_total) {
        $('#user_count_total').html(utils.comma(data.users_count_total) +'명');
    }
    $('#users_count_auth').html('채팅참여 '+ utils.comma(data.users_count_auth) +'명');
};

ui.search = function()
{
    var page = $('#chat_history').data('page') + 1;

    $('#chat_history').data('page',  page);
    $('#chat_history').data('loading',  true);

    var params = {
        "ServiceID"         : window.container.service_id,
        "ChannelPoolID"     : window.container.channel_id,
        "SearchType"        : $('[name=SearchType]').val(),
        "SearchValue"       : $('[name=SearchValue]').val(),
        "page"              : page,
    };
    utils.ajax({
        'url': utils.replace('/ajax/control/panel/users/{ServiceID}/{ChannelPoolID}', params),
        'type': 'get',
        'dataType': 'json',
        'async': false,
        'data': params,
        'success': function(response) {
            if (params.page == 1) {
                $('#user_list').find('tbody').empty();
            }
            if (params.page == 1 && response.data.Users.length == 0) {
                $('#user_list').find('thead').show();
            } else {
                $('#user_list').find('thead').hide();
            }
            for(var i = 0; i < response.data.Users.length; i++) {
                var html = '' +
                    '<tr>' +
                    '   <td><a href="javascript:void(0)" name="btn_menu" name="btn_menu" onclick="ui.menu_open(this)">{Nickname}</a></td>' +
                    '   <td><a href="#none" name="btn_popup_messages" data-type="phone" data-nickname="{Nickname}" data-phone="{MobileTEL}">{MobileTEL}</a></td>' +
                    '   <td><a href="#none" name="btn_popup_messages" data-type="ip" data-nickname="{Nickname}" data-ip="{ChatMemberIP}">{ChatMemberIP}</a></td>' +
                    '</tr>';
                var $user = $(utils.replace(html, response.data.Users[i]));
                $user.find('[name=btn_menu]').parent().data('user', {
                    "nickname"          : response.data.Users[i].Nickname,
                    "phone"             : response.data.Users[i].MobileTEL,
                    "ip_client"         : response.data.Users[i].ChatMemberIP,
                    "is_teacher"        : response.data.Users[i].IsTeacher,
                    "is_hidden"         : response.data.Users[i].IsHidden,
                    "is_block"          : response.data.Users[i].IsBlock,
                    "is_block_history"  : response.data.Users[i].IsBlockHistory,
                });
                $user.find('[name=btn_popup_messages]').on('click', function(e) {
                    var popup_params = {
                        "ChannelPoolID"     : params.ChannelPoolID,
                        "SearchType"        : $(this).data('type'),
                        "Nickname"          : $(this).data('nickname'),
                        "MobileTEL"         : $(this).data('phone'),
                        "ChatMemberIP"      : $(this).data('ip')
                    };

                    if (!utils.empty(popup_params.Nickname)) {
                        utils.popup(utils.replace('/popup/chat/messages/{ChannelPoolID}?SearchType={SearchType}&Nickname={Nickname}&MobileTEL={MobileTEL?}&ChatMemberIP={ChatMemberIP?}', popup_params), 570, 700);
                    }
                    e.preventDefault();
                });
                $('#user_list').find('tbody').append($user);
            }
            if (response.data.TotalCount > $('#user_list > tbody > tr').length) {
                $('#chat_history').data('loading',  false);
            }
        }
    });
};

ui.control_broadcast = function(BroadStatus, PlayWaitTime, callback)
{
    var params = {
        "ChannelPoolID"     : window.container.channel_id,
        "BroadStatus"       : BroadStatus,
        "PlayWaitTime"      : PlayWaitTime
    };
    utils.ajax({
        'url': utils.replace('/ajax/control/panel/broadcast/{ChannelPoolID}', params),
        'type': 'post',
        'dataType': 'json',
        'async': false,
        'data': params,
        'success': function(response) {
            console.log(response)
            if (response.code != 200) {
                alert(response.message);
                return;
            }
            if (callback) {
                callback(response);
            }
            $('.live_control li').removeClass('active');
            $('[data-status='+ BroadStatus +']').parent().addClass('active');
        }
    });
};

ui.message.send = function(e)
{
    if (e.type == 'click' || e.which == 13) {
        if (e.shiftKey) {
            return;
        }
        var params = {
            "nickname"      : $('[name=Nickname]').val(),
            "message"       : utils.xss($("#message").val()),
            "is_notice"     : $('[name=IsNoticeMessage]').prop('checked') ? 1 : 0,
        };
        if (utils.empty(params.nickname)) {
            alert('닉네임을 입력해주세요.');
            $('[name=Nickname]').focus();
            return;
        }
        if (utils.empty(params.message)) {
            alert('메세지를 입력해주세요.');
            $("#message").focus();
            return;
        }
        if (!utils.empty(message)) {
            $("#message").val('').focus();
            if (broadcast.socket) {
                broadcast.socket.emit('admin:send', params);
                $('[name=IsNoticeMessage]').prop('checked', false);
            }
        }
        e.preventDefault();
    }
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

ui.do_color = function(){
    $('.color-add').each(function() {
        var $form = $(this).closest('form');
        var color = $(this).data('color') || $form.find('[name=TextColor]').val();
        $form.find('[name=TextColor]').val(color);
        $form.find("textarea").css("color", color);
        $form.find(".color-box").css("color", color);
    });
    $("#color-picker, .color-dim").remove();
};

ui.do_bold = function()
{
    $("input:checkbox[name=TextBold]").each(function() {
        var $form = $(this).closest('form');
        if ($(this).is(':checked')) {
            $form.find("[name=Notice]").css("font-weight", "bold");
        } else {
            $form.find("[name=Notice]").css("font-weight", "normal");
        }
    });
};

ui.menu_open = function(_this)
{
    USER = $(_this).parent().data('user');
    var _posT = $(_this).offset().top;
    $('body').off('click');
    $('body').addClass("no-scroll");
    if (USER.is_block == 1) {
        $(".layer-area-mn").find('[name=btn_layer_block]').parent().hide();
    }
    if (USER.is_hidden == 1) {
        $(".layer-area-mn").find('[name=btn_layer_hidden]').parent().hide();
        $(".layer-area-mn").find('[name=btn_layer_hidden_cancel]').parent().show();
    } else {
        $(".layer-area-mn").find('[name=btn_layer_hidden]').parent().show();
        $(".layer-area-mn").find('[name=btn_layer_hidden_cancel]').parent().hide();
    }
    if (USER.is_teacher == 1) {
        $(".layer-area-mn").find('[name=btn_layer_teacher]').parent().hide();
        $(".layer-area-mn").find('[name=btn_layer_teacher_cancel]').parent().show();
    } else {
        $(".layer-area-mn").find('[name=btn_layer_teacher]').parent().show();
        $(".layer-area-mn").find('[name=btn_layer_teacher_cancel]').parent().hide();
    }
    $(".layer-area-mn").show();
    $(".layer-area-mn").css('top', _posT - '240');
    setTimeout(function() {
        $('body').on('click', function(e) {
            $('body').off('click');
            if ($(e.target).closest('.layer-area-mn').length == 0) {
                ui.menu_close();
                USER = null;
            }
        });
    }, 0);
}

ui.menu_close = function()
{
    $(".layer-area-mn").hide();
    $('body').removeClass("no-scroll");
};