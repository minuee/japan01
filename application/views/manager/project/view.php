<!-- fullCalendar -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.print.min.css" media="print">

<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/chat.css?v=<?=time()?>" rel="stylesheet">
<link type="text/css" href="<?php echo base_url(); ?>assets/node/css/jquery.mCustomScrollbar.css" rel="stylesheet">


<div class="content-wrapper" id="page_container">
    <input type="hidden" id="GlobalProjectIdx" value="<?=$ProjectIdx?>" >
    <input type="hidden" id="UID" value="<?=$LoginSession['userId']?>" >
    <input type="hidden" id="MyName" value="<?=$LoginSession['name']?>">
    <input type="hidden" id="MyGroupID" value="<?=$LoginSession['groupidx']?>">
    <input type="hidden" id="MyRoleID" value="<?=$LoginSession['role']?>">
    <input type="hidden" id="lastMessanger" value="">
    <input type="hidden" id="NowNoticeIdx" value="<?=@$NoticeMessages['NoticeIdx']?>" >
    <div class="h_con ChatUserLost_Wrapper <?=(!$IsChat? 'display_none':'')?>" id="hidden_con">
        <ul id="hidden_join_list">
            <li>입장현황</li>
        </ul>
    </div>

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
                        <h4 class="box-title">범례(진행상태)</h4>
                    </div>
                    <div class="box-header with-border">
                        <? foreach($CommonCode['WorksStatus'] as $key => $row ) {?>
                            <div class=" external-event <?=$row['class']?> fc-white" draggable="false" style="cursor:none"><?=$row['name']?></div>
                        <? } ?>
                    </div>
                    <div class="box-body">
                        <!-- the events -->
                        <div id="external-events">
                            <!--<div class="checkbox">
                                <label for="drop-remove">
                                    <input type="checkbox" id="drop-remove">
                                    remove after drop
                                </label>
                            </div>-->
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /. box -->
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">ToDo 생성</h3>
                    </div>
                    <div class="box-body">
                        <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                            <ul class="fc-color-picker" id="color-chooser">
                                <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                            </ul>
                        </div>
                        <!-- /btn-group -->
                        <div class="input-group">
                            <input id="new-event" type="text" class="form-control" placeholder="업무명 입력">

                            <div class="input-group-btn">
                                <span id="add-new-event" type="button" class="btn btn-primary btn-flat bg-todo">생성</span>
                            </div>
                            <!-- /btn-group -->
                        </div>
                        <!-- /input-group -->
                    </div>
                </div>
                <div class="box box-solid">
                    <div class="box-header with-border">
                        <h4 class="box-title">범례(헤더:업무성격)</h4>
                    </div>
                    <div class="box-header with-border">
                        <? foreach($CommonCode['ChildMode'] as $key => $row ) {?>
                            <div class=" external-event fc-white" draggable="false" style="cursor:none;background-color:<?=$row['color']?>"><?=$row['name']?></div>
                        <? } ?>
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
<script src="<?php echo base_url(); ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo base_url(); ?>assets/plugins/morris/fastclick.js"></script>
<!-- fullCalendar -->
<script src="<?php echo base_url(); ?>assets/plugins/moment/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/html2canvas.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jspdf.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jspdf_addimage.js"></script>
<script>

    function pageprint(event)
    {
        html2canvas($('#calendar'), {
            logging: true,
            useCORS: true,
            background :'#FFFFFF',
            onrendered: function (canvas) {
                var imgData = canvas.toDataURL("image/jpeg");
                var doc = new jsPDF();
                doc.addImage(imgData, 'JPEG', 15, 40, 180, 160);
                download(doc.output(), "Project.pdf", "text/pdf");
            }
        }) ;
    }

    function download(strData, strFileName, strMimeType)
    {
        var D = document,
            A = arguments,
            a = D.createElement("a"),
            d = A[0],
            n = A[1],
            t = A[2] || "text/plain";


        a.href = "data:" + strMimeType + "," + escape(strData);

        if (window.MSBlobBuilder) {
            var bb = new MSBlobBuilder();
            bb.append(strData);
            return navigator.msSaveBlob(bb, strFileName);
        }

        if ('download' in a) {
            a.setAttribute("download", n);
            a.innerHTML = "downloading...";
            D.body.appendChild(a);
            setTimeout(function() {
                var e = D.createEvent("MouseEvents");
                e.initMouseEvent("click", true, false, window, 0, 0, 0, 0, 0, false, false,
                    false, false, 0, null);
                a.dispatchEvent(e);
                D.body.removeChild(a);
            }, 66);
            return true;
        }


        var f = D.createElement("iframe");
        D.body.appendChild(f);
        f.src = "data:" + (A[2] ? A[2] : "application/octet-stream") + (window.btoa ? ";base64"
            : "") + "," + (window.btoa ? window.btoa : escape)(strData);
        setTimeout(function() {
            D.body.removeChild(f);
        }, 333);
        return true;
    }
</script>
<script>

    function removeitem(idx) {
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

    function fn_info(idx) {
        $("#popdetail").setLayer({
            'url' : '/manager/project/popdetail2/' + idx,
            'width' : 1024,
            'max_height' : 500
        });
    }

    function isValidDate(s) {
        var bits = s.split('-');
        var d = new Date(bits[2] + '-' + bits[1] + '-' + bits[0]);
        return !!(d && (d.getMonth() + 1) == bits[1] && d.getDate() == Number(bits[0]));
    }
    $(function () {

        init_events($('#external-events div.external-event'));

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
                right : 'month,basicWeek,basicDay'
            },
            buttonText: {
                today: '오늘',month: '월',week : '주',day  : '일'
            },
            firstDay: 0,				//---	0. 일요일
            weekends: true,
            allDaySlot: true,
            allDayText: '종일',
            monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'] ,
            monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            dayNames: ['일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'],
            dayNamesShort: ['일', '월', '화', '수', '목', '금', '토']
            ,eventSources: [
                {
                    events: function(start, end, timezone, callback) {
                        let restart = moment(start).format('YYYY-MM-DD');
                        let reend = moment(end).format('YYYY-MM-DD');
                        let pidx = $("#GlobalProjectIdx").val();
                        $.ajax({
                            url: '/manager/project/get_events',
                            dataType: 'json',
                            beforeSend : function() {
                                $('.wrap-loading').removeClass('display_none');
                            },
                            data: {
                                ProjectIdx: pidx,
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
                eventElement.find(".fc-title").html(eventElement.find('.fc-title').text());
            },
            eventAllow: function (dropLocation, draggedEvent) {
                let nowday = moment(draggedEvent.dueDate);
                let today = moment(nowday ).format('YYYY-MM-DD');
                let startdays = moment(draggedEvent.start ).format('YYYY-MM-DD');
                let enddays = moment(draggedEvent.end ).format('YYYY-MM-DD');
                let isAdmin = $("#MyRoleID").val();
                let MyUserID = $("#UID").val();

                if ( draggedEvent.wstatus == 9 ) {
                    return false;
                }

                if ( MyUserID !== draggedEvent.userid && isAdmin == <?=ROLE_EMPLOYEE?> ) {
                    return false;
                }

                if ( draggedEvent.end == null && today >  startdays) {
                    return false;
                }
                if ( draggedEvent.end != null && today > enddays) {
                    return false;
                }
            },
            eventDragStart: function(event, jsEvent, ui, view){

                let MyUserID = $("#UID").val();
                let isAdmin = $("#MyRoleID").val();
                if ( MyUserID !== event.userid && isAdmin == <?=ROLE_EMPLOYEE?> ) {
                    return false;
                }

                let nowdate = new Date($.now());
                let nowMonth = nowdate.getMonth()+1;
                let nowDays = nowdate.getDate();
                let nowtime = nowdate.getFullYear()+"-"+(nowMonth>9?nowMonth:'0'+nowMonth)+"-"+(nowDays>9?nowDays:'0'+nowDays);
                let textstart = moment(event.start._i).format('YYYY-MM-DD');
                if ( textstart < nowtime ){
                    alert("오늘 이전 데이터는 수정이 불가합니다.");
                    return false;
                }
            },
            eventResize: function(event) {
                let restart = moment(event.start ).format('YYYY-MM-DD');
                let reend = moment(event.end ).format('YYYY-MM-DD');

                $.ajax({
                    type        : 'POST' ,
                    async       : false,
                    url         : "/manager/project/workdateupdate",
                    data        : "ProjectWorkIdx="+event.id+"&sDate="+restart+"&eDate="+reend,
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
            eventDrop: function(event) {
                let datepattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
                let restart = moment(event.start ).format('YYYY-MM-DD');
                let reend = moment(event.end ).format('YYYY-MM-DD');
                if ( datepattern.test(reend) === false){
                    reend =  restart;
                }

                //alert(restart);
                //alert(reend);
                $.ajax({
                    type        : 'POST' ,
                    async       : false,
                    url         : "/manager/project/workdateupdate",
                    data        : "ProjectWorkIdx="+event.id+"&sDate="+restart+"&eDate="+reend,
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
                if (event.id) {
                    fn_info(event.id);
                }
            },
            eventMouseover: function (data, event, view) {
                let restart = moment(data.start ).format('YYYY-MM-DD');
                let reend = moment(data.textend ).format('YYYY-MM-DD');
                let datepattern = /[0-9]{4}-[0-9]{2}-[0-9]{2}/;
                if ( datepattern.test(reend) === false){
                    reend =  restart;
                }
                tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:'+data.backgroundColor+';position:absolute;z-index:10001;padding:5px 10px;line-height: 140%;">' + data.title + '<br />Term : '+ restart +' ~ '+ reend  + data.textrate +'</div>';
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
            editable  : true,
            droppable : true,
            drop      : function (date, allDay) {
                let nowdate = new Date($.now());
                let nowMonth = nowdate.getMonth()+1;
                let nowDays = nowdate.getDate();
                let nowtime = nowdate.getFullYear()+"-"+(nowMonth>9?nowMonth:'0'+nowMonth)+"-"+(nowDays>9?nowDays:'0'+nowDays);

                let originalEventObject = $(this).data('eventObject');
                let copiedEventObject = $.extend({}, originalEventObject);

                copiedEventObject.start           = date;
                copiedEventObject.allDay          = allDay;
                copiedEventObject.backgroundColor = $(this).css('background-color');
                copiedEventObject.borderColor     = $(this).css('border-color');
                console.log("copiedEventObject2",copiedEventObject);
                let restart = moment(copiedEventObject.start ).format('YYYY-MM-DD');
                if ( restart < nowtime ){
                    alert("오늘 이전 일자로는 등록이 불가합니다.");
                    return false;
                }

                //let isreg =  fn_insert(copiedEventObject.title,copiedEventObject.start);
                if (  copiedEventObject.title ) {
                    $.ajax({
                        type: 'POST',
                        async: false,
                        url: "/manager/project/workinsert",
                        data: "ProjectIdx=" + $("#GlobalProjectIdx").val() + "&sDate=" + restart + "&eDate=" + restart + "&title=" + encodeURI(copiedEventObject.title),
                        dataType: 'JSON',
                        success: function (res) {
                            if (res.result !== true) {
                                alert('오류가 발생하였습니다');
                                return false;
                            } else {
                                copiedEventObject.id = res.result_idx;
                                copiedEventObject.textend = restart;
                                copiedEventObject.textrate = 0;
                              /*  if ($('#drop-remove').is(':checked')) {
                                    //$(this).remove();
                                }*/
                                $('#calendar').fullCalendar('renderEvent', copiedEventObject, true);
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
        var currColor = '#605ca8' //Red by default
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