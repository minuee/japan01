<!-- fullCalendar -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.print.css" media="print">
<style>
    .fc-scroller {
        overflow-y: hidden !important;
    }
</style>
<div class="content-wrapper" id="page_container">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <?=$projectTitle?>
            <small>※ 등록후 1시간 이내에서는 삭제가 가능합니다. <span class="text-red">※당직은 자동으로 등록됩니다.(현재 개발부서만)</span> </small>
        </h1>
        <ol class="breadcrumb">
            <input type="hidden" id="MyName" value="<?=$LoginSession['name']?>">
            <input type="hidden" id="MyUserID" value="<?=$LoginSession['userId']?>">
            <input type="hidden" id="MyTeamID" value="<?=$LoginSession['groupidx']?>">
            <input type="hidden" id="MyGroupID" value="<?=$LoginSession['groupcode']?>">
            <input type="hidden" id="MyRoleID" value="<?=$LoginSession['role']?>">
            <input type="hidden" id="IsOnlyme" value="0">
            <li><a href="#"><i class="fa fa-calendar"></i> 일정관리</a></li>
            <li class="active"></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="col-sm-12">
                    <div class="box box-solid">
                        <div class="box-body">
                            <!-- the events -->
                            <div id="external-events">
                                <? foreach($CommonCode['Schedule'] as $key => $row ) {
                                    if ( $row['mode'] > 1 ) continue;
                                    ?>
                                    <div class="fc-white external-event"  style="background-color:<?=$row['color']?>;"><?=$row['name']?></div>
                                <? } ?>
                                <!--<div class="external-event bg-black">연차(휴가)</div>
                                <div class="external-event bg-aqua">반차</div>
                                <div class="external-event bg-green">조퇴</div>
                                <div class="external-event bg-yellow">당직</div>
                                <div class="external-event bg-red">특근</div>
                                <div class="external-event bg-fuchsia">전체일정</div>
                                <div class="external-event bg-purple">팀일정</div>
                                <div class="external-event bg-teal">개인비밀</div>-->
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-sm-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <p class="box-title-small">
                                <span class="text-fuchsia">■</span>전체일정
                                <span class="text-purple">■</span>팀일정
                                <span class="text-teal">■</span>개인일정
                            </p>
                        </div>
                        <div class="box-body">
                            <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                                <ul class="fc-color-picker" id="color-chooser">
                                    <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                    <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
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
                </div>
                <div class="col-sm-12 <?=($LoginSession['role'] == ROLE_ADMIN ? '' : 'display_none' )?>">
                    <label for="ProjectGroup" class="hidden">사업부</label>
                    <select class="form-control noh_text_14 w_100" id="ProjectGroup">
                        <option value="0">사업부선택</option>
                        <? foreach( $BUSINESSCode as $key => $row) { ?>
                            <option value="<?=$row['code']?>|<?=$row['text']?>" <?=selected($row['code'], $search['ProjectGroup'])?>><?=$row['name']?></option>
                        <? }?>
                    </select>
                </div>
                <div class="col-sm-12">
                    <label for="ScheduleTeam"  class="hidden">팀선택</label>
                    <select class="form-control noh_text_14 w_100" id="ScheduleTeam">
                        <?//if( $LoginSession['role'] == ROLE_ADMIN || $LoginSession['role'] == ROLE_SUPERVISOR ) {?>
                        <option value="0">팀선택</option>
                        <? //}?>
                        <? foreach( $GROUPCode as $key => $row) { ?>
                        <!--<option value="<?/*=$key*/?>" <?/*=selected($LoginSession['groupidx'],$key)*/?>><?/*=$row*/?></option>-->
                            <option value="<?=$key?>" <?=selected($LoginSession['groupidx'],$key)?>><?=$row?></option>
                        <? }?>
                    </select>
                </div>
                <div class="col-sm-12 mt_10 <?=($LoginSession['role'] == ROLE_ADMIN ? 'display_none' : '' )?>">
                    <label for="AgentRegist" >팀원대리등록</label>
                    <select class="form-control noh_text_14 w_100" id="AgentRegist">
                            <option value="">작업자선택</option>
                            <? foreach ( $Users as  $key => $val ) {?>
                            <option value="<?=$val['userId']?>_<?=$val['GROUP_IDX']?>"><?=$val['name']?></option>
                            <? }?>
                    </select>
                </div>
                <div class="col-sm-12 " >
                    <button id="btn_view_onlyme" type="button" class="btn btn-default btn-flat w_100">내 일정만 보기</button>
                </div>
                <div class="col-sm-12 schedule_help_wrapper">
                    <label for="ProjectTeam" >범례</label>
                    <ul>
                        <li>※ 연차,조퇴는 업무할당 불가( 본인업무생성가능)</li>
                        <li>※ 수동일정(전체일정등)은 해당박스클릭후 생성</li>
                        <li>개인일정은 본인에게만 노출</li>
                        <li>당일이전 일정은 수정불가(Drop주의)</li>
                        <li class="text-red">※ 팀원선택후 등록시 해당인원일정등록,단 연차,반차만 가능</li>
                    </ul>
                </div>
            </div>

            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-body no-padding">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<script src="<?php echo base_url(); ?>assets/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/morris/fastclick.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.js"></script>

<script src="<?php echo base_url(); ?>assets/js/html2canvas.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jspdf.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jspdf_addimage.js"></script>
<script src="<?php echo base_url(); ?>assets/js/schedule.js?v=<?=time()?>"></script>

<script>

    function fn_schedule_info(idx) {
        $("#popdetail").setLayer({
            'url' : '/manager/schedule/popdetail/' + idx,
            'width' : 1024,
            'max_height' : 500
        });
    }

    function removeitem( idx) {
        if ( idx ) {
            $("#calendar").fullCalendar('removeEvents', idx);
        }
    }

    function reloaditem( idx) {
        if ( idx ) {
            $("#calendar").fullCalendar('removeEvents', idx);
            $("#calendar").fullCalendar('refetchEvents', idx);
        }
    }

    function init_events(ele) {
        ele.each(function () {
            var eventObject = {
                title: $.trim($(this).text())
            };
            $(this).data('eventObject', eventObject);
            $(this).draggable({
                zIndex        : 1070,
                revert        : true,
                revertDuration: 0
            })

        })
    }
    $(function () {

        jQuery(document).on("change", "#ProjectGroup", function(){
            // 불러온다
            let gcode = $(this).val().split("|");
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/getSubTeam",
                data: "GroupCode=" + gcode[0],
                async: false,
                success: function (json) {
                    if (json.totalCount > 0 ) {
                        $("#ScheduleTeam").html('');
                        let html = "<option value='0'>팀선택</option>";
                        $("#ScheduleTeam").append(html);
                        for(var i = 0; i < json.dataList.length; i++) {
                            html = "<option value='"+json.dataList[i]['IDX']+"'>"+json.dataList[i]['NAME']+"</option>";
                            $("#ScheduleTeam").append(html);
                        }
                        return false;
                    }
                }
            });
        });

        init_events($('#external-events div.external-event'));

        let fullcalendarWidth2 = $("#calendar").width();
        let fullcalendarWidth  = fullcalendarWidth2 > 1300  ? 2000 : fullcalendarWidth2;
        let date = new Date();
        let d    = date.getDate();
        let m    = date.getMonth()+1;
        let y    = date.getFullYear();
        $('#calendar').fullCalendar({
            customButtons: {
                myCustomButton: {
                    text: 'Export PDF',
                    click: function(event) {
                        pageprint(event);
                    }
                }
            },
            header    : {
                left  : 'prev,next today myCustomButton',
                center: 'title',
                //right : 'month.agendaDay'
                right : 'month,basicWeek'
            },
            buttonText: {
                today: '오늘',
                month: '월',
                week : '주'
            },
            firstDay: 0,				//---	0. 일요일
            weekends: true,
            allDaySlot: true,
            allDayText: '종일',
            monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'] ,
            monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            dayNames: ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'],
            dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
            //buttonText: {today: '이번달', month: '월', week: '주', day: '일'}
            //,lang : "ko"
            height: fullcalendarWidth,//$("#calendar").width(),
            contentHeight: fullcalendarWidth,//$("#calendar").width(),
            eventSources: [
                {
                    events: function(start, end, timezone, callback) {
                        let restart = moment(start).format('YYYY-MM-DD');
                        let reend = moment(end).format('YYYY-MM-DD');

                        $.ajax({
                            url: '/manager/schedule/get_events',
                            dataType: 'json',
                            beforeSend : function() {
                                $('.wrap-loading').removeClass('display_none');
                            },
                            data: {
                                // our hypothetical feed requires UNIX timestamps
                                start: restart,
                                end: reend
                            },
                            success: function(res) {
                                let events = res.events;
                                callback(events);
                                $('.wrap-loading').addClass('display_none');
                            }
                        });
                    }
                },
            ]
            ,eventRender: function(event, eventElement) {
                //eventElement.find("div.fc-content").prepend("<span class='fc_event_user_logo'>(" + event.todouser + ")</span>");
                eventElement.find(".fc-title").html(eventElement.find('.fc-title').text());
                let select_team = $("#ScheduleTeam").val();
                let select_group = 0;
                if ( $("#ProjectGroup").val() == 0 ) {
                    select_group = 0;
                }else{
                    let select_tmpgroup = $("#ProjectGroup").val().split("|");
                    select_group = select_tmpgroup[1];
                }

                let IsOnlyme = $("#IsOnlyme").val();
                let MyUserID = $("#MyUserID").val();

                if(  IsOnlyme == 1 && MyUserID > 0) {
                    return MyUserID === event.userid;
                }else  if (  select_group == 0 && ( event.type == 10 ||  event.type == 20 ||  event.type == 30 || event.type == 99 )) {
                    return true;
                }else if( select_group != 0   && select_team == 0 ) {
                    return select_group === event.group;
                }else if( select_team > 0 && IsOnlyme == 0 ) {
                    return select_team === event.team;
                }
            },
            eventAllow: function (dropLocation, draggedEvent) {
                let nowday = moment(draggedEvent.dueDate);
                let today = moment(nowday ).format('YYYY-MM-DD');
                let startdays = moment(draggedEvent.start ).format('YYYY-MM-DD');
                let enddays = moment(draggedEvent.end ).format('YYYY-MM-DD');
                let isAdmin = $("#MyRoleID").val();
                let MyUserID = $("#MyUserID").val();

                if (draggedEvent.type == 99 ) {
                    return false;
                }

                if ( MyUserID !== draggedEvent.userid && isAdmin == <?=ROLE_EMPLOYEE?> ) {
                    return false;
                }

                if ( draggedEvent.end == null && today >  startdays) {
                    //alert('현재일보다 작은일자로 수정하실수 없습니다!');
                    return false;
                }
                if ( draggedEvent.end != null && today > enddays) {
                    //alert('현재일보다 작은일자로 수정하실수 없습니다!!');
                    return false;
                }

            },
            eventDragStart: function(event, jsEvent, ui, view){
                let MyUserID = $("#MyUserID").val();
                let isAdmin = $("#MyRoleID").val();
                if ( MyUserID !== event.userid && isAdmin == <?=ROLE_EMPLOYEE?> ) {
                    return false;
                }

                let nowdate = new Date($.now());
                let nowMonth = nowdate.getMonth()+1;
                let nowDays = nowdate.getDate();
                let nowtime = nowdate.getFullYear()+"-"+(nowMonth>9?nowMonth:'0'+nowMonth)+"-"+(nowDays>9?nowDays:'0'+nowDays);
                let textend = moment(event.textend ).format('YYYY-MM-DD');
                if ( textend < nowtime ){
                    alert("오늘 이전 데이터트는 수정이 불가합니다.");
                    return false;
                }
            },
            eventResize: function(event) {
                let restart = moment(event.start ).format('YYYY-MM-DD');
                let reend = moment(event.end ).format('YYYY-MM-DD');
                $.ajax({
                    type        : 'POST' ,
                    async       : true,
                    url         : "/manager/schedule/update",
                    data        : "ScheduleIdx="+event.id+"&sDate="+restart+"&eDate="+reend,
                    dataType    : 'JSON',
                    success  : function(res) {
                        if ( res.result !== true ) {
                            alert('오류가 발생하였습니다');
                            return false;
                        }
                    },
                    error : function (ts) {
                        alert(ts.responseStatus);
                    }
                });

            },
            eventDrop: function(event,delta, revertFunc, jsEvent, ui, view ) {
                let restart = moment(event.start ).format('YYYY-MM-DD');
                let reend = moment(event.end ).format('YYYY-MM-DD');

                let originstart = event.start._i;
                if ( event.end == null) {
                    reend = moment(event.start ).format('YYYY-MM-DD');
                }
                //조건제어
                let nowdate = new Date($.now());
                let nowMonth = nowdate.getMonth()+1;
                let nowdays = nowdate.getDate()+1;
                let nowtime = nowdate.getFullYear()+"-"+(nowMonth>9?nowMonth:'0'+nowMonth)+"-"+(nowdays>9?nowdays:'0'+nowdays);
                if (nowtime > originstart) {
                    revertFunc();
                    return false;
                }
                //alert(d.getDate()+"-"+(d.getMonth() + 1)+"-"+d.getFullYear()+" "+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds());

                $.ajax({
                    type        : 'POST' ,
                    async       : false,
                    url         : "/manager/schedule/update",
                    data        : "ScheduleIdx="+event.id+"&sDate="+restart+"&eDate="+reend,
                    dataType    : 'JSON',
                    success  : function(res) {
                        if ( res.result !== true ) {
                            alert('오류가 발생하였습니다');
                            return false;
                        }
                    },
                    error : function (ts) {
                        alert(ts.responseStatus);
                    }
                });

            },
            eventClick: function(event) {
                if (event.id && event.type !== 99 ) {
                    fn_schedule_info(event.id);
                }
            },
            eventMouseover: function (data, event, view) {
                let restart = moment(data.start ).format('YYYY-MM-DD');
                let reend = moment(data.textend ).format('YYYY-MM-DD');
                let datepattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
                if ( datepattern.test(reend) === false){
                    reend =  restart;
                }
                tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:'+data.backgroundColor+';position:absolute;z-index:10001;padding:5px 10px;  line-height: 140%;">' + data.Comment + '<br />Term : '+ restart +' ~ '+ reend  +'</div>';
                $("body").append(tooltip);
                $(this).mouseover(function (e) {
                    $(this).css('z-index', 10000);
                    $('.tooltiptopicevent').fadeIn('500');
                    $('.tooltiptopicevent').fadeTo('10', 1.9);
                }).mousemove(function (e) {
                    $('.tooltiptopicevent').css('top', e.pageY + 10);
                    $('.tooltiptopicevent').css('left', e.pageX + 20);
                });


            },
            eventMouseout: function (data, event, view) {
                $(this).css('z-index', 8);

                $('.tooltiptopicevent').remove();

            },
            ontentHeight: 'auto',
            editable  : true,
            droppable : true,
            drop      : function (date, allDay) {
                let originalEventObject = $(this).data('eventObject');
                let copiedEventObject = $.extend({}, originalEventObject);

                copiedEventObject.start           = date;
                copiedEventObject.allDay          = allDay;
                copiedEventObject.todouser        = $("#MyName").val();
                copiedEventObject.userid          = $("#MyUserID").val();
                copiedEventObject.team          = $("#MyTeamID").val();
                copiedEventObject.group          = $("#MyGroupID").val();
                copiedEventObject.backgroundColor = $(this).css('background-color');
                copiedEventObject.borderColor     = $(this).css('border-color');
                let restart = moment(copiedEventObject.start ).format('YYYY-MM-DD');
                let hexcode = rgb2hex($(this).css('background-color'));

                let agentmode = "";
                if ( $("#AgentRegist").val()  && ( hexcode == "#cccccc"  || hexcode == "#45a9f4" || hexcode == "#878787"  ) ) {
                    agentmode = $("#AgentRegist").val();
                    if( !confirm("대리등록이 맞습니까?") )
                    {
                        return false;
                    }
                }

                //let isreg =  fn_insert(copiedEventObject.title,copiedEventObject.start);
                if (  copiedEventObject.title ) {
                    $.ajax({
                        type: 'POST',
                        async: false,
                        url: "/manager/schedule/insert",
                        data: "sDate=" + restart + "&eDate=" + restart + "&Type=" + encodeURI(hexcode) + "&SubTitle=" + encodeURI(copiedEventObject.title) + "&agentmode="+ agentmode,
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.result !== true) {
                                alert('오류가 발생하였습니다');
                                $("#AgentRegist").find('option:eq(0)').prop('selected', true);
                                return false;
                            } else {
                                copiedEventObject.id = res.result_idx;
                                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
                                $("#AgentRegist").find('option:eq(0)').prop('selected', true);
                            }
                        },
                        error: function (ts) {
                            alert(ts.responseStatus);
                        }
                    });
                }
            }
            , loading:function(bool) {
                jQuery("#loading").toggle(bool);
            }
        });

        /* ADDING EVENTS */
        var currColor = '#39cccc'; //Red by default
        var colorChooser = $('#color-chooser-btn')
        $('#color-chooser > li > a').click(function (e) {
            e.preventDefault()
            currColor = $(this).css('color')
            $('#add-new-event').css({ 'background-color': currColor, 'border-color': currColor })
        })
        $('#add-new-event').click(function (e) {
            e.preventDefault()
            var val = $('#new-event').val()
            if (val.length == 0) {
                return
            }
            var event = $('<div />')
            event.css({
                'background-color': currColor,
                'border-color'    : currColor,
                'color'           : '#fff'
            }).addClass('external-event')
            event.html(val)
            $('#external-events').prepend(event)

            init_events(event)

            $('#new-event').val('')
        })
    });

</script>
