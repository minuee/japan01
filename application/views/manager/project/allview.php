<!-- fullCalendar -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.print.min.css" media="print">

<div class="content-wrapper" id="page_container">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
           Project 전체 조회
            <small class="w_50 <?=($LoginSession['role'] < ROLE_MANAGER ? '':'display_none')?>"">
                <span class="f_l w_50 <?=($LoginSession['role'] == ROLE_ADMIN  ? '' : 'display_none')?>"  style="max-width:150px !important;">
                    <label for="ProjectGroup" class="hidden">사업부</label>
                    <select class="noh_text_12 w_100" id="ProjectGroup">
                        <option value="0">사업부선택</option>
                        <? foreach( @$BUSINESSCode as $key => $row) { ?>
                            <option value="<?=$row['code']?>" <?=selected($row['code'], $PARENT_GROUP)?>><?=$row['name']?></option>
                        <? }?>
                    </select>
                </span>
                <span class="f_l">
                    <label for="ProjectTeam" class="hidden">팀</label>
                    <select class="noh_text_14 w_100" id="ProjectTeam">
                        <? foreach( $GROUPCode as $key => $row) { ?>
                            <option value="<?=$key?>" <?=selected($key,$GROUPIDX)?>><?=$row?></option>
                        <? }?>
                    </select>
                </span>
            </small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Project`s Detail</a></li>

        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2">
                <div class="col-sm-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h4 class="box-title">범례(진행상태)</h4>
                            <input type="hidden" id="IsMode" value="0">
                            <input type="hidden" id="GROUPIDX" value="<?=$GROUPIDX?>">
                        </div>
                        <div class="box-header with-border">
                            <h4 class="box-title">팀원별</h4>
                            <select class="form-control noh_text_14 w_100" id="AgentRegist">
                                <option value="0">직원선택</option>
                                <? foreach ( $Users as  $key => $val ) {?>
                                    <option value="<?=$val['userId']?>"><?=$val['name']?></option>
                                <? }?>
                            </select>

                        </div>

                        <div class="box-header with-border">
                            <? foreach($CommonCode['WorksStatus'] as $key => $row ) {?>
                                <div class="fc-white <?=$row['class']?> external-event-read is_enable_select" draggable="false" data-code="<?=$key?>"><?=$row['name']?></div>
                            <? } ?>
                        </div>
                        <div class="box-header with-border">
                            <h4 class="box-title">범례(헤더:업무성격)</h4>
                        </div>
                        <div class="box-header with-border">
                            <? foreach($CommonCode['ChildMode'] as $key => $row ) {?>
                                <div class=" external-event fc-white" draggable="false" style="cursor:none;background-color:<?=$row['color']?>"><?=$row['name']?></div>
                            <? } ?>
                        </div>
                        <div class="box-header with-border">
                            <h4 class="box-title">범례(일정)</h4>
                            <input type="hidden" id="IsMode" value="0">
                        </div>
                        <div class="box-header with-border">
                            <? foreach($CommonCode['Schedule'] as $key => $row ) {
                                if ( $row['mode'] > 2 ) continue;
                                ?>
                                <div class="fc-white external-event-read2"  style="background-color:<?=$row['color']?>;"><?=$row['name']?></div>
                            <? } ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 schedule_help_wrapper">
                    <label for="ProjectTeam" >참고</label>
                    <ul>
                        <li>※ 단순조회용</li>
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

        //build download link:
        a.href = "data:" + strMimeType + "," + escape(strData);

        if (window.MSBlobBuilder) {
            var bb = new MSBlobBuilder();
            bb.append(strData);
            return navigator.msSaveBlob(bb, strFileName);
        } /* end if(window.MSBlobBuilder) */

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
        } /* end if('download' in a) */

        //do iframe dataURL download:
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
    $(function () {

        $(document).off('change', '#ProjectTeam').on('change', '#ProjectTeam',function() {

            let idx = $(this).val();
            let gidx = $("#ProjectGroup").val();
            if ( idx > 0 ) {
                location.href='/manager/project/allview/' + idx +'/' + gidx;
            }
            return false;
        });



        $(document).on("change", "#ProjectGroup", function(){
            //$("#AgentRegist").find("option:eq(0)").prop("selected", true);
            $("#AgentRegist").html("<option value='0'>직원선택</option>");
            // 불러온다
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/manager/project/getSubTeam",
                data: "GroupCode=" + $(this).val(),
                async: false,
                success: function (json) {
                    if (json.totalCount > 0 ) {
                        $("#ProjectTeam").html('');

                        let html = "<option value='0'>팀선택</option>";
                        $("#ProjectTeam").append(html);
                        for(var i = 0; i < json.dataList.length; i++) {
                            html = "<option value='"+json.dataList[i]['IDX']+"'>"+json.dataList[i]['NAME']+"</option>";
                            $("#ProjectTeam").append(html);
                        }

                        return false;
                    }

                }
            });

        });

        $(document).on("change", "#AgentRegist", function () {
            $('#calendar').fullCalendar('rerenderEvents');
        });

        function removeitem( idx = null) {
            if ( idx ) {
                $("#calendar").fullCalendar('removeEvents', idx);
            }
        }

        function fn_info(idx) {
            $("#popdetail").setLayer({
                'url' : '/manager/project/popdetailread/' + idx,
                'width' : 1024,
                'max_height' : 500
            });
        }

        $(document).on("click", ".is_enable_select", function () {

            let thiscode = $(this).data("code");
            $(this).toggleClass("on");
            let checkcnt = 0;
            $('.is_enable_select').each(function (index) {
                if ( $(this).hasClass("on")) {
                    $("#IsMode").val($(this).data("code"));
                    checkcnt++;
                }
                if ( $(this).data("code") !== thiscode ) {
                    $(this).removeClass("on");
                }
            });

            if ( checkcnt == 0 ) $("#IsMode").val(0);
            $('#calendar').fullCalendar('rerenderEvents');
            return false;
        });

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
                        $.ajax({
                            url: '/manager/project/get_events_all',
                            dataType: 'json',
                            beforeSend : function() {
                                $('.wrap-loading').removeClass('display_none');
                            },
                            data: {
                                start: restart,
                                end: reend,
                                gidx: $("#GROUPIDX").val()
                            },
                            success: function(res) {
                                let events = res.events;
                                callback(events);
                                $('.wrap-loading').addClass('display_none');
                            }
                        });
                    }
                }
            ]
            ,eventRender: function(event, eventElement) {
                let select_user = $("#AgentRegist").val();
                eventElement.find(".fc-title").html(eventElement.find('.fc-title').text());

                if ( $("#IsMode").val() > 0  && select_user == 0 ) {
                    return $("#IsMode").val() == event.wstatus;
                }else if ( $("#IsMode").val() > 0  && select_user > 0 ) {
                    return ( $("#IsMode").val() == event.wstatus && select_user == event.todouserid );
                }else if( select_user > 0 ) {
                    return select_user == event.todouserid;
                }

            },
            eventClick: function(event) {
                if (event.id && event.type == 1 ) {
                    fn_info(event.id);
                }
            },
            eventMouseover: function (data, event, view) {
                let restart = moment(data.start ).format('YYYY-MM-DD');
                let reend = moment(data.textend ).format('YYYY-MM-DD');
                tooltip = '<div class="tooltiptopicevent" style="width:auto;height:auto;background:'+data.backgroundColor+';position:absolute;z-index:10001;padding:5px 10px; line-height: 140%;">' + data.title + '<br />Term : '+ restart +' ~ '+ reend + data.textrate +'</div>';
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
            editable  : false,
            droppable : false
        });

    })
</script>