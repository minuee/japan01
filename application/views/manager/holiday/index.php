
<!-- fullCalendar -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.print.min.css" media="print">


<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/chat.css" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/jquery.mCustomScrollbar.css" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/emoji.css" rel="stylesheet">

<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/main.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/jquery.emojiarea.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/idle-timer.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/underscore-min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/utils.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/utils.extend.js"></script>
<script type="text/javascript" src="http://192.168.56.1:3001/js/socket.io/1.7.2/socket.io.min.js" id="socket"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/placeholders.min.js"></script>
<script>
    window.container = {
        "nickname" : '<?=$UserName?>',
        "service_id": 'PROJECT',
        "channel_id": '<?=$ProjectNo?>',
        "channel": 'PROJECT:<?=$ProjectNo?>',
        "channel_status": 'play',
        "chat_room_idx": '<?=$ProjectIdx?>',
        "connection": '//'+ document.getElementById('socket').src.split('/')[2] +'/',
        "is_auth": '1',
        "UID": "<?=$LoginSession['userId']?>",
        "current_datetime": '<?=date("Y-m-d H:i:s")?>',
        "begin_datetime": '<?=date("Y-m-d H:i:s")?>',
        "wait_end_datetime": '',
        "max_message_count": 150,
        "message_count": 0,
        "is_user_count": 1,
        "users_count": 0,
        "notice": {},
    };
</script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/actions.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/ui.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/broadcast.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/node/js/index.js"></script>


<div class="content-wrapper" id="page_container">
    <input type="hidden" id="GlobalProjectIdx" value="<?=$ProjectIdx?>" >
    <input type="hidden" id="UID" value="<?=$LoginSession['userId']?>" >
    <input type="hidden" id="lastMessanger" value="">

    <div class="h_con ChatUserLost_Wrapper <?=(!$IsChat? 'display_none':'')?>" id="hidden_con">
        <ul id="hidden_join_list">
            <li>입장현황</li>
        </ul>
    </div>

    <div class="Chat_Wrapper display_none" style="clear:both;position: fixed;bottom:10px;right:5px;width:500px;height:600px;border:2px solid #ddd;z-index:10000;" >
        <div class="r_con">
            <div class="room_top">
                <h3><img src="<?php echo base_url(); ?>assets/node/images/r_chat_tit.gif" alt="채팅"></h3>
                <!--<a href="#none" class="room_close"><img src="<?php /*echo base_url(); */?>assets/node/images/r_chat_close.gif" alt="닫기"></a>-->
            </div>

            <!-- 채팅창 -->
            <div class="room_box">
                <div class="chatScrollH">
                    <ul id="chat_history">
                        <? if ( count($HistoryMessages) > 0 ) {?>
                            <!--<li class="w_100 btn_before_more"><a href="#"><span>이전 메시지 불러오기</span></a></li>-->
                            <?
                            $_preArray = array();
                            foreach ( $HistoryMessages as  $mkey => $row ) {
                                $_preArray[$mkey]['HRegID'] = $row['RegID'];
                                $_aling_css = "f_l text-left";
                                if ( $row['RegID'] == $LoginSession['userId'] ) {
                                    $_aling_css = "f_r text-right";
                                }
                                $before_user_css = "";
                                $before_user_li_css = "";
                                if ( $mkey  > 0   ) {
                                    if ( $_preArray[($mkey-1)]['HRegID'] == $HistoryMessages[$mkey]['RegID']) {
                                        $before_user_css = "display_none ";
                                        $before_user_li_css = "margin_top_0";
                                    }
                                }
                                $RegDatetime = $row['RegDatetime'];
                                if ( substr($row['RegDatetime'],0,10) == date("Y-m-d") ) {
                                    $RegDatetime = substr($row['RegDatetime'],10,6);
                                }
                                ?>
                                <li class="w_100 <?=$before_user_li_css?>">
                                    <dd class="<?=$_aling_css?>">
                                        <strong class="<?=$before_user_css?>">
                                            <?=$row['UserName']?>
                                        </strong>
                                        <p class="end_txt"><?=$RegDatetime?></p>
                                        <p class="speech-bubble"><?=$row['Message']?></p>
                                    </dd>
                                </li>
                            <? } ?>
                            <li class="w_100 text-center">------------ 최근 대화 ------------</li>
                        <? } ?>
                    </ul>
                </div>
                <div class="downScroll"><img src="<?php echo base_url(); ?>assets/node/images/r_down.png" alt="down"></div>
            </div>

            <div class="room_write">
                <ul class="check_noti">
                    <li><a href="#none" id="btn_show_userlist">접속자현황</a></li>
                    <li><a href="#none" name="btn_clear">채팅지우기</a></li>
                </ul>

                <div class="enter_write">
                    <div class="enter_write active" data-emojiarea><!-- textarea disabled 시 active 추가 해줘야 색상 변경 -->
                        <span class="emoji emoji-smile emoji-button">&#x1f604;</span>
                        <textarea id="message" name="message" rows="" cols="" maxlength="3000" placeholder=""></textarea>
                        <div class="textarea-clone"></div>
                    </div>
                    <button type="button" id="btn_enter" class="btn_enty"><img src="<?php echo base_url(); ?>assets/node/images/r_write.png" alt="입력"></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            [<?=$projectTitle?>] Project Leader : <?=$RegName?>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Project`s Detail</a></li>
            <li class="active"></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">범례(ToDo)</h4>
                    </div>
                    <div class="box-header with-border">
                        <div class="external-event bg-black" draggable="false">완료</div>
                        <div class="external-event bg-aqua">대기</div>
                        <div class="external-event bg-light-blue">작업중</div>
                        <div class="external-event bg-red">중단</div>
                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-events">
                            <div class="checkbox">
                                <label for="drop-remove">
                                    <input type="checkbox" id="drop-remove">
                                    remove after drop
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">일감생성(MakeDo)</h3>
                    </div>
                    <div class="box-body">
                        <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                            <!--<button type="button" id="color-chooser-btn" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Color <span class="caret"></span></button>-->
                            <ul class="fc-color-picker" id="color-chooser">
                                <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>

                                <!--<li><a class="text-black" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-green" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-lime" href="#"><i class="fa fa-square"></i></a></li>

                                <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>-->
                            </ul>
                        </div>
                        <!-- /btn-group -->
                        <div class="input-group">
                            <input id="new-event" type="text" class="form-control" placeholder="Event Title">

                            <div class="input-group-btn">
                                <button id="add-new-event" type="button" class="btn btn-primary btn-flat">생성</button>
                            </div>
                            <!-- /btn-group -->
                        </div>
                        <!-- /input-group -->
                    </div>
                </div>
                <div class="box box-solid <?=(!$IsChat? 'display_none':'')?>">
                    <div class="box-body text-right ">
                        <span id="users_count">현재 0명 접속중</span>&nbsp;&nbsp;&nbsp;<span id="btn_chat" class="btn btn-primary btn-flat">채팅룸열기</span>
                    </div>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <!-- THE CALENDAR -->
                        <div id="calendar"></div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>

<!-- jQuery UI 1.11.4 -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<!-- Slimscroll -->
<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/plugins/morris/fastclick.js"></script>
<!-- fullCalendar -->
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.js"></script>

<!-- Page specific script -->
<script>
    $(function () {


        $( ".Chat_Wrapper" ).draggable({containment: "#page_container"}).resizable({
            containment: "#page_container"
        });

        $(document).on("click", "#btn_chat", function () {
            $(".Chat_Wrapper").toggleClass("display_none");
            if ( $(".Chat_Wrapper").hasClass("display_none") ) {
                $(this).text('채팅룸열기');
                $('#hidden_con').animate({
                    "margin-left":"-230"
                },500,function(){
                    $('#btn_show_userlist').removeClass('none');
                });
            }else{
                // 채팅창 로드시 맨 아래로 이동
                $(".chatScrollH").mCustomScrollbar("scrollTo", "bottom", {
                    "scrollInertia": 0
                });
                $(this).text('채팅룸닫기');

            }
            return false;
        });


        /* initialize the external events
         -----------------------------------------------------------------*/
        function init_events(ele) {

            ele.each(function () {

                // create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
                // it doesn't need to have a start or end
                var eventObject = {
                    title: $.trim($(this).text()) // use the element's text as the event title
                };

                // store the Event Object in the DOM element so we can get to it later
                $(this).data('eventObject', eventObject);

                // make the event draggable using jQuery UI
                $(this).draggable({
                    zIndex        : 1070,
                    revert        : true, // will cause the event to go back to its
                    revertDuration: 0  //  original position after the drag
                })

            })
        }

        //$('#list_table').on('click', '.btn-income-detail',function() {
        function fn_info(idx) {
            $("#popdetail").setLayer({
                'url' : '/manager/project/popdetail/' + idx,
                'width' : 1024,
                'max_height' : 500
            });
        }


        init_events($('#external-events div.external-event'));

        /* initialize the calendar
         -----------------------------------------------------------------*/
        //Date for the calendar events (dummy data)
        var date = new Date()
        var d    = date.getDate(),
            m    = date.getMonth(),
            y    = date.getFullYear()
        $('#calendar').fullCalendar({
            header    : {
                left  : 'prev,next today',
                center: 'title',
                //right : 'month.agendaDay'
                right : 'month,basicWeek,basicDay'
            },
            buttonText: {
                today: '오늘',
                month: '월',
                week : '주',
                day  : '일'
            },
            //Random default events
            events    :
                <?=$ResultData?>
                /*{
                    title          : 'Todo관리툴 준비작업',
                    start          : "2019-07-01",
                    allDay         : true,
                    backgroundColor: '#cccccc', //red
                    borderColor    : '#cccccc', //red
                    imageurl        : "성남",
                    id       : 1
                },
                {
                    title          : 'Long Event',
                    start          : new Date(y, m, d - 5),
                    end            : new Date(y, m, d - 2),
                    backgroundColor: '#cccccc', //yellow
                    borderColor    : '#cccccc' //yellow
                },
                {
                    title          : 'Meeting',
                    start          : new Date(y, m, d, 10, 30),
                    allDay         : true,
                    backgroundColor: '#cccccc', //Blue
                    borderColor    : '#cccccc' //Blue
                },
                {
                    title          : 'Lunch',
                    start          : new Date(y, m, d, 12, 0),
                    end            : new Date(y, m, d, 14, 0),
                    allDay         : false,
                    backgroundColor: '#cccccc', //Info (aqua)
                    borderColor    : '#cccccc' //Info (aqua)
                },
                {
                    title          : 'Birthday Party',
                    start          : new Date(y, m, d + 1, 19, 0),
                    end            : new Date(y, m, d + 1, 22, 30),
                    allDay         : false,
                    backgroundColor: '#cccccc', //Success (green)
                    borderColor    : '#cccccc' //Success (green)
                },
                {
                    title          : 'Click for Google',
                    start          : new Date(y, m, 28),
                    end            : new Date(y, m, 29),
                    url            : 'http://google.com/',
                    backgroundColor: '#cccccc', //Primary (light-blue)
                    borderColor    : '#cccccc' //Primary (light-blue)
                }*/
            ,
            eventRender: function(event, eventElement) {
                /*if (event.rate) {
                    eventElement.find("div.fc-content").prepend("<span class='fc_event_rate'>" + event.rate + "%</span>");
                }*/
                if (event.todouser) {
                    eventElement.find("div.fc-content").prepend("<span class='fc_event_user_logo'>(" + event.todouser + ")</span>");
                }

            },
            eventResize: function(event) {
                let restart = moment(event.start ).format('YYYY-MM-DD');
                let reend = moment(event.end ).format('YYYY-MM-DD');

                $.ajax({
                    type        : 'POST' ,
                    async       : true,
                    url         : "/manager/project/workupdate",
                    data        : "ProjectWorkIdx="+event.id+"&sDate="+restart+"&eDate="+reend,
                    dataType    : 'JSON',
                    success  : function(res) {
                        if ( res.result !== true ) {
                            alert('오류가 발생하였습니다');
                            return false;
                        }
                        // 리스트 생성
                        let newArray = [] ;
                        let makedata = {};

                        makedata.cal_mode = 'updateTerm';
                        makedata.cal_UID = $("#UID").val();
                        makedata.cal_title = event.title ;
                        makedata.cal_start = restart;
                        makedata.cal_end = reend;
                        makedata.cal_id = event.id;
                        makedata.cal_todouser = event.todouser ;
                        newArray.push(makedata) ;
                        cal_update('updateTerm',newArray);
                    },
                    error : function (ts) {
                        alert(ts.responseStatus);
                    }
                });

            },
            eventDrop: function(event) {
                let restart = moment(event.start ).format('YYYY-MM-DD');
                let reend = moment(event.end ).format('YYYY-MM-DD');

                $.ajax({
                    type        : 'POST' ,
                    async       : true,
                    url         : "/manager/project/workupdate",
                    data        : "ProjectWorkIdx="+event.id+"&sDate="+restart+"&eDate="+reend,
                    dataType    : 'JSON',
                    success  : function(res) {
                        if ( res.result !== true ) {
                            alert('오류가 발생하였습니다');
                            return false;
                        }

                        // 리스트 생성
                        let newArray = [] ;
                        let makedata = {};

                        makedata.cal_mode = 'updateTerm';
                        makedata.cal_UID = $("#UID").val();
                        makedata.cal_title = event.title ;
                        makedata.cal_start = restart;
                        makedata.cal_end = reend;
                        makedata.cal_id = event.id;
                        makedata.cal_todouser = event.todouser ;
                        newArray.push(makedata) ;
                        cal_update('updateTerm',newArray);

                    },
                    error : function (ts) {
                        alert(ts.responseStatus);
                    }
                });

            },
            eventClick: function(event) {
                if (event.id) {
                    fn_info(event.id);
                }
            },
            editable  : true,
            droppable : true,
            drop      : function (date, allDay) {
                let originalEventObject = $(this).data('eventObject');
                let copiedEventObject = $.extend({}, originalEventObject);

                copiedEventObject.start           = date;
                copiedEventObject.allDay          = allDay;
                copiedEventObject.backgroundColor = $(this).css('background-color');
                copiedEventObject.borderColor     = $(this).css('border-color');
                let restart = moment(copiedEventObject.start ).format('YYYY-MM-DD');

                //let isreg =  fn_insert(copiedEventObject.title,copiedEventObject.start);
                if (  copiedEventObject.title ) {
                    $.ajax({
                        type: 'POST',
                        async: true,
                        url: "/manager/project/workinsert",
                        data: "ProjectIdx=" + $("#GlobalProjectIdx").val() + "&sDate=" + restart + "&eDate=" + restart + "&title=" + encodeURI(copiedEventObject.title),
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.result !== true) {
                                alert('오류가 발생하였습니다');
                                return false;
                            } else {
                                copiedEventObject.id = res.result_idx;
                                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                                if ($('#drop-remove').is(':checked')) {
                                    $(this).remove()
                                }

                                // 리스트 생성
                                let newArray = [] ;
                                let makedata = {};
                                makedata.cal_mode = 'insert';
                                makedata.cal_UID = $("#UID").val();
                                makedata.cal_title = copiedEventObject.title;
                                makedata.cal_start = restart;
                                makedata.cal_end = restart;
                                makedata.cal_id = res.result_idx;
                                makedata.cal_todouser = null ;
                                newArray.push(makedata) ;
                                cal_update('insert',newArray);
                            }
                        },
                        error: function (ts) {
                            alert(ts.responseStatus);
                        }
                    });
                }

            }
        });

        /* ADDING EVENTS */
        var currColor = '#00c0ef' //Red by default
        //Color chooser button
        var colorChooser = $('#color-chooser-btn')
        $('#color-chooser > li > a').click(function (e) {
            e.preventDefault()
            //Save color
            currColor = $(this).css('color')
            //Add color effect to button
            $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
        })
        $('#add-new-event').click(function (e) {
            e.preventDefault()
            //Get value and make sure it is not null
            var val = $('#new-event').val()
            if (val.length == 0) {
                return
            }

            //Create events
            var event = $('<div />')
            event.css({
                'background-color': currColor,
                'border-color'    : currColor,
                'color'           : '#fff'
            }).addClass('external-event')
            event.html(val)
            $('#external-events').prepend(event)

            //Add draggable funtionality
            init_events(event)

            //Remove event from text input
            $('#new-event').val('')
        })
    })
</script>