var broadcast = new Broadcast();
var USER = null;


$(function() {

    ui.do_color(); ui.do_color();

    $('.color-add').on('click',function() {

        var $cp = $(this);

        $("#color-picker, .color-dim").remove();

        //colorPicker 생성
        $(this).after('<div id="color-picker"></div>');
        $(this).before('<div class="color-dim"></div>');

        var colorpicker = tui.colorPicker.create({
            container: document.getElementById('color-picker')
        });

        //선택 된 color code
        colorpicker.on('selectColor', function(obj) {
            $cp.data('color', obj.color);
        });

        //colorPicker show
        $("#color-picker").show();

        //빈 화면 클릭시 닫기
        $('.color-dim').click(function(){
            $("#color-picker, .color-dim").remove();
        });
    });

    // font Bold 처리
    $("input:checkbox[name=TextBold]").change(ui.do_bold);

    /* 시청자수 노출 여부 */
    $('#form0').find('[name=IsVisiblePlayCnt]').on('click', function() {
        var IsVisiblePlayCnt = $(this).val();
        if (IsVisiblePlayCnt == 2) {
            $('#form0').find('[name=ParticipationRate]').prop('disabled', false)
        } else {
            $('#form0').find('[name=ParticipationRate]').prop('disabled', true).val($('#form0').find('[name=ParticipationRate]').data('default'));
        }
    });
    
    // 방송 컨트롤 저장
    $('#form0').find('[name=btn_save]').on('click', function(e) {
        var params = {
            "ServiceID"             : window.container.service_id,
            "ChannelPoolID"         : window.container.channel_id,
            "IsVisiblePlayCnt"      : $('#form0').find('[name=IsVisiblePlayCnt]:checked').val(),
            "ParticipationRate"     : $('#form0').find('[name=ParticipationRate]').val(),
        };

        if (params.IsVisiblePlayCnt == 2 && utils.empty(params.ParticipationRate)) {
            alert('임의 노출값을 입력해주세요.');
            $('#form0').find('[name=ParticipationRate]').focus();
            return false;
        }
        if (params.IsVisiblePlayCnt == 2 && params.ParticipationRate < 1) {
            alert('임의 노출값은 최소 1 입니다.');
            $('#form0').find('[name=ParticipationRate]').focus();
            return false;
        }

        utils.ajax({
            "url": utils.replace('/ajax/control/panel/save/{ChannelPoolID}', params),
            "type": 'post',
            'dataType': 'json',
            'async': false,
            "data": params,
            "success": function(response) {
                broadcast.socket.emit('admin:channel:control:update');
                alert(response.message);
            }
        });
        e.preventDefault();
    });

    // 고정공지
    $('#form1').find('[name=btn_save]').on('click', function(e) {
        var params = {
            "ServiceID"             : window.container.service_id,
            "ChannelPoolID"         : window.container.channel_id,
            "Notice"                : $('#form1').find('[name=Notice]').val(),
            "TextColor"             : $('#form1').find('[name=TextColor]').val(),
            "TextBold"              : $('#form1').find('[name=TextBold]').prop('checked') ? 1 : 0,
        };
        if (utils.empty(params.Notice)) {
            alert('고정공지 내용을 입력해주세요.');
            $('#form1').find('[name=Notice]').focus();
            return false;
        }
        utils.ajax({
            "url": utils.replace('/ajax/control/panel/notice/fix/save/{ChannelPoolID}', params),
            "type": 'post',
            'dataType': 'json',
            'async': false,
            "data": params,
            "success": function(response) {
                broadcast.socket.emit('admin:channel:notice:update', 'fix');
                alert(response.message);
            }
        });
        e.preventDefault();
    });

    // 고정공지 해제
    $('#form1').find('[name=btn_cancel]').on('click', function(e) {
        var params = {
            "ServiceID"             : window.container.service_id,
            "ChannelPoolID"         : window.container.channel_id,
        };
        if (confirm('고정 공지를 해제하시겠습니까?')) {
            utils.ajax({
                "url": utils.replace('/ajax/control/panel/notice/fix/cancel/{ChannelPoolID}', params),
                "type": 'post',
                'dataType': 'json',
                'async': false,
                "data": params,
                "success": function(response) {
                    broadcast.socket.emit('admin:channel:notice:update', 'fix');
                    $('#form1').find('[name=Notice]').val('');
                    $('#form1').find('[name=TextColor]').val('#707070');
                    $('#form1').find('[name=TextBold]').prop('checked', false);
                    $('#form1').find('.color-add').data('color', '#707070');
                    ui.do_color(); ui.do_color();
                    alert(response.message);
                }
            });
        }
        e.preventDefault();
    });

    // 레이어 팝업 닫기
    $('[name=btn_close]').on('click', function(e) {
        $(this).closest('.layer-area').layer('close');
        e.preventDefault();
    });

    // 유저 조회
    $('#btn_user_search').on('click', function(e) {
        $('#chat_history').data('page',  0);
        $('#chat_history').data('loading',  true);
        ui.search();
        e.preventDefault();
    });

    // 유저 조회
    $('[name=SearchValue]').on('keypress', function(e) {
        if (e.which == 13) {
            $('#chat_history').data('page',  0);
            $('#chat_history').data('loading',  true);
            ui.search();
        }
    });

    // 유저 조회
    $('#chat_history').scroll(function() {
        if ($(this).prop('scrollHeight') - $(this).scrollTop() == $(this).height()) {
            if ($('#chat_history').data('loading') == false) {
                ui.search();
            }
        }
    });

    // 입력
    $('#btn_enter').on('click', ui.message.send);
    $("#message").on('keypress', ui.message.send);
});