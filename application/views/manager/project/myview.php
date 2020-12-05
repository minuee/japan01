<!-- fullCalendar -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/plugins/fullcalendar/dist/fullcalendar.print.min.css" media="print">

<div class="content-wrapper" id="page_container">

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>My Project Work View</h1>
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
<script>
    $(function () {
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
            header    : {
                left  : 'prev,next today',
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
                            url: '/manager/project/get_events_my',
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
                                console.log("events",events);
                                callback(events);
                                $('.wrap-loading').addClass('display_none');
                            }
                        });
                    }
                },
            ]
            ,eventRender: function(event, eventElement) {
                eventElement.find(".fc-title").html(eventElement.find('.fc-title').text());

                if ( $("#IsMode").val() > 0 ) {
                    return $("#IsMode").val() == event.wstatus ;
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